<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessInvoiceJob;
use App\Models\Voucher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoucherController extends Controller
{
    /**
     * Registra los comprobantes en la cola para su procesamiento.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function registerInvoices(Request $request): JsonResponse
    {
        foreach ($request->vouchers as $voucherId) {
            // Obtener el comprobante
            $voucher = Voucher::find($voucherId);

            if ($voucher) {
                // Encolar el Job para que se procese en segundo plano
                ProcessInvoiceJob::dispatch($voucher);
            }
        }

        return response()->json([
            'message' => 'Los comprobantes están siendo procesados. Recibirás un resumen por correo.',
        ], 202); // Código HTTP 202 para procesamiento en curso.
    }

    public function getTotalAmountsByCurrency(): JsonResponse
    {
        // Obtener el usuario autenticado
        $user = Auth::user();

        // Consultar los totales acumulados agrupados por tipo de moneda
        $totals = \App\Models\Voucher::selectRaw('moneda, SUM(amount) as total_amount')
            ->where('user_id', $user->id) // Filtrar por el usuario autenticado
            ->groupBy('moneda')           // Agrupar por la moneda
            ->get();

        // Formatear los datos para la respuesta
        $response = [
            'totals' => $totals->pluck('total_amount', 'moneda') // Monedas como clave, totales como valor
        ];

        return response()->json($response, 200); // Respuesta con código HTTP 200
    }

    public function deleteVoucher($id): JsonResponse
    {
        // Obtener el usuario autenticado
        $user = Auth::user();

        // Buscar el comprobante por ID y validar que pertenece al usuario
        $voucher = Voucher::where('id', $id)->where('user_id', $user->id)->first();

        if (!$voucher) {
            // Responder con un error si el comprobante no existe o no pertenece al usuario
            return response()->json([
                'message' => 'Comprobante no encontrado o no autorizado para eliminarlo.'
            ], 404);
        }

        try {
            // Eliminar el comprobante (Soft Delete si usas "SoftDeletes", Hard Delete si no)
            $voucher->delete();

            // Responder con éxito
            return response()->json([
                'message' => 'Comprobante eliminado exitosamente.'
            ], 200);
        } catch (\Exception $e) {
            // Manejar errores inesperados
            return response()->json([
                'message' => 'Ocurrió un error al intentar eliminar el comprobante.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function listVouchers(Request $request): JsonResponse
    {
        // Obtener el usuario autenticado
        $user = Auth::user();

        // Validar los filtros enviados en la solicitud
        $validated = $request->validate([
            'serie' => 'nullable|string',                // Filtro por serie
            'numero' => 'nullable|string',              // Filtro por número
            'tipo_comprobante' => 'nullable|string',    // Filtro por tipo de comprobante
            'moneda' => 'nullable|string|in:PEN,USD',   // Filtro por moneda (valores válidos: PEN, USD)
            'fecha_inicio' => 'required|date',          // Fecha de inicio (OBLIGATORIO)
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio', // Fecha de fin (OBLIGATORIO)
        ]);

        // Construir la consulta usando Eloquent
        $vouchersQuery = \App\Models\Voucher::query()
            ->where('user_id', $user->id) // Solo comprobantes del usuario autenticado
            ->whereBetween('created_at', [$validated['fecha_inicio'], $validated['fecha_fin']]); // Rango obligatorio

        // Aplicar los demás filtros, solo si se proporcionan
        if (!empty($validated['serie'])) {
            $vouchersQuery->where('serie', $validated['serie']);
        }

        if (!empty($validated['numero'])) {
            $vouchersQuery->where('numero', $validated['numero']);
        }

        if (!empty($validated['tipo_comprobante'])) {
            $vouchersQuery->where('tipo_comprobante', $validated['tipo_comprobante']);
        }

        if (!empty($validated['moneda'])) {
            $vouchersQuery->where('moneda', $validated['moneda']);
        }

        // Obtener los resultados
        $vouchers = $vouchersQuery->get();

        // Responder con los comprobantes encontrados
        return response()->json([
            'message' => 'Listado de comprobantes recuperado exitosamente.',
            'data' => $vouchers,
        ], 200);
    }

}
