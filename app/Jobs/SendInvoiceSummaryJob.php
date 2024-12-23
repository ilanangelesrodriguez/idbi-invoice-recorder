<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Voucher;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendInvoiceSummaryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected User $user;

    /**
     * Create a new job instance.
     *
     * @param $user User que recibirá el resumen (correo electrónico).
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Generate the summary email and send it.
     */
    public function handle(): void
    {
        // Obtener los comprobantes procesados
        $successfullyProcessed = Voucher::whereNotNull('serie')
            ->whereNull('error_message')
            ->get();

        $failed = Voucher::whereNotNull('error_message')->get();

        // Enviar el correo al usuario con los detalles
        Mail::to($this->user->email)->send(new InvoiceSummaryMail($successfullyProcessed, $failed));
    }
}
