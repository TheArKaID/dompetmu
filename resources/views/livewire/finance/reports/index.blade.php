<div class="min-h-screen pb-4 space-y-4">

    {{-- ── Sticky Header ────────────────────────────────────────────────────────── --}}
    <div class="sticky top-0 z-20 bg-zinc-900/95 backdrop-blur-md border-b border-zinc-800/70 px-4 py-3">
        <div class="flex items-center justify-between">
            <h1 class="text-base font-bold text-white">Laporan Keuangan</h1>
            <div class="flex items-center gap-2">
                {{-- Month filter --}}
                <select wire:model.live="month"
                        class="bg-zinc-800 border border-zinc-700 text-zinc-200 text-xs rounded-lg px-2 py-1.5 focus:ring-1 focus:ring-violet-500 focus:border-violet-500 outline-none">
                    <option value="">Semua Bulan</option>
                    @foreach(['01'=>'Jan','02'=>'Feb','03'=>'Mar','04'=>'Apr','05'=>'Mei','06'=>'Jun','07'=>'Jul','08'=>'Agu','09'=>'Sep','10'=>'Okt','11'=>'Nov','12'=>'Des'] as $v => $label)
                    <option value="{{ $v }}">{{ $label }}</option>
                    @endforeach
                </select>
                {{-- Year filter --}}
                <select wire:model.live="year"
                        class="bg-zinc-800 border border-zinc-700 text-zinc-200 text-xs rounded-lg px-2 py-1.5 focus:ring-1 focus:ring-violet-500 focus:border-violet-500 outline-none">
                    @for($y = now()->year; $y >= now()->year - 5; $y--)
                    <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>
            </div>
        </div>
    </div>

    <div class="px-4 space-y-4">

        {{-- ── Summary Cards ──────────────────────────────────────────────────────── --}}
        <div class="grid grid-cols-2 gap-3">
            <div class="bg-emerald-950/60 border border-emerald-800/50 rounded-2xl p-3.5">
                <p class="text-emerald-400 text-[10px] font-semibold uppercase tracking-wider">Pemasukan</p>
                <p class="text-emerald-300 text-lg font-bold mt-1 leading-tight">
                    Rp {{ number_format($this->summary['income'], 0, ',', '.') }}
                </p>
            </div>
            <div class="bg-rose-950/60 border border-rose-800/50 rounded-2xl p-3.5">
                <p class="text-rose-400 text-[10px] font-semibold uppercase tracking-wider">Pengeluaran</p>
                <p class="text-rose-300 text-lg font-bold mt-1 leading-tight">
                    Rp {{ number_format($this->summary['expense'], 0, ',', '.') }}
                </p>
            </div>
        </div>

        {{-- Net --}}
        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-3.5 flex items-center justify-between">
            <span class="text-zinc-400 text-sm">Saldo Bersih</span>
            <span class="font-bold text-base {{ $this->summary['net'] >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                {{ $this->summary['net'] >= 0 ? '+' : '' }}Rp {{ number_format($this->summary['net'], 0, ',', '.') }}
            </span>
        </div>

        {{-- ── Monthly Bar Chart ───────────────────────────────────────────────────── --}}
        @if(!$month)
        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-4">
            <p class="text-zinc-300 text-sm font-semibold mb-3">Grafik Bulanan {{ $year }}</p>
            @php
                $maxVal = collect($this->monthlyChart)->map(fn($m) => max($m['income'], $m['expense']))->max() ?: 1;
            @endphp
            <div class="flex items-end gap-1 h-32 overflow-x-auto pb-1">
                @foreach($this->monthlyChart as $m)
                <div class="flex-1 min-w-[20px] flex flex-col items-center gap-0.5">
                    <div class="w-full flex items-end justify-center gap-0.5" style="height: 96px;">
                        {{-- Income bar --}}
                        <div class="flex-1 bg-emerald-500/70 rounded-t-sm transition-all duration-500"
                             style="height: {{ max(2, round(($m['income'] / $maxVal) * 96)) }}px;"
                             title="Pemasukan: Rp {{ number_format($m['income'], 0, ',', '.') }}">
                        </div>
                        {{-- Expense bar --}}
                        <div class="flex-1 bg-rose-500/70 rounded-t-sm transition-all duration-500"
                             style="height: {{ max(2, round(($m['expense'] / $maxVal) * 96)) }}px;"
                             title="Pengeluaran: Rp {{ number_format($m['expense'], 0, ',', '.') }}">
                        </div>
                    </div>
                    <span class="text-zinc-600 text-[8px]">{{ $m['label'] }}</span>
                </div>
                @endforeach
            </div>
            <div class="flex items-center gap-4 mt-2 justify-center">
                <span class="flex items-center gap-1 text-[10px] text-zinc-400">
                    <span class="w-2.5 h-2.5 rounded-sm bg-emerald-500/70"></span>Pemasukan
                </span>
                <span class="flex items-center gap-1 text-[10px] text-zinc-400">
                    <span class="w-2.5 h-2.5 rounded-sm bg-rose-500/70"></span>Pengeluaran
                </span>
            </div>
        </div>
        @endif

        {{-- ── Pengeluaran by Category ─────────────────────────────────────────────── --}}
        @if($this->expenseByCategory->isNotEmpty())
        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-4">
            <p class="text-zinc-300 text-sm font-semibold mb-3">Pengeluaran per Kategori</p>
            <div class="space-y-2.5">
                @foreach($this->expenseByCategory->take(6) as $cat)
                <button type="button"
                        wire:click="openDetail('{{ $cat['label'] }}', 'expense')"
                        class="w-full flex items-center gap-3 group text-left">
                    <div class="w-32 flex-none">
                        <p class="text-zinc-300 text-xs font-medium truncate group-hover:text-white transition-colors">{{ $cat['label'] }}</p>
                        <p class="text-zinc-500 text-[10px]">Rp {{ number_format($cat['total'], 0, ',', '.') }}</p>
                    </div>
                    <div class="flex-1 h-2 bg-zinc-800 rounded-full overflow-hidden">
                        <div class="h-full bg-rose-500/80 rounded-full transition-all duration-500"
                             style="width: {{ $cat['percentage'] }}%"></div>
                    </div>
                    <span class="text-zinc-400 text-xs flex-none w-9 text-right">{{ $cat['percentage'] }}%</span>
                </button>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ── Pemasukan by Category ───────────────────────────────────────────────── --}}
        @if($this->incomeByCategory->isNotEmpty())
        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-4">
            <p class="text-zinc-300 text-sm font-semibold mb-3">Pemasukan per Kategori</p>
            <div class="space-y-2.5">
                @foreach($this->incomeByCategory->take(6) as $cat)
                <button type="button"
                        wire:click="openDetail('{{ $cat['label'] }}', 'income')"
                        class="w-full flex items-center gap-3 group text-left">
                    <div class="w-32 flex-none">
                        <p class="text-zinc-300 text-xs font-medium truncate group-hover:text-white transition-colors">{{ $cat['label'] }}</p>
                        <p class="text-zinc-500 text-[10px]">Rp {{ number_format($cat['total'], 0, ',', '.') }}</p>
                    </div>
                    <div class="flex-1 h-2 bg-zinc-800 rounded-full overflow-hidden">
                        <div class="h-full bg-emerald-500/80 rounded-full transition-all duration-500"
                             style="width: {{ $cat['percentage'] }}%"></div>
                    </div>
                    <span class="text-zinc-400 text-xs flex-none w-9 text-right">{{ $cat['percentage'] }}%</span>
                </button>
                @endforeach
            </div>
        </div>
        @endif

        @if($this->summary['income'] == 0 && $this->summary['expense'] == 0)
        <div class="flex flex-col items-center justify-center py-12 gap-3">
            <div class="w-16 h-16 rounded-full bg-zinc-800 flex items-center justify-center">
                <svg class="w-8 h-8 text-zinc-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <p class="text-zinc-400 text-sm">Belum ada data untuk periode ini</p>
        </div>
        @endif
    </div>

    {{-- ── Detail Drawer ───────────────────────────────────────────────────────── --}}
    <div class="fixed inset-0 z-[80] bg-zinc-950/70 backdrop-blur-sm"
         x-show="$wire.showDetail"
         @click="$wire.showDetail = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display:none;"></div>

    <div class="fixed bottom-0 inset-x-0 z-[90] bg-zinc-900 border-t border-zinc-800 rounded-t-3xl shadow-2xl max-h-[80vh] flex flex-col"
         x-show="$wire.showDetail"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-y-full"
         x-transition:enter-end="translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-y-0"
         x-transition:leave-end="translate-y-full"
         style="display:none;">

        @if($showDetail)
        {{-- Handle --}}
        <div class="flex justify-center pt-3 pb-1">
            <div class="w-10 h-1 rounded-full bg-zinc-700"></div>
        </div>
        {{-- Header --}}
        <div class="flex items-center justify-between px-4 py-3 border-b border-zinc-800/70">
            <div>
                <p class="text-white font-bold">{{ $detailLabel }}</p>
                <p class="text-xs {{ $detailColor === 'income' ? 'text-emerald-400' : 'text-rose-400' }}">
                    {{ $detailColor === 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                </p>
            </div>
            <button type="button" wire:click="$set('showDetail', false)"
                    class="w-8 h-8 rounded-full bg-zinc-800 hover:bg-zinc-700 flex items-center justify-center transition-colors">
                <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        {{-- Transactions list --}}
        <div class="flex-1 overflow-y-auto px-4 py-2 space-y-2">
            @foreach($this->detailLedgers as $l)
            <div class="flex items-center gap-3 py-2 border-b border-zinc-800/50">
                <div class="flex-1">
                    <p class="text-zinc-200 text-sm">{{ $l->note ?: '(Tanpa catatan)' }}</p>
                    <p class="text-zinc-500 text-xs">{{ $l->account->name }} · {{ $l->transaction_date->format('d M Y') }}</p>
                </div>
                <span class="{{ $detailColor === 'income' ? 'text-emerald-400' : 'text-rose-400' }} text-sm font-semibold">
                    Rp {{ number_format($l->amount, 0, ',', '.') }}
                </span>
            </div>
            @endforeach
            @if($this->detailLedgers->isEmpty())
            <p class="text-zinc-500 text-sm text-center py-6">Tidak ada transaksi</p>
            @endif
        </div>
        @endif
    </div>

</div>
