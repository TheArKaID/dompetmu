<div class="min-h-screen pb-4">

    {{-- ── Sticky Header ────────────────────────────────────────────────────────── --}}
    <div class="sticky top-0 z-20 bg-zinc-900/95 backdrop-blur-md border-b border-zinc-800/70 px-4 py-3">
        <h1 class="text-base font-bold text-white">Pengaturan</h1>
    </div>

    {{-- ── Section: Main --}}
    @if($section === 'main')
    <div class="px-4 pt-4 space-y-3">

        {{-- Account info card --}}
        <div class="bg-gradient-to-br from-violet-700/30 to-indigo-800/30 border border-violet-700/30 rounded-2xl p-4 flex items-center gap-3">
            <div class="w-12 h-12 rounded-full bg-violet-600/30 border border-violet-500/40 flex items-center justify-center flex-none">
                <span class="text-violet-300 font-bold text-base">{{ auth()->user()->initials() }}</span>
            </div>
            <div>
                <p class="text-white font-semibold">{{ auth()->user()->name }}</p>
                <p class="text-violet-300 text-xs">{{ auth()->user()->email }}</p>
            </div>
        </div>

        {{-- Keuangan section --}}
        <p class="text-zinc-500 text-[10px] font-bold uppercase tracking-widest px-1 pt-1">Data Keuangan</p>

        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl overflow-hidden divide-y divide-zinc-800">
            <button type="button" wire:click="setSection('rekening')"
                    class="w-full flex items-center gap-3 px-4 py-3.5 hover:bg-zinc-800 transition-colors text-left">
                <div class="w-8 h-8 rounded-xl bg-sky-500/15 border border-sky-500/25 flex items-center justify-center flex-none">
                    <svg class="w-4 h-4 text-sky-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-white text-sm font-medium">Master Rekening</p>
                    <p class="text-zinc-500 text-xs">Kelola daftar rekening/dompet</p>
                </div>
                <svg class="w-4 h-4 text-zinc-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </button>

            <button type="button" wire:click="setSection('kategori')"
                    class="w-full flex items-center gap-3 px-4 py-3.5 hover:bg-zinc-800 transition-colors text-left">
                <div class="w-8 h-8 rounded-xl bg-emerald-500/15 border border-emerald-500/25 flex items-center justify-center flex-none">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.562 3.224l10.536 10.536a2.182 2.182 0 010 3.086l-4.14 4.14a2.182 2.182 0 01-3.086 0L2.336 10.45A2.182 2.182 0 011.664 8.9V3.8c0-1.1.9-2 2-2h5.1c.6 0 1.2.224 1.664.674zM6.5 6.5h.01"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-white text-sm font-medium">Kategori</p>
                    <p class="text-zinc-500 text-xs">Kelola kategori pemasukan & pengeluaran</p>
                </div>
                <svg class="w-4 h-4 text-zinc-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>

        {{-- Keamanan & Data --}}
        <p class="text-zinc-500 text-[10px] font-bold uppercase tracking-widest px-1 pt-2">Keamanan & Data</p>

        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl overflow-hidden divide-y divide-zinc-800">
            <button type="button" wire:click="setSection('pin')"
                    class="w-full flex items-center gap-3 px-4 py-3.5 hover:bg-zinc-800 transition-colors text-left">
                <div class="w-8 h-8 rounded-xl bg-violet-500/15 border border-violet-500/25 flex items-center justify-center flex-none">
                    <svg class="w-4 h-4 text-violet-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-white text-sm font-medium">Keamanan PIN</p>
                    <p class="text-zinc-500 text-xs">{{ $pinEnabled ? 'PIN aktif — klik untuk ubah atau hapus' : 'Aktifkan PIN untuk keamanan' }}</p>
                </div>
                <div class="flex items-center gap-2">
                    @if($pinEnabled)
                    <span class="text-[9px] font-semibold text-emerald-400 bg-emerald-500/20 border border-emerald-500/30 px-1.5 py-0.5 rounded-md">Aktif</span>
                    @endif
                    <svg class="w-4 h-4 text-zinc-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </button>

            <button type="button" wire:click="setSection('import')"
                    class="w-full flex items-center gap-3 px-4 py-3.5 hover:bg-zinc-800 transition-colors text-left">
                <div class="w-8 h-8 rounded-xl bg-amber-500/15 border border-amber-500/25 flex items-center justify-center flex-none">
                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-white text-sm font-medium">Export & Import</p>
                    <p class="text-zinc-500 text-xs">Import CSV atau export data</p>
                </div>
                <svg class="w-4 h-4 text-zinc-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>

        {{-- Akun --}}
        <p class="text-zinc-500 text-[10px] font-bold uppercase tracking-widest px-1 pt-2">Akun</p>

        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl overflow-hidden divide-y divide-zinc-800">
            <a href="{{ route('profile.edit') }}" wire:navigate
               class="flex items-center gap-3 px-4 py-3.5 hover:bg-zinc-800 transition-colors">
                <div class="w-8 h-8 rounded-xl bg-zinc-700 flex items-center justify-center flex-none">
                    <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <p class="text-white text-sm font-medium flex-1">Profil & Password</p>
                <svg class="w-4 h-4 text-zinc-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full flex items-center gap-3 px-4 py-3.5 hover:bg-rose-500/10 transition-colors text-left">
                    <div class="w-8 h-8 rounded-xl bg-rose-500/15 border border-rose-500/25 flex items-center justify-center flex-none">
                        <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </div>
                    <p class="text-rose-400 text-sm font-medium flex-1">Keluar</p>
                </button>
            </form>
        </div>

        <p class="text-zinc-700 text-[10px] text-center pt-2">FinArka v1.0 · Personal Finance</p>
    </div>
    @endif

    {{-- ── Section: Rekening ────────────────────────────────────────────────────── --}}
    @if($section === 'rekening')
    <div class="px-4 pt-3">
        <button type="button" wire:click="setSection('main')"
                class="flex items-center gap-1.5 text-zinc-400 hover:text-white text-sm mb-4 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali
        </button>
        @livewire('finance.accounts.index')
    </div>
    @endif

    {{-- ── Section: Kategori ────────────────────────────────────────────────────── --}}
    @if($section === 'kategori')
    <div class="px-4 pt-3">
        <button type="button" wire:click="setSection('main')"
                class="flex items-center gap-1.5 text-zinc-400 hover:text-white text-sm mb-4 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali
        </button>
        @livewire('finance.categories.index')
    </div>
    @endif

    {{-- ── Section: PIN ─────────────────────────────────────────────────────────── --}}
    @if($section === 'pin')
    <div class="px-4 pt-3">
        <button type="button" wire:click="setSection('main')"
                class="flex items-center gap-1.5 text-zinc-400 hover:text-white text-sm mb-4 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali
        </button>

        <div class="space-y-4">
            @if(!$pinEnabled)
            {{-- Set new PIN --}}
            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-4 space-y-4">
                <div class="flex items-center gap-3 mb-1">
                    <div class="w-10 h-10 rounded-xl bg-violet-500/15 border border-violet-500/25 flex items-center justify-center">
                        <svg class="w-5 h-5 text-violet-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-white font-semibold">Aktifkan PIN</p>
                        <p class="text-zinc-400 text-xs">Tambahkan lapisan keamanan 4 digit</p>
                    </div>
                </div>

                <div>
                    <label class="text-zinc-400 text-xs font-medium block mb-1">PIN Baru (4 digit)</label>
                    <input type="password" wire:model="newPin" placeholder="••••" maxlength="4" inputmode="numeric"
                           class="w-full bg-zinc-800 border border-zinc-700 text-zinc-200 text-center text-xl tracking-widest rounded-xl px-3 py-2.5 outline-none focus:ring-1 focus:ring-violet-500">
                    @error('newPin') <p class="text-rose-400 text-xs mt-0.5">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-zinc-400 text-xs font-medium block mb-1">Konfirmasi PIN</label>
                    <input type="password" wire:model="newPinConfirm" placeholder="••••" maxlength="4" inputmode="numeric"
                           class="w-full bg-zinc-800 border border-zinc-700 text-zinc-200 text-center text-xl tracking-widest rounded-xl px-3 py-2.5 outline-none focus:ring-1 focus:ring-violet-500">
                    @error('newPinConfirm') <p class="text-rose-400 text-xs mt-0.5">{{ $message }}</p> @enderror
                </div>

                <button type="button" wire:click="savePin"
                        class="w-full py-2.5 rounded-xl bg-violet-600 hover:bg-violet-700 text-white text-sm font-semibold transition-colors active:scale-95">
                    Aktifkan PIN
                </button>
            </div>
            @else
            {{-- PIN is active —show change/remove --}}
            <div class="bg-emerald-950/50 border border-emerald-800/40 rounded-2xl p-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center flex-none">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div>
                    <p class="text-emerald-300 font-semibold">PIN Aktif</p>
                    <p class="text-emerald-400/70 text-xs">Aplikasi akan terkunci saat dibuka</p>
                </div>
            </div>

            {{-- Change PIN --}}
            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-4 space-y-3">
                <p class="text-white font-semibold text-sm">Ganti PIN</p>
                <div>
                    <label class="text-zinc-400 text-xs font-medium block mb-1">PIN Baru</label>
                    <input type="password" wire:model="newPin" placeholder="••••" maxlength="4" inputmode="numeric"
                           class="w-full bg-zinc-800 border border-zinc-700 text-zinc-200 text-center text-xl tracking-widest rounded-xl px-3 py-2.5 outline-none focus:ring-1 focus:ring-violet-500">
                </div>
                <div>
                    <label class="text-zinc-400 text-xs font-medium block mb-1">Konfirmasi PIN Baru</label>
                    <input type="password" wire:model="newPinConfirm" placeholder="••••" maxlength="4" inputmode="numeric"
                           class="w-full bg-zinc-800 border border-zinc-700 text-zinc-200 text-center text-xl tracking-widest rounded-xl px-3 py-2.5 outline-none focus:ring-1 focus:ring-violet-500">
                </div>
                <button type="button" wire:click="savePin"
                        class="w-full py-2 rounded-xl bg-violet-600 hover:bg-violet-700 text-white text-sm font-semibold transition-colors">
                    Simpan PIN Baru
                </button>
            </div>

            {{-- Remove PIN --}}
            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-4 space-y-3">
                <p class="text-white font-semibold text-sm">Hapus PIN</p>
                <div>
                    <label class="text-zinc-400 text-xs font-medium block mb-1">Masukkan PIN saat ini</label>
                    <input type="password" wire:model="currentPin" placeholder="••••" maxlength="4" inputmode="numeric"
                           class="w-full bg-zinc-800 border border-zinc-700 text-zinc-200 text-center text-xl tracking-widest rounded-xl px-3 py-2.5 outline-none focus:ring-1 focus:ring-rose-500">
                    @error('currentPin') <p class="text-rose-400 text-xs mt-0.5">{{ $message }}</p> @enderror
                </div>
                <button type="button" wire:click="removePin"
                        class="w-full py-2 rounded-xl border border-rose-700/50 text-rose-400 hover:bg-rose-500/10 text-sm font-semibold transition-colors">
                    Hapus PIN
                </button>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- ── Section: Import ─────────────────────────────────────────────────────── --}}
    @if($section === 'import')
    <div class="px-4 pt-3">
        <button type="button" wire:click="setSection('main')"
                class="flex items-center gap-1.5 text-zinc-400 hover:text-white text-sm mb-4 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali
        </button>
        @livewire('finance.import.index')
    </div>
    @endif

</div>
