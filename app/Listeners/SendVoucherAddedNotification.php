<?php

namespace App\Listeners;

use App\Events\Vouchers\VouchersCreated;
use App\Mail\VouchersCreatedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendVoucherAddedNotification implements ShouldQueue
{
    public function handle(VouchersCreated $event): void
    {
        $mail = new VouchersCreatedMail($event->vouchers, $event->user);
        Mail::to($event->user->email)->send($mail);
    }
}
