<?php

namespace App\Console\Commands;

use App\Models\Voucher;
use Illuminate\Console\Command;

class UpdateVoucherFields extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'voucher:update-fields';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Procesa registros existentes de vouchers y actualiza nuevos campos.';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Obtén todos los registros existentes
        $vouchers = Voucher::all();

        foreach ($vouchers as $voucher) {
            // Verifica si el contenido del XML no está vacío
            if (!empty($voucher->xml_content)) {
                // Parsea el contenido del XML
                $xml = @simplexml_load_string($voucher->xml_content);

                if ($xml) {
                    try {
                        // Actualiza los nuevos campos
                        $voucher->update([
                            'serie' => (string) $xml->Serie ?? null,
                            'numero' => (string) $xml->Numero ?? null,
                            'tipo_comprobante' => (string) $xml->TipoComprobante ?? null,
                            'moneda' => (string) $xml->Moneda ?? null,
                        ]);

                        $this->info("Voucher ID {$voucher->id} actualizado correctamente.");
                    } catch (\Exception $e) {
                        $this->error("Error al procesar Voucher ID {$voucher->id}: {$e->getMessage()}");
                    }
                } else {
                    $this->error("Error al procesar el campo xml_content para Voucher ID {$voucher->id}.");
                }
            } else {
                $this->error("El campo xml_content está vacío para Voucher ID {$voucher->id}.");
            }
        }

        $this->info('Todos los vouchers han sido procesados.');
        return 0;
    }
}
