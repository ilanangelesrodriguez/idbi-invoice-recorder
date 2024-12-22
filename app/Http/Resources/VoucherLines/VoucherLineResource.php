<?php

namespace App\Http\Resources\VoucherLines;

use App\Http\Resources\Vouchers\VoucherResource;
use App\Models\VoucherLine;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VoucherLineResource extends JsonResource
{
    /**
     * @var VoucherLine
     */
    public $resource;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'quantity' => $this->resource->quantity,
            'unit_price' => $this->resource->unit_price,
            'voucher' => $this->whenLoaded(
                'voucher',
                fn () => VoucherResource::make($this->resource->voucher),
            ),
        ];
    }
}
