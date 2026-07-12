<?php

namespace App\Enums;

enum TransactionType: string
{
    case INCOME       = 'INCOME';
    case EXPENSE      = 'EXPENSE';
    case DEBT         = 'DEBT';
    case DEBT_PAYMENT = 'DEBT_PAYMENT';
    case LOAN         = 'LOAN';
    case LOAN_PAYMENT = 'LOAN_PAYMENT';
    case TRANSFER_OUT = 'TRANSFER_OUT';
    case TRANSFER_IN  = 'TRANSFER_IN';
    case ADMIN_FEE    = 'ADMIN_FEE';

    /** Indonesian display label */
    public function label(): string
    {
        return match ($this) {
            self::INCOME       => 'Pemasukan',
            self::EXPENSE      => 'Pengeluaran',
            self::DEBT         => 'Hutang',
            self::DEBT_PAYMENT => 'Bayar Hutang',
            self::LOAN         => 'Beri Pinjaman',
            self::LOAN_PAYMENT => 'Terima Bayar',
            self::TRANSFER_OUT => 'Transfer Keluar',
            self::TRANSFER_IN  => 'Transfer Masuk',
            self::ADMIN_FEE    => 'Biaya Admin',
        };
    }

    /** Badge color variant */
    public function color(): string
    {
        return match ($this) {
            self::INCOME, self::DEBT, self::LOAN_PAYMENT, self::TRANSFER_IN => 'emerald',
            self::EXPENSE, self::DEBT_PAYMENT, self::LOAN, self::ADMIN_FEE  => 'rose',
            self::TRANSFER_OUT                                               => 'indigo',
        };
    }

    /** TRUE = money enters the account; FALSE = money leaves */
    public function isMutationIn(): bool
    {
        return match ($this) {
            self::INCOME, self::DEBT, self::LOAN_PAYMENT, self::TRANSFER_IN => true,
            default => false,
        };
    }

    /** Whether a category selection is required for this type */
    public function requiresCategory(): bool
    {
        return in_array($this, [self::INCOME, self::EXPENSE], true);
    }

    /** Whether a contact name is required for this type */
    public function requiresContact(): bool
    {
        return in_array($this, [
            self::DEBT,
            self::DEBT_PAYMENT,
            self::LOAN,
            self::LOAN_PAYMENT,
        ], true);
    }

    /** The CategoryType this transaction type is associated with (null for non-category types) */
    public function categoryType(): ?CategoryType
    {
        return match ($this) {
            self::INCOME => CategoryType::INCOME,
            self::EXPENSE => CategoryType::EXPENSE,
            default => null,
        };
    }

    /** Types that users can pick manually in the standard create form */
    public static function userSelectableTypes(): array
    {
        return [
            self::INCOME,
            self::EXPENSE,
            self::DEBT,
            self::DEBT_PAYMENT,
            self::LOAN,
            self::LOAN_PAYMENT,
        ];
    }
}
