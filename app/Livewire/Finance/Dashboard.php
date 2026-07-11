<?php

namespace App\Livewire\Finance;

use App\Enums\TransactionType;
use App\Models\Account;
use App\Models\Ledger;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Dashboard Keuangan — FinArka')]
class Dashboard extends Component
{
    public string $selectedMonth;

    public function mount(): void
    {
        $this->selectedMonth = now()->format('Y-m');
    }

    #[Computed]
    public function accounts(): \Illuminate\Database\Eloquent\Collection
    {
        return Account::all();
    }

    #[Computed]
    public function totalBalance(): float
    {
        return $this->accounts->sum('balance');
    }

    #[Computed]
    public function monthlyIncome(): float
    {
        [$year, $month] = explode('-', $this->selectedMonth);

        return (float) Ledger::where('is_mutation_in', true)
            ->whereIn('transaction_type', [
                TransactionType::INCOME->value,
                TransactionType::DEBT->value,
                TransactionType::LOAN_PAYMENT->value,
            ])
            ->whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month)
            ->sum('amount');
    }

    #[Computed]
    public function monthlyExpense(): float
    {
        [$year, $month] = explode('-', $this->selectedMonth);

        return (float) Ledger::where('is_mutation_in', false)
            ->whereIn('transaction_type', [
                TransactionType::EXPENSE->value,
                TransactionType::DEBT_PAYMENT->value,
                TransactionType::LOAN->value,
            ])
            ->whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month)
            ->sum('amount');
    }

    #[Computed]
    public function netCashflow(): float
    {
        return $this->monthlyIncome - $this->monthlyExpense;
    }

    #[Computed]
    public function recentTransactions()
    {
        return Ledger::with(['account', 'category'])
            ->orderByDesc('transaction_date')
            ->limit(10)
            ->get();
    }

    public function render()
    {
        return view('livewire.finance.dashboard');
    }
}
