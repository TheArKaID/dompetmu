<?php

namespace App\Livewire\Finance\Reports;

use App\Models\Ledger;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Laporan — FinArka')]
class Index extends Component
{
    #[Url]
    public int $year;

    #[Url]
    public string $month = ''; // '' = whole year, '01'..'12' = specific month

    // Detail modal state
    public bool $showDetail     = false;
    public string $detailLabel  = '';
    public string $detailColor  = ''; // 'income' | 'expense'

    public function mount(): void
    {
        $this->year = (int) now()->format('Y');
    }

    // ── Computed ────────────────────────────────────────────────────────────────

    /** Returns summary: total income, total expense, and net */
    #[Computed]
    public function summary(): array
    {
        $q = Ledger::whereIn('transaction_type', ['INCOME', 'EXPENSE']);
        $this->applyDateFilter($q);

        $rows = $q->get();

        $income  = $rows->where('transaction_type', 'INCOME')->sum('amount');
        $expense = $rows->where('transaction_type', 'EXPENSE')->sum('amount');

        return [
            'income'  => $income,
            'expense' => $expense,
            'net'     => $income - $expense,
        ];
    }

    /** Income breakdown by category */
    #[Computed]
    public function incomeByCategory(): \Illuminate\Support\Collection
    {
        return $this->buildCategoryBreakdown('INCOME');
    }

    /** Expense breakdown by category */
    #[Computed]
    public function expenseByCategory(): \Illuminate\Support\Collection
    {
        return $this->buildCategoryBreakdown('EXPENSE');
    }

    /** Ledgers for the detail modal */
    #[Computed]
    public function detailLedgers(): \Illuminate\Support\Collection
    {
        if (! $this->showDetail) {
            return collect();
        }

        $q = Ledger::with(['account', 'category'])
            ->where('transaction_type', strtoupper($this->detailColor) === 'INCOME' ? 'INCOME' : 'EXPENSE')
            ->whereHas('category', fn ($c) => $c->where('name', $this->detailLabel))
            ->orderByDesc('transaction_date');

        $this->applyDateFilter($q);

        return $q->get();
    }

    /** Monthly bar chart data (12 months for the selected year) */
    #[Computed]
    public function monthlyChart(): array
    {
        $months = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthStr = str_pad($m, 2, '0', STR_PAD_LEFT);
            $q = Ledger::whereIn('transaction_type', ['INCOME', 'EXPENSE'])
                ->whereYear('transaction_date', $this->year)
                ->whereMonth('transaction_date', $m);

            $rows    = $q->get();
            $income  = $rows->where('transaction_type', 'INCOME')->sum('amount');
            $expense = $rows->where('transaction_type', 'EXPENSE')->sum('amount');

            $months[] = [
                'month'   => $monthStr,
                'label'   => \Carbon\Carbon::createFromFormat('m', $monthStr)->format('M'),
                'income'  => $income,
                'expense' => $expense,
            ];
        }
        return $months;
    }

    // ── Actions ─────────────────────────────────────────────────────────────────

    public function openDetail(string $label, string $color): void
    {
        $this->detailLabel = $label;
        $this->detailColor = $color;
        $this->showDetail  = true;
        unset($this->detailLedgers);
    }

    public function updatedYear(): void    { $this->clearCaches(); }
    public function updatedMonth(): void   { $this->clearCaches(); }

    private function clearCaches(): void
    {
        unset($this->summary, $this->incomeByCategory, $this->expenseByCategory, $this->monthlyChart, $this->detailLedgers);
    }

    // ── Helpers ──────────────────────────────────────────────────────────────────

    private function buildCategoryBreakdown(string $type): \Illuminate\Support\Collection
    {
        $q = Ledger::with('category')
            ->where('transaction_type', $type)
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id');

        $this->applyDateFilter($q);

        $rows  = $q->get()->sortByDesc('total');
        $grand = $rows->sum('total');

        return $rows->map(function ($row) use ($grand) {
            $name = $row->category?->name ?? '(Tanpa Kategori)';
            return [
                'label'      => $name,
                'total'      => $row->total,
                'percentage' => $grand > 0 ? round(($row->total / $grand) * 100, 1) : 0,
            ];
        });
    }

    private function applyDateFilter(\Illuminate\Database\Eloquent\Builder $q): void
    {
        if ($this->month) {
            $q->whereYear('transaction_date', $this->year)
              ->whereMonth('transaction_date', $this->month);
        } else {
            $q->whereYear('transaction_date', $this->year);
        }
    }

    public function render()
    {
        return view('livewire.finance.reports.index');
    }
}
