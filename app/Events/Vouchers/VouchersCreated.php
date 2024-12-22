<?php

namespace App\Events\Vouchers;

use App\Models\User;
use App\Models\Voucher;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VouchersCreated
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * @param Voucher[] $vouchers
     * @param User $user
     */
    public function __construct(
        public readonly array $vouchers,
        public readonly User $user
    ) {
    }
}
