<?php

namespace App\Livewire\Finance\Transactions;

use App\Enums\CategoryType;
use App\Enums\TransactionType;
use App\Models\Account;
use App\Models\Category;
use App\Models\Ledger;
use App\Services\ExportService;
use App\Services\LedgerService;

use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Transaksi — FinArka')]
class Index extends Component
{
    use WithPagination, WithFileUploads;

    // ── Filters ───────────────────────────────────────────────────────────────
    #[Url]
    public string $search = '';

    #[Url]
    public string $filterAccount = '';

    #[Url]
    public string $filterType = '';

    #[Url]
    public string $filterDateFrom = '';

    #[Url]
    public string $filterDateTo = '';

    // ── Modal state ───────────────────────────────────────────────────────────
    public bool $showModal    = false;
    public string $activeTab  = 'standard'; // 'standard' | 'transfer'

    // ── Standard form fields ──────────────────────────────────────────────────
    public string $transactionType  = 'INCOME';
    public string $accountId        = '';
    public string $categoryId       = '';
    public string|float $amount     = '';
    public string $transactionDate  = '';
    public string $note             = '';
    public string $contactName      = '';
    public $photo                   = null;

    // ── Transfer form fields ──────────────────────────────────────────────────
    public string $fromAccountId        = '';
    public string $toAccountId          = '';
    public string|float $transferAmount = '';
    public string|float $adminFee       = 0;
    public string $transferDate         = '';
    public string $transferNote         = '';

    // ── Reset filters ─────────────────────────────────────────────────────────

    public function updatedSearch(): void       { $this->resetPage(); }
    public function updatedFilterAccount(): void { $this->resetPage(); }
    public function updatedFilterType(): void    { $this->resetPage(); }
    public function updatedFilterDateFrom(): void{ $this->resetPage(); }
    public function updatedFilterDateTo(): void  { $this->resetPage(); }

    // ── Computed ──────────────────────────────────────────────────────────────

    #[Computed]
    public function ledgers()
    {
        $q = Ledger::with(['account', 'category'])
            ->orderByDesc('transaction_date');

        if ($this->search) {
            $q->where(function ($q) {
                $q->where('note', 'like', '%'.$this->search.'%')
                  ->orWhere('contact_name', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->filterAccount) {
            $q->where('account_id', $this->filterAccount);
        }

        if ($this->filterType) {
            $q->where('transaction_type', $this->filterType);
        }

        if ($this->filterDateFrom) {
            $q->whereDate('transaction_date', '>=', $this->filterDateFrom);
        }

        if ($this->filterDateTo) {
            $q->whereDate('transaction_date', '<=', $this->filterDateTo);
        }

        return $q->paginate(15);
    }

    #[Computed]
    public function accounts()
    {
        return Account::orderBy('name')->get();
    }

    #[Computed]
    public function incomeCategories()
    {
        return Category::where('type', CategoryType::INCOME)->orderBy('name')->get();
    }

    #[Computed]
    public function expenseCategories()
    {
        return Category::where('type', CategoryType::EXPENSE)->orderBy('name')->get();
    }

    #[Computed]
    public function currentTypeCategories()
    {
        $type = TransactionType::tryFrom($this->transactionType);

        if (! $type?->requiresCategory()) {
            return collect();
        }

        return $type === TransactionType::INCOME
            ? $this->incomeCategories
            : $this->expenseCategories;
    }

    #[Computed]
    public function transactionTypes(): array
    {
        return TransactionType::userSelectableTypes();
    }

    // ── Modal open / reset ────────────────────────────────────────────────────

    public function openCreate(): void
    {
        $this->resetForm();
        $this->transactionDate = now()->format('Y-m-d\TH:i');
        $this->transferDate    = now()->format('Y-m-d\TH:i');

        if ($this->accounts->isNotEmpty()) {
            $this->accountId     = $this->accounts->first()->id;
            $this->fromAccountId = $this->accounts->first()->id;
            $this->toAccountId   = $this->accounts->count() > 1
                ? $this->accounts->get(1)->id
                : $this->accounts->first()->id;
        }

        $this->showModal = true;
    }

    private function resetForm(): void
    {
        $this->reset([
            'transactionType', 'accountId', 'categoryId',
            'amount', 'transactionDate', 'note', 'contactName', 'photo',
            'fromAccountId', 'toAccountId', 'transferAmount',
            'adminFee', 'transferDate', 'transferNote',
        ]);
        $this->transactionType = 'INCOME';
        $this->adminFee        = 0;
        $this->activeTab       = 'standard';
    }

    // ── Submit Standard ───────────────────────────────────────────────────────

    public function submitStandard(LedgerService $service): void
    {
        $type       = TransactionType::from($this->transactionType);
        $baseRules  = [
            'transactionType' => 'required|string',
            'accountId'       => 'required|uuid|exists:accounts,id',
            'amount'          => 'required|numeric|min:0.01',
            'transactionDate' => 'required|date',
        ];

        if ($type->requiresCategory()) {
            $baseRules['categoryId'] = 'required|uuid|exists:categories,id';
        }

        if ($type->requiresContact()) {
            $baseRules['contactName'] = 'required|string|max:100';
        }

        if ($this->photo) {
            $baseRules['photo'] = 'nullable|image|max:5120'; // 5 MB
        }

        $this->validate($baseRules);

        $service->createStandard([
            'transaction_type' => $this->transactionType,
            'account_id'       => $this->accountId,
            'category_id'      => $this->categoryId ?: null,
            'amount'           => $this->amount,
            'transaction_date' => $this->transactionDate,
            'note'             => $this->note ?: null,
            'contact_name'     => $this->contactName ?: null,
        ], $this->photo);

        $this->showModal = false;
        $this->resetForm();
        unset($this->ledgers);
        $this->dispatch('notify', type: 'success', message: 'Transaksi berhasil disimpan.');
    }

    // ── Submit Transfer ───────────────────────────────────────────────────────

    public function submitTransfer(LedgerService $service): void
    {
        $this->validate([
            'fromAccountId'  => 'required|uuid|exists:accounts,id',
            'toAccountId'    => 'required|uuid|exists:accounts,id|different:fromAccountId',
            'transferAmount' => 'required|numeric|min:0.01',
            'adminFee'       => 'nullable|numeric|min:0',
            'transferDate'   => 'required|date',
        ]);

        $service->createTransfer([
            'from_account_id'  => $this->fromAccountId,
            'to_account_id'    => $this->toAccountId,
            'amount'           => $this->transferAmount,
            'admin_fee'        => $this->adminFee ?: 0,
            'transaction_date' => $this->transferDate,
            'note'             => $this->transferNote ?: null,
        ]);

        $this->showModal = false;
        $this->resetForm();
        unset($this->ledgers);
        $this->dispatch('notify', type: 'success', message: 'Transfer berhasil dicatat.');
    }

    // ── Delete ────────────────────────────────────────────────────────────────

    public function deleteLedger(string $id, LedgerService $service): void
    {
        $ledger = Ledger::findOrFail($id);
        $service->deleteLedger($ledger);
        unset($this->ledgers);
        $this->dispatch('notify', type: 'warning', message: 'Transaksi telah dihapus.');
    }

    // ── Export CSV ────────────────────────────────────────────────────────────

    public function exportCsv(ExportService $exportService): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $ledgers = Ledger::with(['account', 'category'])
            ->orderByDesc('transaction_date')
            ->get();

        $csv      = $exportService->toCsvString($ledgers);
        $filename = 'finarika_export_'.now()->format('Ymd_His').'.csv';

        return response()->streamDownload(
            fn () => print($csv),
            $filename,
            ['Content-Type' => 'text/csv']
        );
    }

    public function render()
    {
        return view('livewire.finance.transactions.index');
    }
}
