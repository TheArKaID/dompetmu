<?php

namespace App\Enums;

enum CategoryType: string
{
    case INCOME  = 'INCOME';
    case EXPENSE = 'EXPENSE';

    public function label(): string
    {
        return match ($this) {
            self::INCOME  => 'Pemasukan',
            self::EXPENSE => 'Pengeluaran',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::INCOME  => 'emerald',
            self::EXPENSE => 'rose',
        };
    }
}
