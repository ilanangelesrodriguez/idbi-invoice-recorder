<?php

namespace App\Http\Controllers\Vouchers;

use App\Http\Resources\Vouchers\VoucherResource;
use App\Models\Voucher;
use App\Services\VoucherService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class StoreVouchersHandler
{
    public function __construct(private readonly VoucherService $voucherService)
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            // Validar que se reciba `xml_content` requerido y como string
            $request->validate([
                'xml_content' => 'required|string'
            ]);

            // Extraer el contenido del XML
            $xmlContent = $request->input('xml_content');

            // Parsear el contenido XML y continuar con la lógica del proceso
            $xml = simplexml_load_string($xmlContent);
            $serie = (string) $xml->Serie ?? null;
            $numero = (string) $xml->Numero ?? null;
            $tipoComprobante = (string) $xml->TipoComprobante ?? null;
            $moneda = (string) $xml->Moneda ?? null;

            // Crear el registro de Voucher con los nuevos campos
            $voucher = Voucher::create([
                'xml_content' => $xmlContent,
                'serie' => $serie,
                'numero' => $numero,
                'tipo_comprobante' => $tipoComprobante,
                'moneda' => $moneda,
            ]);

            return response()->json($voucher, 201);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
