<?php

namespace App\Models;

use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ledger extends Model
{
    use HasUuids;

    /** Ledger entries are immutable audit records — no updated_at */
    public $timestamps = false;

    const CREATED_AT = 'created_at';

    protected $fillable = [
        'transaction_type',
        'account_id',
        'category_id',
        'reference_id',
        'amount',
        'is_mutation_in',
        'contact_name',
        'transaction_date',
        'note',
        'photo_path',
    ];

    protected function casts(): array
    {
        return [
            'transaction_type' => TransactionType::class,
            'is_mutation_in'   => 'boolean',
            'transaction_date' => 'datetime',
            'created_at'       => 'datetime',
            'amount'           => 'float',
        ];
    }

    /** The account this ledger entry belongs to */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /** The category (only populated for INCOME/EXPENSE types) */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /** Human-readable amount with sign */
    public function getFormattedAmountAttribute(): string
    {
        $sign = $this->is_mutation_in ? '+' : '-';

        return $sign.' Rp '.number_format($this->amount, 0, ',', '.');
    }
}
