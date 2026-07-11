<?php

namespace App\Livewire\Finance\Hutang;

use App\Enums\TransactionType;
use App\Models\Account;
use App\Models\Ledger;
use App\Services\LedgerService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Hutang — FinArka')]
class Index extends Component
{
    // ── Filters ───────────────────────────────────────────────────────────────
    #[Url]
    public string $filterStatus = ''; // '' | 'unpaid' | 'paid'

    #[Url]
    public string $search = '';

    // ── Detail drawer state ───────────────────────────────────────────────────
    public bool $showDetail      = false;
    public ?string $detailId     = null; // ledger id of the hutang principal

    // ── Add payment modal ─────────────────────────────────────────────────────
    public bool $showPayModal    = false;
    public string $payAccountId = '';
    public string|float $payAmount = '';
    public string $payDate       = '';
    public string $payNote       = '';

    // ── Create hutang (quick modal) ────────────────────────────────────────────
    public bool $showCreateModal   = false;
    public string $hutangSubtype   = 'DEBT';       // DEBT | LOAN
    public string $hutangAccount   = '';
    public string|float $hutangAmount = '';
    public string $hutangContact   = '';
    public string $hutangDate      = '';
    public string $hutangNote      = '';

    // ── Computed ───────────────────────────────────────────────────────────────

    /** List all DEBT and LOAN principal ledger entries */
    #[Computed]
    public function hutangs(): \Illuminate\Support\Collection
    {
        $types = [TransactionType::DEBT->value, TransactionType::LOAN->value];

        $q = Ledger::with(['account', 'category'])
            ->whereIn('transaction_type', $types)
            ->orderByDesc('transaction_date');

        if ($this->search) {
            $q->where('contact_name', 'like', '%'.$this->search.'%');
        }

        $rows = $q->get();

        // Compute paid amounts via payments, then apply status filter
        return $rows->map(function (Ledger $l) {
            $paid = $this->paidAmount($l->id, $l->transaction_type);
            $remaining = max(0, $l->amount - $paid);
            return (object)[
                'id'          => $l->id,
                'type'        => $l->transaction_type,
                'contact'     => $l->contact_name,
                'amount'      => $l->amount,
                'paid'        => $paid,
                'remaining'   => $remaining,
                'is_paid'     => $remaining <= 0,
                'date'        => $l->transaction_date,
                'note'        => $l->note,
                'account'     => $l->account,
            ];
        })->when($this->filterStatus === 'unpaid', fn($c) => $c->where('is_paid', false))
          ->when($this->filterStatus === 'paid',   fn($c) => $c->where('is_paid', true));
    }

    /** Payments for the open detail */
    #[Computed]
    public function detailPayments(): \Illuminate\Support\Collection
    {
        if (!$this->detailId) return collect();

        $principal = Ledger::find($this->detailId);
        if (!$principal) return collect();

        // DEBT_PAYMENT for DEBT, LOAN_PAYMENT for LOAN
        $paymentType = $principal->transaction_type === TransactionType::DEBT
            ? TransactionType::DEBT_PAYMENT
            : TransactionType::LOAN_PAYMENT;

        return Ledger::with('account')
            ->where('transaction_type', $paymentType)
            ->where('reference_id', $this->detailId)
            ->orderByDesc('transaction_date')
            ->get();
    }

    /** The detail principal ledger */
    #[Computed]
    public function detailPrincipal(): ?object
    {
        if (!$this->detailId) return null;
        return $this->hutangs->firstWhere('id', $this->detailId);
    }

    #[Computed]
    public function accounts()
    {
        return Account::orderBy('name')->get();
    }

    // ── Actions ───────────────────────────────────────────────────────────────

    public function openDetail(string $id): void
    {
        $this->detailId   = $id;
        $this->showDetail = true;
        unset($this->detailPayments, $this->detailPrincipal);
    }

    public function closeDetail(): void
    {
        $this->showDetail = false;
        $this->detailId   = null;
    }

    public function openPayModal(): void
    {
        $this->payAccountId = $this->accounts->first()?->id ?? '';
        $this->payDate      = now()->format('Y-m-d\TH:i');
        $this->payAmount    = '';
        $this->payNote      = '';
        $this->showPayModal = true;
    }

    public function submitPayment(LedgerService $service): void
    {
        $this->validate([
            'payAccountId' => 'required|uuid|exists:accounts,id',
            'payAmount'    => 'required|numeric|min:0.01',
            'payDate'      => 'required|date',
        ]);

        $principal = Ledger::findOrFail($this->detailId);

        $paymentType = $principal->transaction_type === TransactionType::DEBT
            ? TransactionType::DEBT_PAYMENT
            : TransactionType::LOAN_PAYMENT;

        // DEBT_PAYMENT = money going out (paying back debt) = is_mutation_in false
        // LOAN_PAYMENT = money coming in (receiving repayment on loan) = is_mutation_in true
        $isMutationIn = ($paymentType === TransactionType::LOAN_PAYMENT);

        Ledger::create([
            'transaction_type' => $paymentType,
            'account_id'       => $this->payAccountId,
            'reference_id'     => $this->detailId,     // Link to the principal
            'amount'           => $this->payAmount,
            'is_mutation_in'   => $isMutationIn,
            'transaction_date' => $this->payDate,
            'note'             => $this->payNote ?: null,
        ]);

        $this->showPayModal = false;
        unset($this->detailPayments, $this->hutangs, $this->detailPrincipal);
        $this->dispatch('notify', type: 'success', message: 'Pembayaran berhasil dicatat.');
    }

    public function deletePayment(string $paymentId): void
    {
        Ledger::findOrFail($paymentId)->delete();
        unset($this->detailPayments, $this->hutangs, $this->detailPrincipal);
        $this->dispatch('notify', type: 'warning', message: 'Pembayaran telah dihapus.');
    }

    // ── Create Hutang ─────────────────────────────────────────────────────────

    public function openCreateModal(string $subtype = 'DEBT'): void
    {
        $this->hutangSubtype  = $subtype;
        $this->hutangAccount  = $this->accounts->first()?->id ?? '';
        $this->hutangAmount   = '';
        $this->hutangContact  = '';
        $this->hutangDate     = now()->format('Y-m-d\TH:i');
        $this->hutangNote     = '';
        $this->showCreateModal = true;
    }

    public function submitCreate(LedgerService $service): void
    {
        $this->validate([
            'hutangSubtype'  => 'required|in:DEBT,LOAN',
            'hutangAccount'  => 'required|uuid|exists:accounts,id',
            'hutangAmount'   => 'required|numeric|min:0.01',
            'hutangContact'  => 'required|string|max:100',
            'hutangDate'     => 'required|date',
        ]);

        $service->createStandard([
            'transaction_type' => $this->hutangSubtype,
            'account_id'       => $this->hutangAccount,
            'amount'           => $this->hutangAmount,
            'transaction_date' => $this->hutangDate,
            'note'             => $this->hutangNote ?: null,
            'contact_name'     => $this->hutangContact,
        ]);

        $this->showCreateModal = false;
        unset($this->hutangs);
        $this->dispatch('notify', type: 'success', message: 'Hutang berhasil dicatat.');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function paidAmount(string $principalId, TransactionType $type): float
    {
        $paymentType = $type === TransactionType::DEBT
            ? TransactionType::DEBT_PAYMENT->value
            : TransactionType::LOAN_PAYMENT->value;

        return (float) Ledger::where('transaction_type', $paymentType)
            ->where('reference_id', $principalId)
            ->sum('amount');
    }

    public function updatedSearch(): void { unset($this->hutangs); }
    public function updatedFilterStatus(): void { unset($this->hutangs); }

    public function render()
    {
        return view('livewire.finance.hutang.index');
    }
}
