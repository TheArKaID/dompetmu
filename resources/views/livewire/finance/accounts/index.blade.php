<div class="min-h-screen p-4 sm:p-6 space-y-5" x-data="{ showModal: @entangled('showModal') }">

    {{-- ── Page Header ─────────────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Rekening</h1>
            <p class="text-zinc-400 text-sm mt-0.5">Kelola dompet, bank, dan e-wallet kamu</p>
        </div>
        <button type="button" wire:click="openCreate" class="button button--primary inline-flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Tambah Rekening</span>
        </button>
    </div>

    {{-- ── Account Cards Grid ───────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @forelse($this->accounts as $account)
        <div class="group bg-zinc-900 border border-zinc-800 hover:border-violet-600/50 rounded-2xl p-5 transition-all duration-200 hover:shadow-lg hover:shadow-violet-900/20">
            <div class="flex items-start justify-between">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-violet-600 to-indigo-700 flex items-center justify-center shadow-lg">
                    <span class="text-white font-bold text-sm">{{ strtoupper(substr($account->name, 0, 2)) }}</span>
                </div>
                <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button type="button" wire:click="openEdit('{{ $account->id }}')" class="button button--ghost button--neutral button--sm button--icon-only" title="Edit">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125"/>
                        </svg>
                    </button>
                    <button type="button"
                        wire:click="delete('{{ $account->id }}')"
                        wire:confirm="Yakin ingin menghapus rekening '{{ $account->name }}'?"
                        class="button button--ghost button--danger button--sm button--icon-only"
                        title="Hapus">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="mt-4">
                <p class="text-zinc-400 text-xs font-medium uppercase tracking-wider">{{ $account->name }}</p>
                <p class="text-white text-xl font-bold tabular-nums mt-1">
                    Rp {{ number_format($account->balance, 0, ',', '.') }}
                </p>
                <p class="text-zinc-600 text-xs mt-1">
                    Saldo awal: Rp {{ number_format($account->initial_balance, 0, ',', '.') }}
                </p>
            </div>

            <div class="mt-3 pt-3 border-t border-zinc-800">
                <p class="text-zinc-500 text-xs">
                    {{ $account->ledgers()->count() }} transaksi
                </p>
            </div>
        </div>
        @empty
        <div class="col-span-full py-16 flex flex-col items-center gap-3">
            <div class="w-14 h-14 rounded-full bg-zinc-800 flex items-center justify-center">
                <svg class="w-7 h-7 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
            </div>
            <p class="text-zinc-500">Belum ada rekening. Tambahkan yang pertama!</p>
            <button type="button" wire:click="openCreate" class="button button--sm button--neutral inline-flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Tambah Rekening</span>
            </button>
        </div>
        @endforelse
    </div>

    {{-- ── Create/Edit Modal (Dialog) ──────────────────────────────────────── --}}
    <div class="dialog" :class="{ 'dialog--open': showModal }" x-show="showModal" style="display: none;" x-transition>
        <div class="dialog__backdrop" @click="showModal = false"></div>
        <div class="dialog__panel max-w-md w-full bg-zinc-900 border border-zinc-800 rounded-2xl shadow-2xl p-6">
            <div class="dialog__header mb-5">
                <h3 class="text-white text-lg font-bold">{{ $editingId ? 'Edit Rekening' : 'Tambah Rekening Baru' }}</h3>
                <p class="text-zinc-400 text-sm mt-1">Masukkan detail rekening / dompet / e-wallet.</p>
            </div>

            <form wire:submit="save" class="space-y-4">
                <div class="field">
                    <label class="field__label text-zinc-300 font-medium text-sm block mb-1.5">Nama Rekening</label>
                    <input type="text" wire:model="name" placeholder="Contoh: BCA, Jago, GoPay..." class="input bg-zinc-850 border border-zinc-700 text-white rounded-lg px-3 py-2 w-full focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none text-sm transition-all" autofocus />
                    @error('name')
                        <p class="field__error text-rose-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="field">
                    <label class="field__label text-zinc-300 font-medium text-sm block mb-1.5">Saldo Awal (Rp)</label>
                    <input type="number" wire:model="initialBalance" min="0" step="0.01" placeholder="0" class="input bg-zinc-850 border border-zinc-700 text-white rounded-lg px-3 py-2 w-full focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none text-sm transition-all" />
                    <p class="field__description text-zinc-500 text-xs mt-1.5">Saldo yang sudah ada sebelum mulai mencatat di sini.</p>
                    @error('initialBalance')
                        <p class="field__error text-rose-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="dialog__footer flex justify-end gap-3 pt-4 border-t border-zinc-800/50 mt-6">
                    <button type="button" wire:click="$set('showModal', false)" class="button button--ghost button--neutral">Batal</button>
                    <button type="submit" class="button button--primary inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>{{ $editingId ? 'Simpan Perubahan' : 'Buat Rekening' }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
