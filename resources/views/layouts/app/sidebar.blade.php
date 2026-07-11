<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    </head>
    <body class="bg-zinc-950 text-zinc-100 font-sans antialiased overflow-hidden"
          x-data="{
              showAddMenu: false,
              toasts: [],
              showPinLock: {{ auth()->user()?->pin && !session('pin_unlocked') ? 'true' : 'false' }},
              pinInput: '',
              pinError: '',
              pinAttempts: 0,
              addToast(message, type = 'success') {
                  const id = Date.now();
                  this.toasts.push({ id, message, type });
                  setTimeout(() => this.removeToast(id), 4000);
              },
              removeToast(id) {
                  this.toasts = this.toasts.filter(t => t.id !== id);
              },
          }"
          @notify.window="addToast($event.detail.message, $event.detail.type)">

        {{-- ══════════════════════════════════════════════
             PIN LOCK SCREEN OVERLAY
        ══════════════════════════════════════════════ --}}
        <div x-show="showPinLock"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             class="fixed inset-0 z-[200] bg-zinc-950 flex flex-col items-center justify-center px-6"
             style="{{ auth()->user()?->pin && !session('pin_unlocked') ? '' : 'display:none;' }}">
            <div class="w-full max-w-xs flex flex-col items-center gap-6">
                {{-- Brand --}}
                <div class="flex flex-col items-center gap-2">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-violet-600 to-indigo-700 flex items-center justify-center shadow-lg shadow-violet-900/50">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="text-white font-bold text-xl tracking-tight">FinArka</p>
                    <p class="text-zinc-400 text-sm">Masukkan PIN 4 digit Anda</p>
                </div>

                {{-- PIN dots display --}}
                <div class="flex items-center gap-3">
                    <template x-for="i in [1,2,3,4]" :key="i">
                        <div class="w-4 h-4 rounded-full border-2 transition-all duration-200"
                             :class="pinInput.length >= i ? 'bg-violet-500 border-violet-500 scale-110' : 'border-zinc-600 bg-transparent'">
                        </div>
                    </template>
                </div>

                {{-- Error --}}
                <p class="text-rose-400 text-xs text-center" x-show="pinError" x-text="pinError"></p>

                {{-- Numeric keypad --}}
                <div class="grid grid-cols-3 gap-3 w-full">
                    <template x-for="digit in ['1','2','3','4','5','6','7','8','9','','0','⌫']" :key="digit">
                        <button
                            type="button"
                            class="h-14 rounded-2xl text-xl font-semibold transition-all duration-150 active:scale-90"
                            :class="digit === '' ? 'invisible' :
                                    digit === '⌫' ? 'bg-zinc-800 text-zinc-300 hover:bg-zinc-700' :
                                    'bg-zinc-800 text-white hover:bg-zinc-700'"
                            @click="
                                if (digit === '⌫') {
                                    pinInput = pinInput.slice(0, -1);
                                    pinError = '';
                                } else if (digit !== '' && pinInput.length < 4) {
                                    pinInput += digit;
                                    pinError = '';
                                    if (pinInput.length === 4) {
                                        $wire.call !== undefined ? null : null;
                                        fetch('{{ route('pin.verify') }}', {
                                            method: 'POST',
                                            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content},
                                            body: JSON.stringify({pin: pinInput})
                                        }).then(r => r.json()).then(data => {
                                            if (data.success) {
                                                showPinLock = false;
                                            } else {
                                                pinError = 'PIN salah. Coba lagi.';
                                                pinInput = '';
                                                pinAttempts++;
                                            }
                                        });
                                    }
                                }
                            "
                            x-text="digit">
                        </button>
                    </template>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════
             MAIN MOBILE APP FRAME
        ══════════════════════════════════════════════ --}}
        <div class="fixed inset-0 flex flex-col bg-zinc-950 overflow-hidden"
             x-show="!showPinLock">

            {{-- Top Header Bar --}}
            <header class="flex-none flex items-center justify-between px-4 py-3 bg-zinc-900 border-b border-zinc-800/70 z-30">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-violet-600 to-indigo-700 flex items-center justify-center flex-none">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span class="text-white font-bold text-base tracking-tight">FinArka</span>
                </div>

                {{-- User avatar / profile menu --}}
                <div class="relative" x-data="{ openMenu: false }">
                    <button type="button"
                            class="w-8 h-8 rounded-full bg-zinc-800 flex items-center justify-center hover:ring-2 hover:ring-violet-500 transition-all"
                            @click="openMenu = !openMenu">
                        <span class="text-zinc-300 text-xs font-bold">{{ auth()->user()?->initials() }}</span>
                    </button>

                    <div class="absolute right-0 top-10 bg-zinc-900 border border-zinc-800 rounded-2xl shadow-2xl w-52 py-2 z-50"
                         x-show="openMenu"
                         @click.outside="openMenu = false"
                         x-transition
                         style="display:none;">
                        <div class="flex flex-col px-3 py-2 border-b border-zinc-800 mb-1">
                            <span class="text-white text-xs font-semibold truncate">{{ auth()->user()?->name }}</span>
                            <span class="text-zinc-500 text-[10px] truncate mt-0.5">{{ auth()->user()?->email }}</span>
                        </div>
                        <a href="{{ route('finance.settings') }}" wire:navigate
                           class="flex items-center gap-2 px-3 py-2 text-xs text-zinc-300 hover:bg-zinc-800 hover:text-white transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065zM12 15a3 3 0 100-6 3 3 0 000 6z"/>
                            </svg>
                            Pengaturan
                        </a>
                        <div class="border-t border-zinc-800 my-1"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="flex items-center gap-2 w-full px-3 py-2 text-xs text-rose-400 hover:bg-rose-500/10 transition-colors text-left">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            {{-- Page Content (scrollable) --}}
            <main class="flex-1 overflow-y-auto overflow-x-hidden pb-20">
                {{ $slot }}
            </main>

            {{-- ══════════════════════════════════════════════
                 BOTTOM NAVIGATION BAR
            ══════════════════════════════════════════════ --}}
            <nav class="flex-none bg-zinc-900/95 backdrop-blur-md border-t border-zinc-800/70 safe-area-bottom z-40">
                <div class="flex items-stretch h-16">

                    {{-- 1. Transaksi --}}
                    <a href="{{ route('finance.transactions') }}"
                       wire:navigate
                       class="flex-1 flex flex-col items-center justify-center gap-0.5 transition-all duration-150 group relative"
                       :class="'{{ request()->routeIs('finance.transactions') }}'==='1' ? 'text-violet-400' : 'text-zinc-500'">
                        @if(request()->routeIs('finance.transactions'))
                        <span class="absolute top-0 left-1/2 -translate-x-1/2 w-8 h-0.5 bg-violet-500 rounded-b-full"></span>
                        @endif
                        <svg class="w-5 h-5 {{ request()->routeIs('finance.transactions') ? 'text-violet-400' : 'text-zinc-500 group-hover:text-zinc-300' }} transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <span class="text-[10px] font-medium {{ request()->routeIs('finance.transactions') ? 'text-violet-400' : 'text-zinc-500 group-hover:text-zinc-300' }} transition-colors">Transaksi</span>
                    </a>

                    {{-- 2. Report --}}
                    <a href="{{ route('finance.reports') }}"
                       wire:navigate
                       class="flex-1 flex flex-col items-center justify-center gap-0.5 transition-all duration-150 group relative">
                        @if(request()->routeIs('finance.reports'))
                        <span class="absolute top-0 left-1/2 -translate-x-1/2 w-8 h-0.5 bg-violet-500 rounded-b-full"></span>
                        @endif
                        <svg class="w-5 h-5 {{ request()->routeIs('finance.reports') ? 'text-violet-400' : 'text-zinc-500 group-hover:text-zinc-300' }} transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <span class="text-[10px] font-medium {{ request()->routeIs('finance.reports') ? 'text-violet-400' : 'text-zinc-500 group-hover:text-zinc-300' }} transition-colors">Laporan</span>
                    </a>

                    {{-- 3. Add Trx (Center FAB) --}}
                    <div class="flex-1 flex flex-col items-center justify-center relative">
                        <button type="button"
                                id="btn-add-trx"
                                class="w-12 h-12 -mt-5 rounded-full bg-gradient-to-br from-violet-600 to-indigo-700 flex items-center justify-center shadow-lg shadow-violet-900/50 hover:shadow-violet-700/60 active:scale-95 transition-all duration-150 ring-2 ring-zinc-900"
                                @click="showAddMenu = !showAddMenu">
                            <svg class="w-5 h-5 text-white transition-transform duration-200"
                                 :class="showAddMenu ? 'rotate-45' : 'rotate-0'"
                                 fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                        </button>
                        <span class="text-[10px] font-medium text-zinc-500 mt-1">Tambah</span>
                    </div>

                    {{-- 4. Hutang --}}
                    <a href="{{ route('finance.hutang') }}"
                       wire:navigate
                       class="flex-1 flex flex-col items-center justify-center gap-0.5 transition-all duration-150 group relative">
                        @if(request()->routeIs('finance.hutang'))
                        <span class="absolute top-0 left-1/2 -translate-x-1/2 w-8 h-0.5 bg-violet-500 rounded-b-full"></span>
                        @endif
                        <svg class="w-5 h-5 {{ request()->routeIs('finance.hutang') ? 'text-violet-400' : 'text-zinc-500 group-hover:text-zinc-300' }} transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span class="text-[10px] font-medium {{ request()->routeIs('finance.hutang') ? 'text-violet-400' : 'text-zinc-500 group-hover:text-zinc-300' }} transition-colors">Hutang</span>
                    </a>

                    {{-- 5. Setting --}}
                    <a href="{{ route('finance.settings') }}"
                       wire:navigate
                       class="flex-1 flex flex-col items-center justify-center gap-0.5 transition-all duration-150 group relative">
                        @if(request()->routeIs('finance.settings'))
                        <span class="absolute top-0 left-1/2 -translate-x-1/2 w-8 h-0.5 bg-violet-500 rounded-b-full"></span>
                        @endif
                        <svg class="w-5 h-5 {{ request()->routeIs('finance.settings') ? 'text-violet-400' : 'text-zinc-500 group-hover:text-zinc-300' }} transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065zM12 15a3 3 0 100-6 3 3 0 000 6z"/>
                        </svg>
                        <span class="text-[10px] font-medium {{ request()->routeIs('finance.settings') ? 'text-violet-400' : 'text-zinc-500 group-hover:text-zinc-300' }} transition-colors">Pengaturan</span>
                    </a>
                </div>
            </nav>
        </div>

        {{-- ══════════════════════════════════════════════
             ADD TRX BOTTOM SHEET / DROP-UP MENU
        ══════════════════════════════════════════════ --}}
        {{-- Backdrop --}}
        <div class="fixed inset-0 z-[90] bg-zinc-950/70 backdrop-blur-sm"
             x-show="showAddMenu"
             @click="showAddMenu = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             style="display:none;"></div>

        {{-- Drop-up options --}}
        <div class="fixed bottom-16 inset-x-0 z-[100] flex flex-col items-center gap-3 px-4 pb-4 pointer-events-none"
             x-show="showAddMenu"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-4"
             style="display:none;">

            {{-- Create Hutang --}}
            <a href="{{ route('finance.hutang') }}?action=create"
               wire:navigate
               @click="showAddMenu = false"
               class="pointer-events-auto flex items-center gap-3 w-full max-w-sm bg-zinc-900 border border-zinc-700/80 rounded-2xl px-4 py-3 shadow-xl hover:bg-zinc-800 active:scale-95 transition-all duration-150">
                <div class="w-10 h-10 rounded-xl bg-amber-500/15 border border-amber-500/30 flex items-center justify-center flex-none">
                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-white text-sm font-semibold">Catat Hutang</p>
                    <p class="text-zinc-400 text-xs">Beri pinjaman atau terima hutang</p>
                </div>
            </a>

            {{-- Transfer Saldo --}}
            <a href="{{ route('finance.transactions') }}?action=transfer"
               wire:navigate
               @click="showAddMenu = false"
               class="pointer-events-auto flex items-center gap-3 w-full max-w-sm bg-zinc-900 border border-zinc-700/80 rounded-2xl px-4 py-3 shadow-xl hover:bg-zinc-800 active:scale-95 transition-all duration-150">
                <div class="w-10 h-10 rounded-xl bg-sky-500/15 border border-sky-500/30 flex items-center justify-center flex-none">
                    <svg class="w-5 h-5 text-sky-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                </div>
                <div>
                    <p class="text-white text-sm font-semibold">Transfer Saldo</p>
                    <p class="text-zinc-400 text-xs">Pindahkan saldo antar rekening</p>
                </div>
            </a>

            {{-- Add Transaction --}}
            <a href="{{ route('finance.transactions') }}?action=create"
               wire:navigate
               @click="showAddMenu = false"
               class="pointer-events-auto flex items-center gap-3 w-full max-w-sm bg-zinc-900 border border-zinc-700/80 rounded-2xl px-4 py-3 shadow-xl hover:bg-zinc-800 active:scale-95 transition-all duration-150">
                <div class="w-10 h-10 rounded-xl bg-violet-500/15 border border-violet-500/30 flex items-center justify-center flex-none">
                    <svg class="w-5 h-5 text-violet-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
                <div>
                    <p class="text-white text-sm font-semibold">Catat Transaksi</p>
                    <p class="text-zinc-400 text-xs">Pemasukan atau pengeluaran</p>
                </div>
            </a>
        </div>

        {{-- ══════════════════════════════════════════════
             TOAST NOTIFICATIONS
        ══════════════════════════════════════════════ --}}
        <div class="fixed top-16 left-0 right-0 z-[150] flex flex-col items-center gap-2 px-4 pt-2 pointer-events-none">
            <template x-for="t in toasts" :key="t.id">
                <div class="pointer-events-auto w-full max-w-sm flex items-center gap-3 px-4 py-3 rounded-2xl border shadow-xl"
                     :class="{
                         'bg-emerald-950/95 border-emerald-800/80 text-emerald-300': t.type === 'success',
                         'bg-rose-950/95 border-rose-800/80 text-rose-300': t.type === 'danger' || t.type === 'error',
                         'bg-amber-950/95 border-amber-800/80 text-amber-300': t.type === 'warning',
                         'bg-zinc-900/95 border-zinc-700/80 text-zinc-300': t.type === 'info' || !['success','danger','error','warning'].includes(t.type)
                     }"
                     x-transition>
                    <svg class="w-4 h-4 flex-none"
                         :class="{
                             'text-emerald-400': t.type === 'success',
                             'text-rose-400': t.type === 'danger' || t.type === 'error',
                             'text-amber-400': t.type === 'warning',
                             'text-zinc-400': t.type === 'info'
                         }"
                         fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path x-show="t.type === 'success'" stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        <path x-show="t.type === 'danger' || t.type === 'error'" stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        <path x-show="t.type === 'warning'" stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        <path x-show="!['success','danger','error','warning'].includes(t.type)" stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-sm font-medium flex-1" x-text="t.message"></span>
                    <button @click="removeToast(t.id)" class="ml-1 hover:opacity-70 text-current transition-opacity">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </template>
        </div>

    </body>
</html>
