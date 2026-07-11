<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Credentials: admin@finarika.com / finarika123
     */
    public function run(): void
    {
        // ── Hardcoded single user ─────────────────────────────────────────────
        User::firstOrCreate(
            ['email' => 'admin@finarika.com'],
            [
                'name'              => 'Admin FinArka',
                'password'          => Hash::make('finarika123'),
                'email_verified_at' => now(),
            ]
        );

        // ── Default Accounts ─────────────────────────────────────────────────
        $defaultAccounts = [
            ['name' => 'Cash',      'initial_balance' => 0],
            ['name' => 'BCA',       'initial_balance' => 0],
            ['name' => 'Jago',      'initial_balance' => 0],
            ['name' => 'GoPay',     'initial_balance' => 0],
            ['name' => 'ShopeePay', 'initial_balance' => 0],
        ];

        foreach ($defaultAccounts as $acc) {
            Account::firstOrCreate(['name' => $acc['name']], $acc);
        }

        // ── Default Income Categories ─────────────────────────────────────────
        $incomeCategories = [
            'Gaji', 'Bonus', 'Investasi', 'Freelance',
            'Hadiah', 'Penjualan', 'Lainnya (Pemasukan)',
        ];

        foreach ($incomeCategories as $name) {
            Category::firstOrCreate(['name' => $name, 'type' => 'INCOME']);
        }

        // ── Default Expense Categories ────────────────────────────────────────
        $expenseCategories = [
            'Makanan', 'Minuman', 'Transportasi', 'Listrik',
            'Air', 'Internet', 'Pulsa', 'Belanja', 'Hiburan',
            'Kesehatan', 'Pendidikan', 'Sewa', 'Asuransi',
            'Perawatan', 'Lainnya (Pengeluaran)',
        ];

        foreach ($expenseCategories as $name) {
            Category::firstOrCreate(['name' => $name, 'type' => 'EXPENSE']);
        }
    }
}
