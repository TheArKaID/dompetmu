<div class="min-h-screen p-4 sm:p-6 space-y-5">

    {{-- ── Page Header ─────────────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Rekening</h1>
            <p class="text-zinc-400 text-sm mt-0.5">Kelola dompet, bank, dan e-wallet kamu</p>
        </div>
        <flux:button wire:click="openCreate" icon="plus" variant="primary">
            Tambah Rekening
        </flux:button>
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
                    <flux:button wire:click="openEdit('{{ $account->id }}')" variant="ghost" size="sm" icon="pencil" />
                    <flux:button
                        wire:click="delete('{{ $account->id }}')"
                        wire:confirm="Yakin ingin menghapus rekening '{{ $account->name }}'?"
                        variant="ghost"
                        size="sm"
                        icon="trash"
                        class="text-rose-400 hover:text-rose-300 hover:bg-rose-500/10"
                    />
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
            <flux:button wire:click="openCreate" icon="plus" size="sm">Tambah Rekening</flux:button>
        </div>
        @endforelse
    </div>

    {{-- ── Create/Edit Modal ───────────────────────────────────────────────── --}}
    <flux:modal wire:model="showModal" class="w-full max-w-md">
        <div class="p-6 space-y-5">
            <div>
                <flux:heading size="lg">{{ $editingId ? 'Edit Rekening' : 'Tambah Rekening Baru' }}</flux:heading>
                <flux:text class="text-zinc-400 text-sm">Masukkan detail rekening / dompet / e-wallet.</flux:text>
            </div>

            <form wire:submit="save" class="space-y-4">
                <flux:field>
                    <flux:label>Nama Rekening</flux:label>
                    <flux:input wire:model="name" placeholder="Contoh: BCA, Jago, GoPay..." autofocus />
                    <flux:error name="name" />
                </flux:field>

                <flux:field>
                    <flux:label>Saldo Awal (Rp)</flux:label>
                    <flux:input wire:model="initialBalance" type="number" min="0" step="0.01" placeholder="0" />
                    <flux:description>Saldo yang sudah ada sebelum mulai mencatat di sini.</flux:description>
                    <flux:error name="initialBalance" />
                </flux:field>

                <div class="flex justify-end gap-3 pt-2">
                    <flux:button type="button" wire:click="$set('showModal', false)" variant="ghost">Batal</flux:button>
                    <flux:button type="submit" variant="primary" icon="check">
                        {{ $editingId ? 'Simpan Perubahan' : 'Buat Rekening' }}
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

</div>
