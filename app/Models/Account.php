<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    use HasUuids;

    protected $fillable = ['name', 'initial_balance'];

    protected function casts(): array
    {
        return [
            'initial_balance' => 'float',
        ];
    }

    /** All ledger entries originating from or targeting this account */
    public function ledgers(): HasMany
    {
        return $this->hasMany(Ledger::class);
    }

    /**
     * Computed current balance:
     *   initial_balance + SUM(credits) - SUM(debits)
     */
    public function getBalanceAttribute(): float
    {
        $net = $this->ledgers()
            ->selectRaw(
                'COALESCE(SUM(CASE WHEN is_mutation_in = 1 THEN amount ELSE -amount END), 0) as net'
            )
            ->value('net') ?? 0;

        return (float) $this->initial_balance + (float) $net;
    }
}
