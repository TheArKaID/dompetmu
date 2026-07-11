<?php

namespace App\Models;

use App\Enums\CategoryType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasUuids;

    /** No updated_at column in this table */
    public $timestamps = false;

    const CREATED_AT = 'created_at';

    protected $fillable = ['name', 'type'];

    protected function casts(): array
    {
        return [
            'type'       => CategoryType::class,
            'created_at' => 'datetime',
        ];
    }

    public function ledgers(): HasMany
    {
        return $this->hasMany(Ledger::class);
    }
}
