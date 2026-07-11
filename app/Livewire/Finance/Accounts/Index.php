<?php

namespace App\Livewire\Finance\Accounts;

use App\Models\Account;

use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Rekening — FinArka')]
class Index extends Component
{
    public bool $showModal     = false;
    public ?string $editingId  = null;

    public string $name           = '';
    public string|float $initialBalance = 0;

    // ── Computed ──────────────────────────────────────────────────────────────

    #[Computed]
    public function accounts()
    {
        return Account::orderBy('name')->get();
    }

    // ── Open modals ───────────────────────────────────────────────────────────

    public function openCreate(): void
    {
        $this->editingId      = null;
        $this->name           = '';
        $this->initialBalance = 0;
        $this->showModal      = true;
    }

    public function openEdit(string $id): void
    {
        $account              = Account::findOrFail($id);
        $this->editingId      = $id;
        $this->name           = $account->name;
        $this->initialBalance = $account->initial_balance;
        $this->showModal      = true;
    }

    // ── Save ──────────────────────────────────────────────────────────────────

    public function save(): void
    {
        $this->validate([
            'name'           => 'required|string|max:50|unique:accounts,name,'.($this->editingId ?? 'NULL'),
            'initialBalance' => 'required|numeric|min:0',
        ]);

        if ($this->editingId) {
            $account = Account::findOrFail($this->editingId);
            $account->update([
                'name'            => $this->name,
                'initial_balance' => $this->initialBalance,
            ]);
            $this->dispatch('notify', type: 'success', message: 'Rekening berhasil diperbarui.');
        } else {
            Account::create([
                'name'            => $this->name,
                'initial_balance' => $this->initialBalance,
            ]);
            $this->dispatch('notify', type: 'success', message: 'Rekening baru berhasil dibuat.');
        }

        $this->showModal = false;
        unset($this->accounts);
    }

    // ── Delete ────────────────────────────────────────────────────────────────

    public function delete(string $id): void
    {
        $account = Account::findOrFail($id);

        if ($account->ledgers()->exists()) {
            $this->dispatch('notify', type: 'danger', message: 'Rekening ini masih memiliki transaksi. Hapus transaksi terlebih dahulu.');
            return;
        }

        $account->delete();
        unset($this->accounts);
        $this->dispatch('notify', type: 'success', message: 'Rekening berhasil dihapus.');
    }

    public function render()
    {
        return view('livewire.finance.accounts.index');
    }
}
