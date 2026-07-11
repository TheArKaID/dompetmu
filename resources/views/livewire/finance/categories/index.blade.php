<div class="min-h-screen p-4 sm:p-6 space-y-5" x-data="{ showModal: @entangled('showModal') }">

    {{-- ── Page Header ─────────────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Kategori</h1>
            <p class="text-zinc-400 text-sm mt-0.5">Atur kategori pemasukan dan pengeluaran</p>
        </div>
        <button type="button" wire:click="openCreate" class="button button--primary inline-flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Tambah Kategori</span>
        </button>
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
                @php
                    $badgeColor = match($cat->type->color()) {
                        'emerald' => 'badge--success',
                        'rose' => 'badge--danger',
                        'indigo' => 'badge--primary',
                        default => 'badge--neutral',
                    };
                @endphp
                <span class="badge badge--soft {{ $badgeColor }} badge--sm">
                    {{ $cat->type->label() }}
                </span>
            </div>
            <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                <button type="button" wire:click="openEdit('{{ $cat->id }}')" class="button button--ghost button--neutral button--sm button--icon-only" title="Edit">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125"/>
                    </svg>
                </button>
                <button type="button"
                    wire:click="delete('{{ $cat->id }}')"
                    wire:confirm="Yakin ingin menghapus kategori '{{ $cat->name }}'?"
                    class="button button--ghost button--danger button--sm button--icon-only"
                    title="Hapus">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </div>
        </div>
        @empty
        <div class="py-14 text-center">
            <p class="text-zinc-500 text-sm">
                Belum ada kategori {{ $activeTab === 'INCOME' ? 'pemasukan' : 'pengeluaran' }}.
            </p>
            <button type="button" wire:click="openCreate" class="button button--sm button--neutral mt-3 inline-flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Tambah Kategori</span>
            </button>
        </div>
        @endforelse
    </div>

    {{-- ── Create/Edit Modal (Dialog) ──────────────────────────────────────── --}}
    <div class="dialog" :class="{ 'dialog--open': showModal }" x-show="showModal" style="display: none;" x-transition>
        <div class="dialog__backdrop" @click="showModal = false"></div>
        <div class="dialog__panel max-w-md w-full bg-zinc-900 border border-zinc-800 rounded-2xl shadow-2xl p-6">
            <div class="dialog__header mb-5">
                <h3 class="text-white text-lg font-bold">{{ $editingId ? 'Edit Kategori' : 'Tambah Kategori Baru' }}</h3>
                <p class="text-zinc-400 text-sm mt-1">Beri nama kategori dan pilih jenisnya.</p>
            </div>

            <form wire:submit="save" class="space-y-4">
                <div class="field">
                    <label class="field__label text-zinc-300 font-medium text-sm block mb-1.5">Nama Kategori</label>
                    <input type="text" wire:model="name" placeholder="Contoh: Makan, Gaji, Belanja Online..." class="input bg-zinc-850 border border-zinc-700 text-white rounded-lg px-3 py-2 w-full focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none text-sm transition-all" autofocus />
                    @error('name')
                        <p class="field__error text-rose-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="field">
                    <label class="field__label text-zinc-300 font-medium text-sm block mb-1.5">Tipe</label>
                    <select wire:model="type" class="select bg-zinc-850 border border-zinc-700 text-white rounded-lg px-3 py-2.5 w-full focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none text-sm transition-all">
                        <option value="INCOME">💰 Pemasukan</option>
                        <option value="EXPENSE">💸 Pengeluaran</option>
                    </select>
                    @error('type')
                        <p class="field__error text-rose-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="dialog__footer flex justify-end gap-3 pt-4 border-t border-zinc-800/50 mt-6">
                    <button type="button" wire:click="$set('showModal', false)" class="button button--ghost button--neutral">Batal</button>
                    <button type="submit" class="button button--primary inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>{{ $editingId ? 'Simpan Perubahan' : 'Buat Kategori' }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
