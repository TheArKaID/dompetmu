<div class="min-h-screen p-4 sm:p-6 space-y-5">

    {{-- ── Page Header ─────────────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Kategori</h1>
            <p class="text-zinc-400 text-sm mt-0.5">Atur kategori pemasukan dan pengeluaran</p>
        </div>
        <flux:button wire:click="openCreate" icon="plus" variant="primary">
            Tambah Kategori
        </flux:button>
    </div>

    {{-- ── Type Tabs ───────────────────────────────────────────────────────── --}}
    <div class="flex gap-1 bg-zinc-900 border border-zinc-800 rounded-xl p-1 w-fit">
        <button type="button"
            wire:click="$set('activeTab', 'INCOME')"
            class="px-5 py-2 rounded-lg text-sm font-medium transition-all
                   {{ $activeTab === 'INCOME'
                       ? 'bg-emerald-600 text-white shadow'
                       : 'text-zinc-400 hover:text-white' }}">
            💰 Pemasukan ({{ $this->incomeCategories->count() }})
        </button>
        <button type="button"
            wire:click="$set('activeTab', 'EXPENSE')"
            class="px-5 py-2 rounded-lg text-sm font-medium transition-all
                   {{ $activeTab === 'EXPENSE'
                       ? 'bg-rose-600 text-white shadow'
                       : 'text-zinc-400 hover:text-white' }}">
            💸 Pengeluaran ({{ $this->expenseCategories->count() }})
        </button>
    </div>

    {{-- ── Category List ───────────────────────────────────────────────────── --}}
    @php
        $categories = $activeTab === 'INCOME' ? $this->incomeCategories : $this->expenseCategories;
        $accentColor = $activeTab === 'INCOME' ? 'emerald' : 'rose';
        $bgAccent = $activeTab === 'INCOME' ? 'bg-emerald-500/10' : 'bg-rose-500/10';
        $textAccent = $activeTab === 'INCOME' ? 'text-emerald-400' : 'text-rose-400';
    @endphp

    <div class="bg-zinc-900 border border-zinc-800 rounded-2xl overflow-hidden">
        @forelse($categories as $cat)
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-zinc-800/50 last:border-0 group hover:bg-zinc-800/30 transition-colors">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg {{ $bgAccent }} flex items-center justify-center">
                    <span class="{{ $textAccent }} text-xs font-bold">{{ strtoupper(substr($cat->name, 0, 1)) }}</span>
                </div>
                <span class="text-white text-sm font-medium">{{ $cat->name }}</span>
                <flux:badge size="sm" :color="$cat->type->color()">{{ $cat->type->label() }}</flux:badge>
            </div>
            <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                <flux:button wire:click="openEdit('{{ $cat->id }}')" variant="ghost" size="sm" icon="pencil" />
                <flux:button
                    wire:click="delete('{{ $cat->id }}')"
                    wire:confirm="Yakin ingin menghapus kategori '{{ $cat->name }}'?"
                    variant="ghost"
                    size="sm"
                    icon="trash"
                    class="text-rose-400 hover:text-rose-300 hover:bg-rose-500/10"
                />
            </div>
        </div>
        @empty
        <div class="py-14 text-center">
            <p class="text-zinc-500 text-sm">
                Belum ada kategori {{ $activeTab === 'INCOME' ? 'pemasukan' : 'pengeluaran' }}.
            </p>
            <flux:button wire:click="openCreate" icon="plus" size="sm" class="mt-3">Tambah Kategori</flux:button>
        </div>
        @endforelse
    </div>

    {{-- ── Create/Edit Modal ───────────────────────────────────────────────── --}}
    <flux:modal wire:model="showModal" class="w-full max-w-md">
        <div class="p-6 space-y-5">
            <div>
                <flux:heading size="lg">{{ $editingId ? 'Edit Kategori' : 'Tambah Kategori Baru' }}</flux:heading>
                <flux:text class="text-zinc-400 text-sm">Beri nama kategori dan pilih jenisnya.</flux:text>
            </div>

            <form wire:submit="save" class="space-y-4">
                <flux:field>
                    <flux:label>Nama Kategori</flux:label>
                    <flux:input wire:model="name" placeholder="Contoh: Makan, Gaji, Belanja Online..." autofocus />
                    <flux:error name="name" />
                </flux:field>

                <flux:field>
                    <flux:label>Tipe</flux:label>
                    <flux:select wire:model="type">
                        <flux:select.option value="INCOME">💰 Pemasukan</flux:select.option>
                        <flux:select.option value="EXPENSE">💸 Pengeluaran</flux:select.option>
                    </flux:select>
                    <flux:error name="type" />
                </flux:field>

                <div class="flex justify-end gap-3 pt-2">
                    <flux:button type="button" wire:click="$set('showModal', false)" variant="ghost">Batal</flux:button>
                    <flux:button type="submit" variant="primary" icon="check">
                        {{ $editingId ? 'Simpan Perubahan' : 'Buat Kategori' }}
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

</div>
