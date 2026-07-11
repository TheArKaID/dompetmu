<div class="min-h-screen pb-4 space-y-0"
     x-data="{
         showFilters: false,
         showModal: @entangled('showModal'),
     }">

    {{-- ── Sticky Header ────────────────────────────────────────────────────────── --}}
    <div class="sticky top-0 z-20 bg-zinc-900/95 backdrop-blur-md border-b border-zinc-800/70">
        <div class="flex items-center justify-between px-4 py-3 gap-2">
            <h1 class="text-base font-bold text-white flex-none">Transaksi</h1>

            <div class="flex items-center gap-2 flex-1 justify-end">
                {{-- Search --}}
                <div class="relative flex-1 max-w-[180px]">
                    <input wire:model.live.debounce.300ms="search"
                           type="text"
                           placeholder="Cari..."
                           class="w-full bg-zinc-800 border border-zinc-700 text-zinc-200 text-xs rounded-xl pl-7 pr-2 py-1.5 outline-none focus:ring-1 focus:ring-violet-500 focus:border-violet-500">
                    <svg class="absolute left-2 top-1/2 -translate-y-1/2 w-3 h-3 text-zinc-500 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/>
                    </svg>
                </div>

                {{-- Filter toggle --}}
                <button type="button"
                        @click="showFilters = !showFilters"
                        class="w-8 h-8 rounded-xl flex items-center justify-center transition-all"
                        :class="showFilters || '{{ $filterAccount || $filterType || $filterDateFrom || $filterDateTo }}' ? 'bg-violet-600 text-white' : 'bg-zinc-800 text-zinc-400 hover:text-zinc-200'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                </button>

                {{-- Export --}}
                <button type="button" wire:click="exportCsv"
                        class="w-8 h-8 rounded-xl bg-zinc-800 flex items-center justify-center hover:bg-zinc-700 text-zinc-400 hover:text-zinc-200 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Filter panel (collapsible) --}}
        <div x-show="showFilters"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="px-4 pb-3 grid grid-cols-2 gap-2 border-t border-zinc-800/60"
             style="display:none;">

            <select wire:model.live="filterAccount"
                    class="col-span-2 bg-zinc-800 border border-zinc-700 text-zinc-200 text-xs rounded-xl px-3 py-1.5 outline-none focus:ring-1 focus:ring-violet-500 mt-2">
                <option value="">Semua Rekening</option>
                @foreach($this->accounts as $account)
                <option value="{{ $account->id }}">{{ $account->name }}</option>
                @endforeach
            </select>

            <select wire:model.live="filterType"
                    class="bg-zinc-800 border border-zinc-700 text-zinc-200 text-xs rounded-xl px-3 py-1.5 outline-none focus:ring-1 focus:ring-violet-500">
                <option value="">Semua Tipe</option>
                @foreach(\App\Enums\TransactionType::cases() as $t)
                <option value="{{ $t->value }}">{{ $t->label() }}</option>
                @endforeach
            </select>

            <button type="button" wire:click="$set('filterAccount', ''); $set('filterType', ''); $set('filterDateFrom', ''); $set('filterDateTo', ''); $set('search', '')"
                    class="bg-zinc-800 border border-zinc-700 text-zinc-300 text-xs rounded-xl px-3 py-1.5 hover:bg-zinc-700 transition-colors">
                Reset Filter
            </button>

            <input type="date" wire:model.live="filterDateFrom"
                   class="bg-zinc-800 border border-zinc-700 text-zinc-200 text-xs rounded-xl px-2 py-1.5 outline-none focus:ring-1 focus:ring-violet-500"
                   placeholder="Dari">
            <input type="date" wire:model.live="filterDateTo"
                   class="bg-zinc-800 border border-zinc-700 text-zinc-200 text-xs rounded-xl px-2 py-1.5 outline-none focus:ring-1 focus:ring-violet-500"
                   placeholder="Sampai">
        </div>
    </div>

    {{-- ── Transaction List ────────────────────────────────────────────────────── --}}
    <div class="px-4 pt-3 space-y-0">

        @php
            $prevDate = null;
        @endphp

        @forelse($this->ledgers as $ledger)
            @php
                $dateLabel = $ledger->transaction_date->format('d M Y');
                $showDate  = $dateLabel !== $prevDate;
                $prevDate  = $dateLabel;
            @endphp

            {{-- Date separator --}}
            @if($showDate)
            <div class="py-2 first:pt-0">
                <span class="text-zinc-500 text-[10px] font-semibold uppercase tracking-widest">{{ $dateLabel }}</span>
            </div>
            @endif

            {{-- Transaction row --}}
            <div class="flex items-center gap-3 py-2.5 border-b border-zinc-800/50">
                {{-- Type icon --}}
                <div class="w-9 h-9 rounded-full flex-none flex items-center justify-center
                             {{ $ledger->is_mutation_in
                                 ? 'bg-emerald-500/15 border border-emerald-500/30'
                                 : 'bg-rose-500/15 border border-rose-500/30' }}">
                    <svg class="w-4 h-4 {{ $ledger->is_mutation_in ? 'text-emerald-400' : 'text-rose-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        @if($ledger->is_mutation_in)
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                        @else
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                        @endif
                    </svg>
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <p class="text-zinc-200 text-sm font-medium leading-tight truncate">
                        {{ $ledger->category?->name ?? $ledger->transaction_type->label() }}
                    </p>
                    <p class="text-zinc-500 text-xs mt-0.5 truncate">
                        {{ $ledger->account->name }}
                        @if($ledger->contact_name) · {{ $ledger->contact_name }} @endif
                        @if($ledger->note) · {{ Str::limit($ledger->note, 30) }} @endif
                    </p>
                </div>

                {{-- Amount + delete --}}
                <div class="flex items-center gap-2 flex-none">
                    <span class="text-sm font-bold {{ $ledger->is_mutation_in ? 'text-emerald-400' : 'text-rose-400' }}">
                        {{ $ledger->is_mutation_in ? '+' : '-' }}Rp {{ number_format($ledger->amount, 0, ',', '.') }}
                    </span>
                    <button type="button"
                            wire:click="deleteLedger('{{ $ledger->id }}')"
                            wire:confirm="Hapus transaksi ini?"
                            class="w-6 h-6 rounded-lg hover:bg-rose-500/20 flex items-center justify-center transition-colors opacity-0 hover:opacity-100 group-hover:opacity-100">
                        <svg class="w-3 h-3 text-zinc-600 hover:text-rose-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </div>

        @empty
            <div class="flex flex-col items-center justify-center py-16 gap-3">
                <div class="w-16 h-16 rounded-full bg-zinc-800 flex items-center justify-center">
                    <svg class="w-8 h-8 text-zinc-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <p class="text-zinc-400 text-sm">Belum ada transaksi</p>
                <button type="button" wire:click="openCreate"
                        class="text-violet-400 text-sm font-medium hover:underline">
                    + Tambah transaksi pertama
                </button>
            </div>
        @endforelse

        {{-- Pagination --}}
        @if($this->ledgers->hasPages())
        <div class="pt-4">
            {{ $this->ledgers->links() }}
        </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════
         ADD / EDIT MODAL (Bottom Sheet)
    ══════════════════════════════════════════════ --}}
    <div class="fixed inset-0 z-[80] bg-zinc-950/70 backdrop-blur-sm"
         x-show="showModal"
         @click="showModal = false"
         x-transition style="display:none;"></div>

    <div class="fixed bottom-0 inset-x-0 z-[90] bg-zinc-900 border-t border-zinc-800 rounded-t-3xl shadow-2xl flex flex-col max-h-[92vh]"
         x-show="showModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-y-full"
         x-transition:enter-end="translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-y-0"
         x-transition:leave-end="translate-y-full"
         style="display:none;">

        {{-- Handle --}}
        <div class="flex justify-center pt-3 pb-1 flex-none">
            <div class="w-10 h-1 rounded-full bg-zinc-700"></div>
        </div>

        {{-- Tab selector --}}
        <div class="flex px-4 pb-3 gap-2 flex-none border-b border-zinc-800/70">
            <button type="button" wire:click="$set('activeTab', 'standard')"
                    class="flex-1 py-2 rounded-xl text-sm font-semibold transition-all duration-150
                           {{ $activeTab === 'standard' ? 'bg-violet-600 text-white' : 'bg-zinc-800 text-zinc-400 hover:text-zinc-200' }}">
                Transaksi
            </button>
            <button type="button" wire:click="$set('activeTab', 'transfer')"
                    class="flex-1 py-2 rounded-xl text-sm font-semibold transition-all duration-150
                           {{ $activeTab === 'transfer' ? 'bg-sky-600 text-white' : 'bg-zinc-800 text-zinc-400 hover:text-zinc-200' }}">
                Transfer
            </button>
        </div>

        {{-- Scrollable form body --}}
        <div class="flex-1 overflow-y-auto px-4 pb-4 space-y-3">

            {{-- ── Standard Transaction Form ─────────────────────────────────── --}}
            @if($activeTab === 'standard')
            <form wire:submit.prevent="submitStandard" id="trx-form" class="space-y-3 pt-3">

                {{-- Transaction type toggle --}}
                <div>
                    <label class="text-zinc-400 text-xs font-medium block mb-1.5">Tipe Transaksi</label>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach($this->transactionTypes as $typeEnum)
                        <button type="button"
                                wire:click="$set('transactionType', '{{ $typeEnum->value }}')"
                                class="py-2 rounded-xl text-xs font-medium border transition-all duration-150
                                       {{ $transactionType === $typeEnum->value
                                           ? ($typeEnum->isMutationIn() ? 'bg-emerald-600/30 border-emerald-500/60 text-emerald-300' : 'bg-rose-600/30 border-rose-500/60 text-rose-300')
                                           : 'bg-zinc-800 border-zinc-700 text-zinc-400 hover:border-zinc-600' }}">
                            {{ $typeEnum->label() }}
                        </button>
                        @endforeach
                    </div>
                </div>

                {{-- Rekening --}}
                <div>
                    <label class="text-zinc-400 text-xs font-medium block mb-1">Rekening</label>
                    <select wire:model="accountId"
                            class="w-full bg-zinc-800 border border-zinc-700 text-zinc-200 text-sm rounded-xl px-3 py-2.5 outline-none focus:ring-1 focus:ring-violet-500">
                        @foreach($this->accounts as $a)
                        <option value="{{ $a->id }}">{{ $a->name }}</option>
                        @endforeach
                    </select>
                    @error('accountId') <p class="text-rose-400 text-xs mt-0.5">{{ $message }}</p> @enderror
                </div>

                {{-- Kategori (conditional) --}}
                @if($this->currentTypeCategories->isNotEmpty())
                <div>
                    <label class="text-zinc-400 text-xs font-medium block mb-1">Kategori</label>
                    <select wire:model="categoryId"
                            class="w-full bg-zinc-800 border border-zinc-700 text-zinc-200 text-sm rounded-xl px-3 py-2.5 outline-none focus:ring-1 focus:ring-violet-500">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($this->currentTypeCategories as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                    @error('categoryId') <p class="text-rose-400 text-xs mt-0.5">{{ $message }}</p> @enderror
                </div>
                @endif

                {{-- Contact (conditional) --}}
                @if(\App\Enums\TransactionType::tryFrom($transactionType)?->requiresContact())
                <div>
                    <label class="text-zinc-400 text-xs font-medium block mb-1">Nama Kontak</label>
                    <input type="text" wire:model="contactName" placeholder="Nama orang..."
                           class="w-full bg-zinc-800 border border-zinc-700 text-zinc-200 text-sm rounded-xl px-3 py-2.5 outline-none focus:ring-1 focus:ring-violet-500">
                    @error('contactName') <p class="text-rose-400 text-xs mt-0.5">{{ $message }}</p> @enderror
                </div>
                @endif

                {{-- Amount --}}
                <div>
                    <label class="text-zinc-400 text-xs font-medium block mb-1">Jumlah</label>
                    <input type="number" wire:model="amount" placeholder="0" step="any" min="0"
                           class="w-full bg-zinc-800 border border-zinc-700 text-zinc-200 text-sm rounded-xl px-3 py-2.5 outline-none focus:ring-1 focus:ring-violet-500">
                    @error('amount') <p class="text-rose-400 text-xs mt-0.5">{{ $message }}</p> @enderror
                </div>

                {{-- Date --}}
                <div>
                    <label class="text-zinc-400 text-xs font-medium block mb-1">Tanggal</label>
                    <input type="datetime-local" wire:model="transactionDate"
                           class="w-full bg-zinc-800 border border-zinc-700 text-zinc-200 text-sm rounded-xl px-3 py-2.5 outline-none focus:ring-1 focus:ring-violet-500">
                    @error('transactionDate') <p class="text-rose-400 text-xs mt-0.5">{{ $message }}</p> @enderror
                </div>

                {{-- Note --}}
                <div>
                    <label class="text-zinc-400 text-xs font-medium block mb-1">Catatan (opsional)</label>
                    <textarea wire:model="note" rows="2" placeholder="Catatan tambahan..."
                              class="w-full bg-zinc-800 border border-zinc-700 text-zinc-200 text-sm rounded-xl px-3 py-2.5 outline-none focus:ring-1 focus:ring-violet-500 resize-none"></textarea>
                </div>

                {{-- Buttons --}}
                <div class="flex gap-3 pt-1">
                    <button type="button" wire:click="$set('showModal', false)"
                            class="flex-1 py-2.5 rounded-xl border border-zinc-700 text-zinc-300 text-sm font-medium hover:bg-zinc-800 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="flex-1 py-2.5 rounded-xl bg-violet-600 hover:bg-violet-700 text-white text-sm font-semibold transition-colors active:scale-95">
                        <span wire:loading.remove wire:target="submitStandard">Simpan</span>
                        <span wire:loading wire:target="submitStandard">Menyimpan...</span>
                    </button>
                </div>
            </form>
            @endif

            {{-- ── Transfer Form ──────────────────────────────────────────────── --}}
            @if($activeTab === 'transfer')
            <form wire:submit.prevent="submitTransfer" class="space-y-3 pt-3">
                <div>
                    <label class="text-zinc-400 text-xs font-medium block mb-1">Dari Rekening</label>
                    <select wire:model="fromAccountId"
                            class="w-full bg-zinc-800 border border-zinc-700 text-zinc-200 text-sm rounded-xl px-3 py-2.5 outline-none focus:ring-1 focus:ring-sky-500">
                        @foreach($this->accounts as $a)
                        <option value="{{ $a->id }}">{{ $a->name }}</option>
                        @endforeach
                    </select>
                    @error('fromAccountId') <p class="text-rose-400 text-xs mt-0.5">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="text-zinc-400 text-xs font-medium block mb-1">Ke Rekening</label>
                    <select wire:model="toAccountId"
                            class="w-full bg-zinc-800 border border-zinc-700 text-zinc-200 text-sm rounded-xl px-3 py-2.5 outline-none focus:ring-1 focus:ring-sky-500">
                        @foreach($this->accounts as $a)
                        <option value="{{ $a->id }}">{{ $a->name }}</option>
                        @endforeach
                    </select>
                    @error('toAccountId') <p class="text-rose-400 text-xs mt-0.5">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="text-zinc-400 text-xs font-medium block mb-1">Jumlah Transfer</label>
                    <input type="number" wire:model="transferAmount" placeholder="0" step="any" min="0"
                           class="w-full bg-zinc-800 border border-zinc-700 text-zinc-200 text-sm rounded-xl px-3 py-2.5 outline-none focus:ring-1 focus:ring-sky-500">
                    @error('transferAmount') <p class="text-rose-400 text-xs mt-0.5">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="text-zinc-400 text-xs font-medium block mb-1">Biaya Admin (opsional)</label>
                    <input type="number" wire:model="adminFee" placeholder="0" step="any" min="0"
                           class="w-full bg-zinc-800 border border-zinc-700 text-zinc-200 text-sm rounded-xl px-3 py-2.5 outline-none focus:ring-1 focus:ring-sky-500">
                </div>

                <div>
                    <label class="text-zinc-400 text-xs font-medium block mb-1">Tanggal</label>
                    <input type="datetime-local" wire:model="transferDate"
                           class="w-full bg-zinc-800 border border-zinc-700 text-zinc-200 text-sm rounded-xl px-3 py-2.5 outline-none focus:ring-1 focus:ring-sky-500">
                    @error('transferDate') <p class="text-rose-400 text-xs mt-0.5">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="text-zinc-400 text-xs font-medium block mb-1">Catatan (opsional)</label>
                    <textarea wire:model="transferNote" rows="2" placeholder="Catatan..."
                              class="w-full bg-zinc-800 border border-zinc-700 text-zinc-200 text-sm rounded-xl px-3 py-2.5 outline-none focus:ring-1 focus:ring-sky-500 resize-none"></textarea>
                </div>

                <div class="flex gap-3 pt-1">
                    <button type="button" wire:click="$set('showModal', false)"
                            class="flex-1 py-2.5 rounded-xl border border-zinc-700 text-zinc-300 text-sm font-medium hover:bg-zinc-800 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="flex-1 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-sm font-semibold transition-colors active:scale-95">
                        <span wire:loading.remove wire:target="submitTransfer">Transfer</span>
                        <span wire:loading wire:target="submitTransfer">Memproses...</span>
                    </button>
                </div>
            </form>
            @endif
        </div>
    </div>

</div>

@script
<script>
    // Handle ?action= URL parameter to auto-open modal
    const params = new URLSearchParams(window.location.search);
    const action = params.get('action');
    if (action === 'create') {
        $wire.openCreate();
        history.replaceState(null, '', window.location.pathname);
    } else if (action === 'transfer') {
        $wire.openCreate();
        $wire.set('activeTab', 'transfer');
        history.replaceState(null, '', window.location.pathname);
    }
</script>
@endscript
