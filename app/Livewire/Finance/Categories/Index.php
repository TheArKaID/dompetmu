<?php

namespace App\Livewire\Finance\Categories;

use App\Enums\CategoryType;
use App\Models\Category;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Kategori — FinArka')]
class Index extends Component
{
    public bool $showModal    = false;
    public ?string $editingId = null;
    public string $activeTab  = 'INCOME'; // Tab filter: INCOME | EXPENSE

    public string $name = '';
    public string $type = 'INCOME';

    // ── Computed ──────────────────────────────────────────────────────────────

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

    // ── Open modals ───────────────────────────────────────────────────────────

    public function openCreate(): void
    {
        $this->editingId = null;
        $this->name      = '';
        $this->type      = $this->activeTab; // inherit active tab as default type
        $this->showModal = true;
    }

    public function openEdit(string $id): void
    {
        $cat             = Category::findOrFail($id);
        $this->editingId = $id;
        $this->name      = $cat->name;
        $this->type      = $cat->type->value;
        $this->showModal = true;
    }

    // ── Save ──────────────────────────────────────────────────────────────────

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:50',
            'type' => 'required|in:INCOME,EXPENSE',
        ]);

        if ($this->editingId) {
            $cat = Category::findOrFail($this->editingId);
            $cat->update(['name' => $this->name, 'type' => $this->type]);
            Flux::toast(heading: 'Diperbarui', text: 'Kategori berhasil diperbarui.', variant: 'success');
        } else {
            Category::create(['name' => $this->name, 'type' => $this->type]);
            Flux::toast(heading: 'Ditambahkan', text: 'Kategori baru berhasil dibuat.', variant: 'success');
        }

        $this->showModal = false;
        unset($this->incomeCategories, $this->expenseCategories);
    }

    // ── Delete ────────────────────────────────────────────────────────────────

    public function delete(string $id): void
    {
        $cat = Category::findOrFail($id);

        if ($cat->ledgers()->exists()) {
            Flux::toast(
                heading: 'Tidak Bisa Dihapus',
                text: 'Kategori ini masih digunakan oleh transaksi yang ada.',
                variant: 'danger'
            );
            return;
        }

        $cat->delete();
        unset($this->incomeCategories, $this->expenseCategories);
        Flux::toast(heading: 'Dihapus', text: 'Kategori berhasil dihapus.', variant: 'success');
    }

    public function render()
    {
        return view('livewire.finance.categories.index');
    }
}
