<div class="min-h-screen p-4 sm:p-6 space-y-5">

    {{-- ── Page Header ─────────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Transaksi</h1>
            <p class="text-zinc-400 text-sm mt-0.5">Semua catatan keuangan kamu</p>
        </div>
        <div class="flex items-center gap-2">
            <flux:button wire:click="exportCsv" icon="arrow-down-tray" variant="ghost" size="sm">
                Export CSV
            </flux:button>
            <flux:button wire:click="openCreate" icon="plus" variant="primary">
                Tambah Transaksi
            </flux:button>
        </div>
    </div>

    {{-- ── Filter Bar ───────────────────────────────────────────────────────── --}}
    <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-4">
        <div class="flex flex-wrap gap-3">
            <flux:input
                wire:model.live.debounce.300ms="search"
                placeholder="Cari catatan, kontak..."
                icon="magnifying-glass"
                class="w-full sm:w-56"
                size="sm"
            />

            <flux:select wire:model.live="filterAccount" placeholder="Semua Rekening" size="sm" class="w-full sm:w-44">
                <flux:select.option value="">Semua Rekening</flux:select.option>
                @foreach($this->accounts as $account)
                    <flux:select.option value="{{ $account->id }}">{{ $account->name }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:select wire:model.live="filterType" placeholder="Semua Tipe" size="sm" class="w-full sm:w-44">
                <flux:select.option value="">Semua Tipe</flux:select.option>
                @foreach(\App\Enums\TransactionType::cases() as $t)
                    <flux:select.option value="{{ $t->value }}">{{ $t->label() }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:input wire:model.live="filterDateFrom" type="date" size="sm" class="w-full sm:w-36" />
            <flux:input wire:model.live="filterDateTo"   type="date" size="sm" class="w-full sm:w-36" />

            @if($search || $filterAccount || $filterType || $filterDateFrom || $filterDateTo)
            <flux:button wire:click="$set('search',''); $set('filterAccount',''); $set('filterType',''); $set('filterDateFrom',''); $set('filterDateTo','')"
                variant="ghost" size="sm" icon="x-mark">
                Reset
            </flux:button>
            @endif
        </div>
    </div>

    {{-- ── Transaction Table ────────────────────────────────────────────────── --}}
    <div class="bg-zinc-900 border border-zinc-800 rounded-2xl overflow-hidden">
        <flux:table>
            <flux:table.head>
                <flux:table.row class="border-zinc-800">
                    <flux:table.head.cell class="text-zinc-400">Tanggal</flux:table.head.cell>
                    <flux:table.head.cell class="text-zinc-400">Jenis</flux:table.head.cell>
                    <flux:table.head.cell class="text-zinc-400">Rekening</flux:table.head.cell>
                    <flux:table.head.cell class="text-zinc-400">Kategori / Kontak</flux:table.head.cell>
                    <flux:table.head.cell class="text-zinc-400">Catatan</flux:table.head.cell>
                    <flux:table.head.cell class="text-zinc-400 text-right">Nominal</flux:table.head.cell>
                    <flux:table.head.cell class="text-zinc-400 text-center w-16">Aksi</flux:table.head.cell>
                </flux:table.row>
            </flux:table.head>
            <flux:table.body>
                @forelse($this->ledgers as $ledger)
                <flux:table.row class="border-zinc-800/50 hover:bg-zinc-800/40 transition-colors">
                    <flux:table.cell class="text-zinc-300 text-sm whitespace-nowrap">
                        {{ $ledger->transaction_date->format('d M Y') }}<br>
                        <span class="text-zinc-500 text-xs">{{ $ledger->transaction_date->format('H:i') }}</span>
                    </flux:table.cell>

                    <flux:table.cell>
                        <flux:badge size="sm" :color="$ledger->transaction_type->color()">
                            {{ $ledger->transaction_type->label() }}
                        </flux:badge>
                    </flux:table.cell>

                    <flux:table.cell class="text-zinc-300 text-sm">
                        {{ $ledger->account->name ?? '—' }}
                    </flux:table.cell>

                    <flux:table.cell class="text-zinc-300 text-sm">
                        {{ $ledger->category?->name ?? $ledger->contact_name ?? '—' }}
                    </flux:table.cell>

                    <flux:table.cell class="text-zinc-400 text-sm max-w-[180px]">
                        <span class="truncate block" title="{{ $ledger->note }}">
                            {{ Str::limit($ledger->note ?? '—', 40) }}
                        </span>
                        @if($ledger->photo_path)
                        <a href="{{ Storage::url($ledger->photo_path) }}" target="_blank"
                           class="text-violet-400 text-xs hover:underline">
                           📎 Lihat Foto
                        </a>
                        @endif
                    </flux:table.cell>

                    <flux:table.cell class="text-right whitespace-nowrap">
                        <span class="font-bold tabular-nums text-sm {{ $ledger->is_mutation_in ? 'text-emerald-400' : 'text-rose-400' }}">
                            {{ $ledger->is_mutation_in ? '+' : '-' }} Rp {{ number_format($ledger->amount, 0, ',', '.') }}
                        </span>
                    </flux:table.cell>

                    <flux:table.cell class="text-center">
                        <flux:button
                            wire:click="deleteLedger('{{ $ledger->id }}')"
                            wire:confirm="Yakin ingin menghapus transaksi ini? {{ $ledger->reference_id ? 'Ini akan menghapus seluruh group transfer.' : '' }}"
                            variant="ghost"
                            size="sm"
                            icon="trash"
                            class="text-rose-400 hover:text-rose-300 hover:bg-rose-500/10"
                        />
                    </flux:table.cell>
                </flux:table.row>
                @empty
                <flux:table.row>
                    <flux:table.cell colspan="7" class="py-16 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-12 h-12 rounded-full bg-zinc-800 flex items-center justify-center">
                                <svg class="w-6 h-6 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <p class="text-zinc-500 text-sm">Belum ada transaksi.</p>
                            <flux:button wire:click="openCreate" icon="plus" size="sm">Tambah Sekarang</flux:button>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
                @endforelse
            </flux:table.body>
        </flux:table>

        {{-- Pagination --}}
        @if($this->ledgers->hasPages())
        <div class="px-5 py-4 border-t border-zinc-800">
            {{ $this->ledgers->links() }}
        </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- ── Create / Transfer Modal ─────────────────────────────────────────── --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    <flux:modal wire:model="showModal" class="w-full max-w-2xl">
        <div class="p-6 space-y-5">
            <div>
                <flux:heading size="lg">Catat Transaksi Baru</flux:heading>
                <flux:text class="text-zinc-400 text-sm mt-0.5">Pilih jenis transaksi lalu isi detail di bawah.</flux:text>
            </div>

            {{-- Tab switcher --}}
            <div class="flex gap-1 bg-zinc-800 rounded-lg p-1">
                <button type="button"
                    wire:click="$set('activeTab', 'standard')"
                    class="flex-1 py-1.5 rounded-md text-sm font-medium transition-all
                           {{ $activeTab === 'standard' ? 'bg-zinc-700 text-white shadow' : 'text-zinc-400 hover:text-zinc-200' }}">
                    💳 Standar
                </button>
                <button type="button"
                    wire:click="$set('activeTab', 'transfer')"
                    class="flex-1 py-1.5 rounded-md text-sm font-medium transition-all
                           {{ $activeTab === 'transfer' ? 'bg-zinc-700 text-white shadow' : 'text-zinc-400 hover:text-zinc-200' }}">
                    🔄 Transfer
                </button>
            </div>

            {{-- ── STANDARD FORM ────────────────────────────────────────────── --}}
            @if($activeTab === 'standard')
            <form wire:submit="submitStandard" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Jenis Transaksi</flux:label>
                        <flux:select wire:model.live="transactionType">
                            @foreach($this->transactionTypes as $t)
                                <flux:select.option value="{{ $t->value }}">{{ $t->label() }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:error name="transactionType" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Rekening</flux:label>
                        <flux:select wire:model="accountId">
                            <flux:select.option value="">Pilih rekening...</flux:select.option>
                            @foreach($this->accounts as $acc)
                                <flux:select.option value="{{ $acc->id }}">{{ $acc->name }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:error name="accountId" />
                    </flux:field>
                </div>

                {{-- Category (income/expense only) --}}
                @if(\App\Enums\TransactionType::from($transactionType)->requiresCategory())
                <flux:field>
                    <flux:label>Kategori</flux:label>
                    <flux:select wire:model="categoryId">
                        <flux:select.option value="">Pilih kategori...</flux:select.option>
                        @foreach($this->currentTypeCategories as $cat)
                            <flux:select.option value="{{ $cat->id }}">{{ $cat->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="categoryId" />
                </flux:field>
                @endif

                {{-- Contact Name (debt/loan only) --}}
                @if(\App\Enums\TransactionType::from($transactionType)->requiresContact())
                <flux:field>
                    <flux:label>Nama Kontak</flux:label>
                    <flux:input wire:model="contactName" placeholder="Nama orang / instansi..." />
                    <flux:error name="contactName" />
                </flux:field>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Nominal (Rp)</flux:label>
                        <flux:input wire:model="amount" type="number" min="0" step="0.01" placeholder="0" />
                        <flux:error name="amount" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Tanggal &amp; Waktu</flux:label>
                        <flux:input wire:model="transactionDate" type="datetime-local" />
                        <flux:error name="transactionDate" />
                    </flux:field>
                </div>

                <flux:field>
                    <flux:label>Catatan (opsional)</flux:label>
                    <flux:textarea wire:model="note" rows="2" placeholder="Deskripsi singkat..." />
                </flux:field>

                <flux:field>
                    <flux:label>Foto Bukti (opsional)</flux:label>
                    <input type="file" wire:model="photo" accept="image/*"
                        class="block w-full text-sm text-zinc-400 file:mr-3 file:py-1.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-violet-600/20 file:text-violet-300 hover:file:bg-violet-600/30 cursor-pointer" />
                    <flux:error name="photo" />
                    @if($photo)
                    <div class="mt-2">
                        <img src="{{ $photo->temporaryUrl() }}" class="h-24 rounded-lg object-cover border border-zinc-700" />
                    </div>
                    @endif
                </flux:field>

                <div class="flex justify-end gap-3 pt-2">
                    <flux:button type="button" wire:click="$set('showModal', false)" variant="ghost">Batal</flux:button>
                    <flux:button type="submit" variant="primary" icon="check">Simpan Transaksi</flux:button>
                </div>
            </form>
            @endif

            {{-- ── TRANSFER FORM ────────────────────────────────────────────── --}}
            @if($activeTab === 'transfer')
            <form wire:submit="submitTransfer" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Dari Rekening</flux:label>
                        <flux:select wire:model="fromAccountId">
                            <flux:select.option value="">Pilih rekening asal...</flux:select.option>
                            @foreach($this->accounts as $acc)
                                <flux:select.option value="{{ $acc->id }}">{{ $acc->name }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:error name="fromAccountId" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Ke Rekening</flux:label>
                        <flux:select wire:model="toAccountId">
                            <flux:select.option value="">Pilih rekening tujuan...</flux:select.option>
                            @foreach($this->accounts as $acc)
                                <flux:select.option value="{{ $acc->id }}">{{ $acc->name }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:error name="toAccountId" />
                    </flux:field>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Nominal Transfer (Rp)</flux:label>
                        <flux:input wire:model="transferAmount" type="number" min="0" step="0.01" placeholder="0" />
                        <flux:error name="transferAmount" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Biaya Admin (Rp)</flux:label>
                        <flux:input wire:model="adminFee" type="number" min="0" step="0.01" placeholder="0" />
                        <flux:description>Kosongkan jika tidak ada biaya.</flux:description>
                    </flux:field>
                </div>

                <flux:field>
                    <flux:label>Tanggal &amp; Waktu</flux:label>
                    <flux:input wire:model="transferDate" type="datetime-local" />
                    <flux:error name="transferDate" />
                </flux:field>

                <flux:field>
                    <flux:label>Catatan (opsional)</flux:label>
                    <flux:textarea wire:model="transferNote" rows="2" placeholder="Deskripsi singkat..." />
                </flux:field>

                <div class="flex justify-end gap-3 pt-2">
                    <flux:button type="button" wire:click="$set('showModal', false)" variant="ghost">Batal</flux:button>
                    <flux:button type="submit" variant="primary" icon="check">Catat Transfer</flux:button>
                </div>
            </form>
            @endif
        </div>
    </flux:modal>

</div>
