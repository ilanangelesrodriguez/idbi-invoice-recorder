<?php

namespace App\Jobs;

use App\Models\Voucher;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessInvoiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $voucher;

    /**
     * Create a new job instance.
     *
     * @param Voucher $voucher
     */
    public function __construct(Voucher $voucher)
    {
        $this->voucher = $voucher; // Recibe un comprobante para procesar.
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        try {
            // Procesar el XML del comprobante para extraer datos clave
            $xml = simplexml_load_string($this->voucher->xml_content, null, LIBXML_NOCDATA);

            if ($xml) {
                // Registrar namespaces necesarios
                $namespaces = $xml->getNamespaces(true);
                $xml->registerXPathNamespace('cbc', $namespaces['cbc']);

                // Extraer valores del XML
                $serieNumero = (string) $xml->xpath('cbc:ID')[0];
                $tipoComprobante = (string) $xml->xpath('cbc:InvoiceTypeCode')[0];
                $moneda = (string) $xml->xpath('cbc:DocumentCurrencyCode')[0];

                // Dividir serie y número
                [$serie, $numero] = explode('-', $serieNumero);

                // Actualizar el comprobante procesado
                $this->voucher->update([
                    'serie' => $serie,
                    'numero' => $numero,
                    'tipo_comprobante' => $tipoComprobante,
                    'moneda' => $moneda,
                ]);

                Log::info("Comprobante ID {$this->voucher->id} procesado correctamente.");
            } else {
                throw new \Exception("XML inválido.");
            }
        } catch (\Exception $e) {
            // Registrar el fallo y marcarlo como error en la base de datos
            Log::error("Error procesando comprobante ID {$this->voucher->id}: {$e->getMessage()}");
            $this->voucher->update([
                'error_message' => $e->getMessage(),
            ]);
        }
    }
}
