<div class="min-h-screen p-4 sm:p-6 space-y-5" x-data="{ showModal: @entangled('showModal') }">

    {{-- ── Page Header ─────────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Transaksi</h1>
            <p class="text-zinc-400 text-sm mt-0.5">Semua catatan keuangan kamu</p>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" wire:click="exportCsv" class="button button--sm button--ghost button--neutral inline-flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                </svg>
                <span>Export CSV</span>
            </button>
            <button type="button" wire:click="openCreate" class="button button--primary inline-flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Tambah Transaksi</span>
            </button>
        </div>
    </div>

    {{-- ── Filter Bar ───────────────────────────────────────────────────────── --}}
    <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-4">
        <div class="flex flex-wrap gap-3">
            <div class="w-full sm:w-56 relative">
                <input type="text" 
                       wire:model.live.debounce.300ms="search" 
                       placeholder="Cari catatan, kontak..." 
                       class="input bg-zinc-850 border border-zinc-700 text-white rounded-lg pl-9 pr-3 py-1.5 w-full focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none text-sm transition-all" />
                <div class="absolute left-3 top-2.5 text-zinc-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>

            <select wire:model.live="filterAccount" class="select bg-zinc-850 border border-zinc-700 text-white rounded-lg px-3 py-1.5 w-full sm:w-44 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none text-sm transition-all">
                <option value="">Semua Rekening</option>
                @foreach($this->accounts as $account)
                    <option value="{{ $account->id }}">{{ $account->name }}</option>
                @endforeach
            </select>

            <select wire:model.live="filterType" class="select bg-zinc-850 border border-zinc-700 text-white rounded-lg px-3 py-1.5 w-full sm:w-44 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none text-sm transition-all">
                <option value="">Semua Tipe</option>
                @foreach(\App\Enums\TransactionType::cases() as $t)
                    <option value="{{ $t->value }}">{{ $t->label() }}</option>
                @endforeach
            </select>

            <input type="date" wire:model.live="filterDateFrom" class="input bg-zinc-850 border border-zinc-700 text-white rounded-lg px-3 py-1.5 w-full sm:w-36 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none text-sm transition-all" />
            <input type="date" wire:model.live="filterDateTo" class="input bg-zinc-850 border border-zinc-700 text-white rounded-lg px-3 py-1.5 w-full sm:w-36 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none text-sm transition-all" />

            @if($search || $filterAccount || $filterType || $filterDateFrom || $filterDateTo)
            <button type="button" wire:click="$set('search',''); $set('filterAccount',''); $set('filterType',''); $set('filterDateFrom',''); $set('filterDateTo','')"
                class="button button--ghost button--neutral button--sm inline-flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                <span>Reset</span>
            </button>
            @endif
        </div>
    </div>

    {{-- ── Transaction Table ────────────────────────────────────────────────── --}}
    <div class="bg-zinc-900 border border-zinc-800 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table table--hover table--align-middle w-full text-left text-zinc-300">
                <thead>
                    <tr class="border-b border-zinc-800 text-zinc-400 text-xs font-semibold uppercase tracking-wider">
                        <th class="px-5 py-3">Tanggal</th>
                        <th class="px-5 py-3">Jenis</th>
                        <th class="px-5 py-3">Rekening</th>
                        <th class="px-5 py-3">Kategori / Kontak</th>
                        <th class="px-5 py-3">Catatan</th>
                        <th class="px-5 py-3 text-right">Nominal</th>
                        <th class="px-5 py-3 text-center w-16">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($this->ledgers as $ledger)
                    <tr class="border-b border-zinc-800/50 hover:bg-zinc-800/40 transition-colors">
                        <td class="px-5 py-3.5 text-zinc-300 text-sm whitespace-nowrap">
                            {{ $ledger->transaction_date->format('d M Y') }}<br>
                            <span class="text-zinc-500 text-xs">{{ $ledger->transaction_date->format('H:i') }}</span>
                        </td>

                        <td class="px-5 py-3.5">
                            @php
                                $badgeColor = match($ledger->transaction_type->color()) {
                                    'emerald' => 'badge--success',
                                    'rose' => 'badge--danger',
                                    'indigo' => 'badge--primary',
                                    default => 'badge--neutral',
                                };
                            @endphp
                            <span class="badge badge--soft {{ $badgeColor }} badge--sm">
                                {{ $ledger->transaction_type->label() }}
                            </span>
                        </td>

                        <td class="px-5 py-3.5 text-zinc-300 text-sm">
                            {{ $ledger->account->name ?? '—' }}
                        </td>

                        <td class="px-5 py-3.5 text-zinc-300 text-sm">
                            {{ $ledger->category?->name ?? $ledger->contact_name ?? '—' }}
                        </td>

                        <td class="px-5 py-3.5 text-zinc-400 text-sm max-w-[180px]">
                            <span class="truncate block" title="{{ $ledger->note }}">
                                {{ Str::limit($ledger->note ?? '—', 40) }}
                            </span>
                            @if($ledger->photo_path)
                            <a href="{{ Storage::url($ledger->photo_path) }}" target="_blank"
                               class="text-violet-400 text-xs hover:underline mt-0.5 inline-block">
                               📎 Lihat Foto
                            </a>
                            @endif
                        </td>

                        <td class="px-5 py-3.5 text-right whitespace-nowrap">
                            <span class="font-bold tabular-nums text-sm {{ $ledger->is_mutation_in ? 'text-emerald-400' : 'text-rose-400' }}">
                                {{ $ledger->is_mutation_in ? '+' : '-' }} Rp {{ number_format($ledger->amount, 0, ',', '.') }}
                            </span>
                        </td>

                        <td class="px-5 py-3.5 text-center">
                            <button type="button"
                                wire:click="deleteLedger('{{ $ledger->id }}')"
                                wire:confirm="Yakin ingin menghapus transaksi ini? {{ $ledger->reference_id ? 'Ini akan menghapus seluruh group transfer.' : '' }}"
                                class="button button--ghost button--danger button--sm button--icon-only"
                                title="Hapus">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-12 h-12 rounded-full bg-zinc-800 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-zinc-650" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                </div>
                                <p class="text-zinc-500 text-sm">Belum ada transaksi.</p>
                                <button type="button" wire:click="openCreate" class="button button--sm button--neutral inline-flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    <span>Tambah Sekarang</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($this->ledgers->hasPages())
        <div class="px-5 py-4 border-t border-zinc-800">
            {{ $this->ledgers->links() }}
        </div>
        @endif
    </div>

    {{-- ── Create / Transfer Modal (Dialog) ───────────────────────────────── --}}
    <div class="dialog" :class="{ 'dialog--open': showModal }" x-show="showModal" style="display: none;" x-transition>
        <div class="dialog__backdrop" @click="showModal = false"></div>
        <div class="dialog__panel max-w-2xl w-full bg-zinc-900 border border-zinc-800 rounded-2xl shadow-2xl p-6">
            <div class="dialog__header mb-5">
                <h3 class="text-white text-lg font-bold">Catat Transaksi Baru</h3>
                <p class="text-zinc-400 text-sm mt-1">Pilih jenis transaksi lalu isi detail di bawah.</p>
            </div>

            {{-- Tab switcher --}}
            <div class="flex gap-1 bg-zinc-800 rounded-lg p-1 mb-5">
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
                    <div class="field">
                        <label class="field__label text-zinc-300 font-medium text-sm block mb-1.5">Jenis Transaksi</label>
                        <select wire:model.live="transactionType" class="select bg-zinc-850 border border-zinc-700 text-white rounded-lg px-3 py-2 w-full focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none text-sm transition-all">
                            @foreach($this->transactionTypes as $t)
                                <option value="{{ $t->value }}">{{ $t->label() }}</option>
                            @endforeach
                        </select>
                        @error('transactionType')
                            <p class="field__error text-rose-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field">
                        <label class="field__label text-zinc-300 font-medium text-sm block mb-1.5">Rekening</label>
                        <select wire:model="accountId" class="select bg-zinc-850 border border-zinc-700 text-white rounded-lg px-3 py-2 w-full focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none text-sm transition-all">
                            <option value="">Pilih rekening...</option>
                            @foreach($this->accounts as $acc)
                                <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                            @endforeach
                        </select>
                        @error('accountId')
                            <p class="field__error text-rose-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Category (income/expense only) --}}
                @if(\App\Enums\TransactionType::from($transactionType)->requiresCategory())
                <div class="field">
                    <label class="field__label text-zinc-300 font-medium text-sm block mb-1.5">Kategori</label>
                    <select wire:model="categoryId" class="select bg-zinc-850 border border-zinc-700 text-white rounded-lg px-3 py-2 w-full focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none text-sm transition-all">
                        <option value="">Pilih kategori...</option>
                        @foreach($this->currentTypeCategories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('categoryId')
                        <p class="field__error text-rose-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                @endif

                {{-- Contact Name (debt/loan only) --}}
                @if(\App\Enums\TransactionType::from($transactionType)->requiresContact())
                <div class="field">
                    <label class="field__label text-zinc-300 font-medium text-sm block mb-1.5">Nama Kontak</label>
                    <input type="text" wire:model="contactName" placeholder="Nama orang / instansi..." class="input bg-zinc-850 border border-zinc-700 text-white rounded-lg px-3 py-2 w-full focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none text-sm transition-all" />
                    @error('contactName')
                        <p class="field__error text-rose-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="field">
                        <label class="field__label text-zinc-300 font-medium text-sm block mb-1.5">Nominal (Rp)</label>
                        <input type="number" wire:model="amount" min="0" step="0.01" placeholder="0" class="input bg-zinc-850 border border-zinc-700 text-white rounded-lg px-3 py-2 w-full focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none text-sm transition-all" />
                        @error('amount')
                            <p class="field__error text-rose-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field">
                        <label class="field__label text-zinc-300 font-medium text-sm block mb-1.5">Tanggal &amp; Waktu</label>
                        <input type="datetime-local" wire:model="transactionDate" class="input bg-zinc-850 border border-zinc-700 text-white rounded-lg px-3 py-2 w-full focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none text-sm transition-all" />
                        @error('transactionDate')
                            <p class="field__error text-rose-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="field">
                    <label class="field__label text-zinc-300 font-medium text-sm block mb-1.5">Catatan (opsional)</label>
                    <textarea wire:model="note" rows="2" placeholder="Deskripsi singkat..." class="textarea bg-zinc-850 border border-zinc-700 text-white rounded-lg px-3 py-2 w-full focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none text-sm transition-all"></textarea>
                </div>

                <div class="field">
                    <label class="field__label text-zinc-300 font-medium text-sm block mb-1.5">Foto Bukti (opsional)</label>
                    <input type="file" wire:model="photo" accept="image/*"
                        class="block w-full text-sm text-zinc-400 file:mr-3 file:py-1.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-violet-600/20 file:text-violet-300 hover:file:bg-violet-600/30 cursor-pointer" />
                    @error('photo')
                        <p class="field__error text-rose-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    @if($photo)
                    <div class="mt-2">
                        <img src="{{ $photo->temporaryUrl() }}" class="h-24 rounded-lg object-cover border border-zinc-700" />
                    </div>
                    @endif
                </div>

                <div class="dialog__footer flex justify-end gap-3 pt-4 border-t border-zinc-800/50 mt-6">
                    <button type="button" wire:click="$set('showModal', false)" class="button button--ghost button--neutral">Batal</button>
                    <button type="submit" class="button button--primary inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Simpan Transaksi</span>
                    </button>
                </div>
            </form>
            @endif

            {{-- ── TRANSFER FORM ────────────────────────────────────────────── --}}
            @if($activeTab === 'transfer')
            <form wire:submit="submitTransfer" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="field">
                        <label class="field__label text-zinc-300 font-medium text-sm block mb-1.5">Dari Rekening</label>
                        <select wire:model="fromAccountId" class="select bg-zinc-850 border border-zinc-700 text-white rounded-lg px-3 py-2 w-full focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none text-sm transition-all">
                            <option value="">Pilih rekening asal...</option>
                            @foreach($this->accounts as $acc)
                                <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                            @endforeach
                        </select>
                        @error('fromAccountId')
                            <p class="field__error text-rose-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field">
                        <label class="field__label text-zinc-300 font-medium text-sm block mb-1.5">Ke Rekening</label>
                        <select wire:model="toAccountId" class="select bg-zinc-850 border border-zinc-700 text-white rounded-lg px-3 py-2 w-full focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none text-sm transition-all">
                            <option value="">Pilih rekening tujuan...</option>
                            @foreach($this->accounts as $acc)
                                <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                            @endforeach
                        </select>
                        @error('toAccountId')
                            <p class="field__error text-rose-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="field">
                        <label class="field__label text-zinc-300 font-medium text-sm block mb-1.5">Nominal Transfer (Rp)</label>
                        <input type="number" wire:model="transferAmount" min="0" step="0.01" placeholder="0" class="input bg-zinc-850 border border-zinc-700 text-white rounded-lg px-3 py-2 w-full focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none text-sm transition-all" />
                        @error('transferAmount')
                            <p class="field__error text-rose-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field">
                        <label class="field__label text-zinc-300 font-medium text-sm block mb-1.5">Biaya Admin (Rp)</label>
                        <input type="number" wire:model="adminFee" min="0" step="0.01" placeholder="0" class="input bg-zinc-850 border border-zinc-700 text-white rounded-lg px-3 py-2 w-full focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none text-sm transition-all" />
                        <p class="field__description text-zinc-500 text-xs mt-1.5">Kosongkan jika tidak ada biaya.</p>
                    </div>
                </div>

                <div class="field">
                    <label class="field__label text-zinc-300 font-medium text-sm block mb-1.5">Tanggal &amp; Waktu</label>
                    <input type="datetime-local" wire:model="transferDate" class="input bg-zinc-850 border border-zinc-700 text-white rounded-lg px-3 py-2 w-full focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none text-sm transition-all" />
                    @error('transferDate')
                        <p class="field__error text-rose-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="field">
                    <label class="field__label text-zinc-300 font-medium text-sm block mb-1.5">Catatan (opsional)</label>
                    <textarea wire:model="transferNote" rows="2" placeholder="Deskripsi singkat..." class="textarea bg-zinc-850 border border-zinc-700 text-white rounded-lg px-3 py-2 w-full focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none text-sm transition-all"></textarea>
                </div>

                <div class="dialog__footer flex justify-end gap-3 pt-4 border-t border-zinc-800/50 mt-6">
                    <button type="button" wire:click="$set('showModal', false)" class="button button--ghost button--neutral">Batal</button>
                    <button type="submit" class="button button--primary inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Catat Transfer</span>
                    </button>
                </div>
            </form>
            @endif
        </div>
    </div>

</div>
