<?php

namespace App\Livewire\Finance\Accounts;

use App\Models\Account;
use Flux\Flux;
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
            Flux::toast(heading: 'Diperbarui', text: 'Rekening berhasil diperbarui.', variant: 'success');
        } else {
            Account::create([
                'name'            => $this->name,
                'initial_balance' => $this->initialBalance,
            ]);
            Flux::toast(heading: 'Ditambahkan', text: 'Rekening baru berhasil dibuat.', variant: 'success');
        }

        $this->showModal = false;
        unset($this->accounts);
    }

    // ── Delete ────────────────────────────────────────────────────────────────

    public function delete(string $id): void
    {
        $account = Account::findOrFail($id);

        if ($account->ledgers()->exists()) {
            Flux::toast(
                heading: 'Tidak Bisa Dihapus',
                text: 'Rekening ini masih memiliki transaksi. Hapus transaksi terlebih dahulu.',
                variant: 'danger'
            );
            return;
        }

        $account->delete();
        unset($this->accounts);
        Flux::toast(heading: 'Dihapus', text: 'Rekening berhasil dihapus.', variant: 'success');
    }

    public function render()
    {
        return view('livewire.finance.accounts.index');
    }
}
