<div class="min-h-screen pb-4 space-y-0">

    {{-- ── Sticky Header ────────────────────────────────────────────────────────── --}}
    <div class="sticky top-0 z-20 bg-zinc-900/95 backdrop-blur-md border-b border-zinc-800/70 px-4 py-3">
        <div class="flex items-center justify-between gap-2">
            <h1 class="text-base font-bold text-white flex-none">Hutang & Piutang</h1>
            <div class="flex items-center gap-2 flex-1 justify-end">
                {{-- Status filter --}}
                <select wire:model.live="filterStatus"
                        class="bg-zinc-800 border border-zinc-700 text-zinc-200 text-xs rounded-lg px-2 py-1.5 outline-none focus:ring-1 focus:ring-violet-500">
                    <option value="">Semua</option>
                    <option value="unpaid">Belum Lunas</option>
                    <option value="paid">Lunas</option>
                </select>
                {{-- Search --}}
                <div class="relative">
                    <input wire:model.live.debounce.300ms="search"
                           type="text"
                           placeholder="Cari..."
                           class="bg-zinc-800 border border-zinc-700 text-zinc-200 text-xs rounded-lg pl-7 pr-2 py-1.5 w-24 focus:w-36 transition-all duration-300 outline-none focus:ring-1 focus:ring-violet-500 focus:border-violet-500">
                    <svg class="absolute left-2 top-1/2 -translate-y-1/2 w-3 h-3 text-zinc-500 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Hutang List ─────────────────────────────────────────────────────────── --}}
    <div class="px-4 pt-3 space-y-3">
        @forelse($this->hutangs as $h)
        <button type="button"
                wire:click="openDetail('{{ $h->id }}')"
                class="w-full text-left bg-zinc-900 border border-zinc-800 rounded-2xl p-4 hover:border-zinc-700 active:scale-[0.98] transition-all duration-150">
            <div class="flex items-start justify-between gap-3 mb-3">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-0.5">
                        {{-- Type badge --}}
                        <span class="text-[9px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded-md
                                     {{ $h->type->value === 'DEBT' ? 'bg-rose-500/20 text-rose-400 border border-rose-500/30' : 'bg-sky-500/20 text-sky-400 border border-sky-500/30' }}">
                            {{ $h->type->value === 'DEBT' ? 'Hutang' : 'Piutang' }}
                        </span>
                        {{-- Status badge --}}
                        <span class="text-[9px] font-semibold px-1.5 py-0.5 rounded-md
                                     {{ $h->is_paid ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-amber-500/20 text-amber-400 border border-amber-500/30' }}">
                            {{ $h->is_paid ? 'Lunas' : 'Belum Lunas' }}
                        </span>
                    </div>
                    <p class="text-white font-semibold text-sm leading-tight">{{ $h->contact }}</p>
                    <p class="text-zinc-500 text-xs">{{ $h->account->name }} · {{ $h->date->format('d M Y') }}</p>
                </div>
                <div class="text-right flex-none">
                    <p class="text-white font-bold text-sm">Rp {{ number_format($h->amount, 0, ',', '.') }}</p>
                    <p class="text-zinc-400 text-xs">Sisa: Rp {{ number_format($h->remaining, 0, ',', '.') }}</p>
                </div>
            </div>
            {{-- Progress bar --}}
            @php $pct = $h->amount > 0 ? min(100, round(($h->paid / $h->amount) * 100)) : 0; @endphp
            <div class="h-1.5 bg-zinc-800 rounded-full overflow-hidden">
                <div class="h-full rounded-full transition-all duration-500
                            {{ $h->is_paid ? 'bg-emerald-500' : 'bg-amber-500' }}"
                     style="width: {{ $pct }}%"></div>
            </div>
            <p class="text-zinc-500 text-[10px] mt-1 text-right">{{ $pct }}% dibayar</p>
        </button>
        @empty
        <div class="flex flex-col items-center justify-center py-16 gap-3">
            <div class="w-16 h-16 rounded-full bg-zinc-800 flex items-center justify-center">
                <svg class="w-8 h-8 text-zinc-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <p class="text-zinc-400 text-sm">Belum ada catatan hutang</p>
        </div>
        @endforelse
    </div>

    {{-- ── Hutang Detail Bottom Sheet ──────────────────────────────────────────── --}}
    <div class="fixed inset-0 z-[80] bg-zinc-950/70 backdrop-blur-sm"
         x-show="$wire.showDetail"
         @click="$wire.closeDetail()"
         x-transition style="display:none;"></div>

    <div class="fixed bottom-0 inset-x-0 z-[90] bg-zinc-900 border-t border-zinc-800 rounded-t-3xl shadow-2xl flex flex-col max-h-[85vh]"
         x-show="$wire.showDetail"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-y-full"
         x-transition:enter-end="translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-y-0"
         x-transition:leave-end="translate-y-full"
         style="display:none;">

        @if($showDetail && $this->detailPrincipal)
        @php $dp = $this->detailPrincipal; @endphp

        {{-- Handle --}}
        <div class="flex justify-center pt-3 pb-1 flex-none">
            <div class="w-10 h-1 rounded-full bg-zinc-700"></div>
        </div>

        {{-- Detail header --}}
        <div class="flex items-start gap-3 px-4 pb-3 border-b border-zinc-800/70 flex-none">
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-[10px] font-bold uppercase px-1.5 py-0.5 rounded-md
                                 {{ $dp->type->value === 'DEBT' ? 'bg-rose-500/20 text-rose-400 border border-rose-500/30' : 'bg-sky-500/20 text-sky-400 border border-sky-500/30' }}">
                        {{ $dp->type->value === 'DEBT' ? 'Hutang' : 'Piutang' }}
                    </span>
                    <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded-md
                                 {{ $dp->is_paid ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-amber-500/20 text-amber-400 border border-amber-500/30' }}">
                        {{ $dp->is_paid ? 'Lunas' : 'Belum Lunas' }}
                    </span>
                </div>
                <p class="text-white font-bold">{{ $dp->contact }}</p>
                <p class="text-zinc-400 text-xs">{{ $dp->account->name }} · {{ $dp->date->format('d M Y') }}</p>
                @if($dp->note)
                <p class="text-zinc-500 text-xs mt-0.5 italic">{{ $dp->note }}</p>
                @endif
            </div>
            <button type="button" wire:click="closeDetail"
                    class="w-8 h-8 rounded-full bg-zinc-800 hover:bg-zinc-700 flex items-center justify-center flex-none transition-colors">
                <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Amounts summary --}}
        <div class="px-4 py-3 grid grid-cols-3 gap-2 border-b border-zinc-800/50 flex-none">
            <div class="text-center">
                <p class="text-zinc-500 text-[10px]">Total</p>
                <p class="text-white font-bold text-sm">Rp {{ number_format($dp->amount, 0, ',', '.') }}</p>
            </div>
            <div class="text-center">
                <p class="text-zinc-500 text-[10px]">Dibayar</p>
                <p class="text-emerald-400 font-bold text-sm">Rp {{ number_format($dp->paid, 0, ',', '.') }}</p>
            </div>
            <div class="text-center">
                <p class="text-zinc-500 text-[10px]">Sisa</p>
                <p class="{{ $dp->is_paid ? 'text-zinc-400' : 'text-amber-400' }} font-bold text-sm">Rp {{ number_format($dp->remaining, 0, ',', '.') }}</p>
            </div>
        </div>

        {{-- Add payment button --}}
        @if(!$dp->is_paid)
        <div class="px-4 py-2.5 flex-none border-b border-zinc-800/50">
            <button type="button" wire:click="openPayModal"
                    class="w-full py-2 rounded-xl bg-violet-600 hover:bg-violet-700 text-white text-sm font-semibold transition-colors active:scale-95 flex items-center justify-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Catat Pembayaran
            </button>
        </div>
        @endif

        {{-- Payment history --}}
        <div class="flex-1 overflow-y-auto px-4 py-2">
            <p class="text-zinc-400 text-xs font-semibold uppercase tracking-wider mb-2">Riwayat Pembayaran</p>
            @forelse($this->detailPayments as $pay)
            <div class="flex items-center gap-3 py-2.5 border-b border-zinc-800/50">
                <div class="w-8 h-8 rounded-full bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center flex-none">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-zinc-300 text-sm font-medium">Rp {{ number_format($pay->amount, 0, ',', '.') }}</p>
                    <p class="text-zinc-500 text-xs">{{ $pay->account->name }} · {{ $pay->transaction_date->format('d M Y') }}</p>
                    @if($pay->note)
                    <p class="text-zinc-600 text-xs italic">{{ $pay->note }}</p>
                    @endif
                </div>
                <button type="button" wire:click="deletePayment('{{ $pay->id }}')"
                        wire:confirm="Hapus pembayaran ini?"
                        class="w-7 h-7 rounded-lg hover:bg-rose-500/20 flex items-center justify-center transition-colors">
                    <svg class="w-3.5 h-3.5 text-zinc-600 hover:text-rose-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </div>
            @empty
            <p class="text-zinc-600 text-sm text-center py-6">Belum ada pembayaran</p>
            @endforelse
        </div>
        @endif
    </div>

    {{-- ── Pay Modal ───────────────────────────────────────────────────────────── --}}
    <div class="fixed inset-0 z-[100] bg-zinc-950/70 backdrop-blur-sm flex items-end"
         x-show="$wire.showPayModal"
         @click.self="$wire.set('showPayModal', false)"
         x-transition style="display:none;">
        <div class="w-full bg-zinc-900 border-t border-zinc-800 rounded-t-3xl p-4 space-y-4">
            <div class="flex justify-center mb-1">
                <div class="w-10 h-1 rounded-full bg-zinc-700"></div>
            </div>
            <p class="text-white font-bold text-base text-center">Catat Pembayaran</p>

            <div class="space-y-3">
                <div>
                    <label class="text-zinc-400 text-xs font-medium block mb-1">Rekening</label>
                    <select wire:model="payAccountId"
                            class="w-full bg-zinc-800 border border-zinc-700 text-zinc-200 text-sm rounded-xl px-3 py-2.5 outline-none focus:ring-1 focus:ring-violet-500">
                        @foreach($this->accounts as $acc)
                        <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                        @endforeach
                    </select>
                    @error('payAccountId') <p class="text-rose-400 text-xs mt-0.5">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-zinc-400 text-xs font-medium block mb-1">Jumlah</label>
                    <input type="number" wire:model="payAmount" placeholder="0"
                           class="w-full bg-zinc-800 border border-zinc-700 text-zinc-200 text-sm rounded-xl px-3 py-2.5 outline-none focus:ring-1 focus:ring-violet-500">
                    @error('payAmount') <p class="text-rose-400 text-xs mt-0.5">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-zinc-400 text-xs font-medium block mb-1">Tanggal</label>
                    <input type="datetime-local" wire:model="payDate"
                           class="w-full bg-zinc-800 border border-zinc-700 text-zinc-200 text-sm rounded-xl px-3 py-2.5 outline-none focus:ring-1 focus:ring-violet-500">
                </div>
                <div>
                    <label class="text-zinc-400 text-xs font-medium block mb-1">Catatan (opsional)</label>
                    <input type="text" wire:model="payNote" placeholder="Catatan..."
                           class="w-full bg-zinc-800 border border-zinc-700 text-zinc-200 text-sm rounded-xl px-3 py-2.5 outline-none focus:ring-1 focus:ring-violet-500">
                </div>
            </div>

            <div class="flex gap-3 pt-1">
                <button type="button" wire:click="$set('showPayModal', false)"
                        class="flex-1 py-2.5 rounded-xl border border-zinc-700 text-zinc-300 text-sm font-medium hover:bg-zinc-800 transition-colors">
                    Batal
                </button>
                <button type="button" wire:click="submitPayment"
                        class="flex-1 py-2.5 rounded-xl bg-violet-600 hover:bg-violet-700 text-white text-sm font-semibold transition-colors active:scale-95">
                    Simpan
                </button>
            </div>
        </div>
    </div>

    {{-- ── Create Hutang Modal ──────────────────────────────────────────────────── --}}
    <div class="fixed inset-0 z-[100] bg-zinc-950/70 backdrop-blur-sm flex items-end"
         x-show="$wire.showCreateModal"
         @click.self="$wire.set('showCreateModal', false)"
         x-transition style="display:none;">
        <div class="w-full bg-zinc-900 border-t border-zinc-800 rounded-t-3xl p-4 space-y-4">
            <div class="flex justify-center mb-1">
                <div class="w-10 h-1 rounded-full bg-zinc-700"></div>
            </div>
            <p class="text-white font-bold text-base text-center">Catat Hutang/Piutang</p>

            {{-- Subtype toggle --}}
            <div class="flex rounded-xl overflow-hidden border border-zinc-700 bg-zinc-800">
                <button type="button" wire:click="$set('hutangSubtype', 'DEBT')"
                        class="flex-1 py-2 text-sm font-medium transition-all duration-150 {{ $hutangSubtype === 'DEBT' ? 'bg-rose-600 text-white' : 'text-zinc-400 hover:text-zinc-200' }}">
                    Hutang (Bayar)
                </button>
                <button type="button" wire:click="$set('hutangSubtype', 'LOAN')"
                        class="flex-1 py-2 text-sm font-medium transition-all duration-150 {{ $hutangSubtype === 'LOAN' ? 'bg-sky-600 text-white' : 'text-zinc-400 hover:text-zinc-200' }}">
                    Piutang (Tagih)
                </button>
            </div>

            <div class="space-y-3">
                <div>
                    <label class="text-zinc-400 text-xs font-medium block mb-1">Nama Kontak</label>
                    <input type="text" wire:model="hutangContact" placeholder="Nama orang..."
                           class="w-full bg-zinc-800 border border-zinc-700 text-zinc-200 text-sm rounded-xl px-3 py-2.5 outline-none focus:ring-1 focus:ring-violet-500">
                    @error('hutangContact') <p class="text-rose-400 text-xs mt-0.5">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-zinc-400 text-xs font-medium block mb-1">Rekening</label>
                    <select wire:model="hutangAccount"
                            class="w-full bg-zinc-800 border border-zinc-700 text-zinc-200 text-sm rounded-xl px-3 py-2.5 outline-none focus:ring-1 focus:ring-violet-500">
                        @foreach($this->accounts as $acc)
                        <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-zinc-400 text-xs font-medium block mb-1">Jumlah</label>
                    <input type="number" wire:model="hutangAmount" placeholder="0"
                           class="w-full bg-zinc-800 border border-zinc-700 text-zinc-200 text-sm rounded-xl px-3 py-2.5 outline-none focus:ring-1 focus:ring-violet-500">
                    @error('hutangAmount') <p class="text-rose-400 text-xs mt-0.5">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-zinc-400 text-xs font-medium block mb-1">Tanggal</label>
                    <input type="datetime-local" wire:model="hutangDate"
                           class="w-full bg-zinc-800 border border-zinc-700 text-zinc-200 text-sm rounded-xl px-3 py-2.5 outline-none focus:ring-1 focus:ring-violet-500">
                </div>
                <div>
                    <label class="text-zinc-400 text-xs font-medium block mb-1">Catatan (opsional)</label>
                    <input type="text" wire:model="hutangNote" placeholder="Catatan..."
                           class="w-full bg-zinc-800 border border-zinc-700 text-zinc-200 text-sm rounded-xl px-3 py-2.5 outline-none focus:ring-1 focus:ring-violet-500">
                </div>
            </div>

            <div class="flex gap-3 pt-1">
                <button type="button" wire:click="$set('showCreateModal', false)"
                        class="flex-1 py-2.5 rounded-xl border border-zinc-700 text-zinc-300 text-sm font-medium hover:bg-zinc-800 transition-colors">
                    Batal
                </button>
                <button type="button" wire:click="submitCreate"
                        class="flex-1 py-2.5 rounded-xl bg-violet-600 hover:bg-violet-700 text-white text-sm font-semibold transition-colors active:scale-95">
                    Simpan
                </button>
            </div>
        </div>
    </div>

</div>

@script
<script>
    // If URL has ?action=create, open create modal
    const params = new URLSearchParams(window.location.search);
    if (params.get('action') === 'create') {
        $wire.openCreateModal();
        history.replaceState(null, '', window.location.pathname);
    }
</script>
@endscript
