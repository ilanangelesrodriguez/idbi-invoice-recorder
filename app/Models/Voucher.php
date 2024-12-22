<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * @property string $id
 * @property string $issuer_name
 * @property string $issuer_document_type
 * @property string $issuer_document_number
 * @property string $receiver_name
 * @property string $receiver_document_type
 * @property string $receiver_document_number
 * @property float $total_amount
 * @property string $xml_content
 * @property string $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read User $user
 * @property-read Collection|User[] $lines
 * @mixin Builder
 */
class Voucher extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    protected $fillable = [
        'issuer_name',
        'issuer_document_type',
        'issuer_document_number',
        'receiver_name',
        'receiver_document_type',
        'receiver_document_number',
        'total_amount',
        'xml_content',
        'user_id',
    ];

    protected $casts = [
        'total_amount' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(VoucherLine::class);
    }
}
