<div class="min-h-screen p-4 sm:p-6 space-y-6">

    {{-- ── Page Header ─────────────────────────────────────────────────────── --}}
    <div>
        <h1 class="text-2xl font-bold text-white tracking-tight">Import Data</h1>
        <p class="text-zinc-400 text-sm mt-0.5">Import transaksi dari file CSV format DompetKu</p>
    </div>

    {{-- ── Format Info ─────────────────────────────────────────────────────── --}}
    <div class="bg-indigo-950/50 border border-indigo-800/50 rounded-xl p-4">
        <div class="flex items-start gap-3">
            <div class="w-6 h-6 rounded-full bg-indigo-500/20 flex items-center justify-center flex-none mt-0.5">
                <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-indigo-300 text-sm font-medium">Format CSV yang Didukung</p>
                <p class="text-indigo-400/80 text-xs mt-1">
                    Header kolom: <code class="bg-indigo-900/50 px-1 rounded">Kategori, Rekening, Jumlah, Tanggal, Catatan, Tipe</code>
                </p>
                <p class="text-indigo-400/80 text-xs mt-1">
                    Tipe: <code class="bg-indigo-900/50 px-1 rounded">+</code> untuk pemasukan,
                    <code class="bg-indigo-900/50 px-1 rounded">-</code> untuk pengeluaran.
                    Transfer (Kirim Saldo / Terima Saldo) akan otomatis digabungkan.
                </p>
            </div>
        </div>
    </div>

    {{-- ── Upload Form ─────────────────────────────────────────────────────── --}}
    <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6 space-y-5">

        @if($status === 'idle')
        <form wire:submit="import" class="space-y-5">
            {{-- Drop zone --}}
            <div class="border-2 border-dashed border-zinc-700 hover:border-violet-500/60 rounded-xl p-8 text-center transition-colors cursor-pointer">
                <div class="flex flex-col items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-zinc-800 flex items-center justify-center">
                        <svg class="w-6 h-6 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-zinc-300 text-sm font-medium">Pilih file CSV</p>
                        <p class="text-zinc-500 text-xs mt-0.5">atau drag &amp; drop ke sini</p>
                    </div>

                    <input type="file"
                        wire:model="csvFile"
                        accept=".csv,.txt"
                        class="mt-2 block w-full max-w-xs text-sm text-zinc-400
                                file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0
                                file:text-sm file:font-medium file:bg-violet-600/20 file:text-violet-300
                                hover:file:bg-violet-600/30 cursor-pointer" />

                    @error('csvFile')
                    <p class="text-rose-400 text-xs">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            @if($csvFile)
            <div class="flex items-center gap-2 text-sm text-zinc-300 bg-zinc-800 rounded-lg px-3 py-2">
                <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="truncate">{{ $csvFile->getClientOriginalName() }}</span>
                <span class="text-zinc-500 text-xs ml-auto">{{ number_format($csvFile->getSize() / 1024, 1) }} KB</span>
            </div>
            @endif

            <div class="flex justify-end">
                <button type="submit" class="button button--primary inline-flex items-center gap-1.5" :disabled="!$csvFile">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    <span wire:loading.remove wire:target="import">Mulai Import</span>
                    <span wire:loading wire:target="import">Sedang memproses...</span>
                </button>
            </div>
        </form>
        @endif

        {{-- ── Result ─────────────────────────────────────────────────────── --}}
        @if($status === 'done')
        <div class="space-y-4">
            <div class="flex items-center gap-3 p-4 bg-emerald-950/50 border border-emerald-800/50 rounded-xl">
                <div class="w-9 h-9 rounded-full bg-emerald-500/20 flex items-center justify-center flex-none">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div>
                    <p class="text-emerald-300 font-semibold">Import Berhasil</p>
                    <p class="text-emerald-400/80 text-sm">
                        {{ $imported }} dari {{ $total }} baris berhasil diimpor.
                    </p>
                </div>
            </div>

            @if(count($errors) > 0)
            <div class="bg-zinc-800 rounded-xl p-4">
                <p class="text-zinc-300 text-sm font-medium mb-2">⚠️ {{ count($errors) }} baris gagal:</p>
                <ul class="space-y-1">
                    @foreach($errors as $err)
                    <li class="text-zinc-400 text-xs">• {{ $err }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="flex gap-3">
                <button type="button" wire:click="resetImport" class="button button--ghost button--neutral inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/>
                    </svg>
                    <span>Import Lagi</span>
                </button>
                <a href="{{ route('finance.transactions') }}" wire:navigate class="button button--primary inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                    <span>Lihat Transaksi</span>
                </a>
            </div>
        </div>
        @endif

        @if($status === 'error')
        <div class="space-y-4">
            <div class="flex items-center gap-3 p-4 bg-rose-950/50 border border-rose-800/50 rounded-xl">
                <div class="w-9 h-9 rounded-full bg-rose-500/20 flex items-center justify-center flex-none">
                    <svg class="w-5 h-5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
                <div>
                    <p class="text-rose-300 font-semibold">Import Gagal</p>
                    @foreach($errors as $err)
                    <p class="text-rose-400/80 text-sm">{{ $err }}</p>
                    @endforeach
                </div>
            </div>
            <button type="button" wire:click="resetImport" class="button button--ghost button--neutral inline-flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/>
                </svg>
                <span>Coba Lagi</span>
            </button>
        </div>
        @endif
    </div>

</div>
