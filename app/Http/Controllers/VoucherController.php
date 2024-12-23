<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessInvoiceJob;
use App\Models\Voucher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
}
