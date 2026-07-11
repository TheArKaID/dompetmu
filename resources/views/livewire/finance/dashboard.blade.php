<div class="min-h-screen p-4 sm:p-6 space-y-6">

    {{-- ── Page Header ─────────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Dashboard Keuangan</h1>
            <p class="text-zinc-400 text-sm mt-0.5">{{ now()->translatedFormat('l, d F Y') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <input
                type="month"
                wire:model.live="selectedMonth"
                class="bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-1.5 text-sm text-white focus:outline-none focus:ring-2 focus:ring-violet-500"
            />
            <a href="{{ route('finance.transactions') }}" wire:navigate class="button button--primary flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Tambah Transaksi</span>
            </a>
        </div>
    </div>

    {{-- ── Summary Cards ────────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

        {{-- Total Balance --}}
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-violet-600 via-indigo-600 to-blue-700 p-5 shadow-xl shadow-violet-900/30">
            <div class="absolute -top-6 -right-6 w-24 h-24 rounded-full bg-white/5 blur-xl"></div>
            <p class="text-violet-200 text-xs font-semibold uppercase tracking-widest">Total Saldo</p>
            <p class="text-3xl font-extrabold text-white mt-2 tabular-nums">
                Rp {{ number_format($this->totalBalance, 0, ',', '.') }}
            </p>
            <p class="text-violet-300 text-xs mt-2">{{ $this->accounts->count() }} rekening aktif</p>
        </div>

        {{-- Monthly Income --}}
        <div class="relative overflow-hidden rounded-2xl bg-zinc-900 border border-zinc-800 p-5">
            <div class="absolute top-3 right-3 w-8 h-8 rounded-full bg-emerald-500/10 flex items-center justify-center">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                </svg>
            </div>
            <p class="text-zinc-400 text-xs font-semibold uppercase tracking-widest">Pemasukan</p>
            <p class="text-2xl font-bold text-emerald-400 mt-2 tabular-nums">
                Rp {{ number_format($this->monthlyIncome, 0, ',', '.') }}
            </p>
            <p class="text-zinc-500 text-xs mt-2">Bulan {{ now()->format('F Y') }}</p>
        </div>

        {{-- Monthly Expense --}}
        <div class="relative overflow-hidden rounded-2xl bg-zinc-900 border border-zinc-800 p-5">
            <div class="absolute top-3 right-3 w-8 h-8 rounded-full bg-rose-500/10 flex items-center justify-center">
                <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                </svg>
            </div>
            <p class="text-zinc-400 text-xs font-semibold uppercase tracking-widest">Pengeluaran</p>
            <p class="text-2xl font-bold text-rose-400 mt-2 tabular-nums">
                Rp {{ number_format($this->monthlyExpense, 0, ',', '.') }}
            </p>
            <p class="text-zinc-500 text-xs mt-2">Bulan {{ now()->format('F Y') }}</p>
        </div>

        {{-- Net Cashflow --}}
        <div class="relative overflow-hidden rounded-2xl bg-zinc-900 border border-zinc-800 p-5">
            <div class="absolute top-3 right-3 w-8 h-8 rounded-full {{ $this->netCashflow >= 0 ? 'bg-teal-500/10' : 'bg-orange-500/10' }} flex items-center justify-center">
                <svg class="w-4 h-4 {{ $this->netCashflow >= 0 ? 'text-teal-400' : 'text-orange-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <p class="text-zinc-400 text-xs font-semibold uppercase tracking-widest">Arus Kas Bersih</p>
            <p class="text-2xl font-bold {{ $this->netCashflow >= 0 ? 'text-teal-400' : 'text-orange-400' }} mt-2 tabular-nums">
                {{ $this->netCashflow >= 0 ? '+' : '' }}Rp {{ number_format($this->netCashflow, 0, ',', '.') }}
            </p>
            <p class="text-zinc-500 text-xs mt-2">Income − Expense</p>
        </div>
    </div>

    {{-- ── Account Balances ─────────────────────────────────────────────────── --}}
    <div>
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-white font-semibold text-sm uppercase tracking-wider">Saldo Rekening</h2>
            <a href="{{ route('finance.accounts') }}" wire:navigate class="text-violet-400 hover:text-violet-300 text-xs transition-colors">
                Kelola Rekening →
            </a>
        </div>
        <div class="flex gap-3 overflow-x-auto pb-2 scrollbar-hide">
            @forelse($this->accounts as $account)
            <div class="flex-none min-w-[160px] bg-zinc-900 border border-zinc-800 hover:border-zinc-600 rounded-xl p-4 transition-colors">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-7 h-7 rounded-full bg-violet-500/20 flex items-center justify-center">
                        <span class="text-violet-400 text-xs font-bold">{{ strtoupper(substr($account->name, 0, 1)) }}</span>
                    </div>
                    <span class="text-zinc-400 text-xs font-medium">{{ $account->name }}</span>
                </div>
                <p class="text-white font-bold text-lg tabular-nums">
                    Rp {{ number_format($account->balance, 0, ',', '.') }}
                </p>
            </div>
            @empty
            <div class="flex-none min-w-[160px] bg-zinc-900/50 border border-dashed border-zinc-700 rounded-xl p-4 flex items-center justify-center">
                <a href="{{ route('finance.accounts') }}" wire:navigate class="text-zinc-500 text-xs text-center hover:text-violet-400 transition-colors">
                    + Tambah Rekening
                </a>
            </div>
            @endforelse
        </div>
    </div>

    {{-- ── Recent Transactions ──────────────────────────────────────────────── --}}
    <div>
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-white font-semibold text-sm uppercase tracking-wider">Transaksi Terbaru</h2>
            <a href="{{ route('finance.transactions') }}" wire:navigate class="text-violet-400 hover:text-violet-300 text-xs transition-colors">
                Lihat Semua →
            </a>
        </div>

        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl overflow-hidden">
            @forelse($this->recentTransactions as $ledger)
            <div class="flex items-center justify-between px-5 py-3.5 border-b border-zinc-800/60 last:border-0 hover:bg-zinc-800/30 transition-colors">
                <div class="flex items-center gap-3 min-w-0">
                    {{-- Type indicator dot --}}
                    <div class="w-2 h-2 rounded-full flex-none {{ $ledger->is_mutation_in ? 'bg-emerald-400' : 'bg-rose-400' }}"></div>

                    <div class="min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-white text-sm font-medium truncate max-w-[140px]">
                                {{ $ledger->category?->name ?? $ledger->contact_name ?? $ledger->transaction_type->label() }}
                            </span>
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
                        </div>
                        <p class="text-zinc-500 text-xs mt-0.5">
                            {{ $ledger->account->name }} · {{ $ledger->transaction_date->format('d M Y, H:i') }}
                        </p>
                    </div>
                </div>

                <div class="text-right flex-none ml-4">
                    <p class="font-bold tabular-nums text-sm {{ $ledger->is_mutation_in ? 'text-emerald-400' : 'text-rose-400' }}">
                        {{ $ledger->is_mutation_in ? '+' : '-' }} Rp {{ number_format($ledger->amount, 0, ',', '.') }}
                    </p>
                </div>
            </div>
            @empty
            <div class="py-16 text-center">
                <p class="text-zinc-500 text-sm">Belum ada transaksi.</p>
                <a href="{{ route('finance.transactions') }}" wire:navigate class="button button--sm button--neutral mt-3 inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Catat Transaksi Pertama</span>
                </a>
            </div>
            @endforelse
        </div>
    </div>

</div>
