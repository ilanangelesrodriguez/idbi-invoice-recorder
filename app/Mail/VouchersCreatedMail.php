<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VouchersCreatedMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public array $vouchers;
    public User $user;

    public function __construct(array $vouchers, User $user)
    {
        $this->vouchers = $vouchers;
        $this->user = $user;
    }

    public function build(): self
    {
        return $this->view('emails.vouchers')
            ->subject('Subida de comprobantes')
            ->with(['vouchers' => $this->vouchers, 'user' => $this->user]);
    }
}
