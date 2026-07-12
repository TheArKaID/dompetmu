<div class="min-h-screen pb-4">

    {{-- Sticky Header --}}
    <div class="sticky top-0 z-20 bg-zinc-900/95 backdrop-blur-md border-b border-zinc-800/70 px-4 py-3 flex items-center gap-3">
        <a href="{{ url()->previous() }}" class="w-8 h-8 rounded-xl bg-zinc-800 flex items-center justify-center hover:bg-zinc-700 transition-colors flex-none">
            <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-base font-bold text-white">Keamanan Akun</h1>
    </div>

    <div class="px-4 pt-4 space-y-4">

        {{-- Password success --}}
        @if (session('status') === 'password-updated')
        <div class="flex items-center gap-2 px-3 py-2 bg-emerald-900/40 border border-emerald-700/40 rounded-xl text-emerald-300 text-xs">
            <svg class="w-4 h-4 flex-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
            Password berhasil diperbarui.
        </div>
        @endif

        {{-- Change Password Card --}}
        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-4">
            <p class="text-zinc-400 text-xs font-semibold uppercase tracking-widest mb-3">Ubah Password</p>
            <form method="POST" wire:submit="updatePassword" class="space-y-3">
                @csrf
                <div>
                    <label for="current_password" class="text-zinc-400 text-xs font-medium block mb-1.5">Password Saat Ini</label>
                    <input id="current_password" type="password" wire:model="current_password" required autocomplete="current-password"
                           class="w-full bg-zinc-800 border border-zinc-700 text-zinc-200 text-sm rounded-xl px-3 py-2.5 outline-none focus:ring-1 focus:ring-violet-500">
                    @error('current_password')
                    <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="new_password" class="text-zinc-400 text-xs font-medium block mb-1.5">Password Baru</label>
                    <input id="new_password" type="password" wire:model="password" required autocomplete="new-password"
                           class="w-full bg-zinc-800 border border-zinc-700 text-zinc-200 text-sm rounded-xl px-3 py-2.5 outline-none focus:ring-1 focus:ring-violet-500">
                    @error('password')
                    <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="text-zinc-400 text-xs font-medium block mb-1.5">Konfirmasi Password Baru</label>
                    <input id="password_confirmation" type="password" wire:model="password_confirmation" required autocomplete="new-password"
                           class="w-full bg-zinc-800 border border-zinc-700 text-zinc-200 text-sm rounded-xl px-3 py-2.5 outline-none focus:ring-1 focus:ring-violet-500">
                    @error('password_confirmation')
                    <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                        class="w-full py-2.5 rounded-xl bg-violet-600 hover:bg-violet-700 text-white text-sm font-semibold transition-colors active:scale-95 flex items-center justify-center gap-2">
                    <span wire:loading.remove wire:target="updatePassword">Simpan Password</span>
                    <span wire:loading wire:target="updatePassword">Menyimpan...</span>
                </button>
            </form>
        </div>

        {{-- Two-Factor Authentication --}}
        @if ($canManageTwoFactor)
        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-4" wire:cloak>
            <p class="text-zinc-400 text-xs font-semibold uppercase tracking-widest mb-3">Autentikasi Dua Faktor (2FA)</p>

            @if ($twoFactorEnabled)
                <div class="space-y-3">
                    <div class="flex items-center gap-3 p-3 bg-emerald-950/50 border border-emerald-800/40 rounded-xl">
                        <div class="w-8 h-8 rounded-full bg-emerald-500/20 flex items-center justify-center flex-none">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-emerald-300 text-xs font-semibold">2FA Aktif</p>
                            <p class="text-emerald-400/70 text-[10px]">Akun terlindungi dengan kode TOTP</p>
                        </div>
                    </div>
                    <p class="text-zinc-400 text-xs leading-normal">
                        Kamu akan diminta kode keamanan saat login. Kode dapat diambil dari aplikasi TOTP di ponselmu.
                    </p>
                    <button type="button" wire:click="disable"
                            class="w-full py-2.5 rounded-xl border border-rose-700/50 text-rose-400 hover:bg-rose-500/10 text-sm font-semibold transition-colors">
                        Nonaktifkan 2FA
                    </button>

                    <livewire:settings.two-factor.recovery-codes :$requiresConfirmation/>
                </div>
            @else
                <div class="space-y-3">
                    <p class="text-zinc-400 text-xs leading-normal">
                        Aktifkan 2FA untuk perlindungan ekstra. Kamu akan diminta kode TOTP dari ponselmu setiap login.
                    </p>
                    <button type="button" wire:click="enable"
                            class="w-full py-2.5 rounded-xl bg-violet-600 hover:bg-violet-700 text-white text-sm font-semibold transition-colors active:scale-95">
                        Aktifkan 2FA
                    </button>
                </div>
            @endif
        </div>

        {{-- 2FA Setup Bottom Sheet Backdrop --}}
        <div class="fixed inset-0 z-[80] bg-zinc-950/70 backdrop-blur-sm"
             x-show="$wire.showModal"
             @click="$wire.closeModal()"
             x-transition style="display:none;"></div>

        {{-- 2FA Setup Bottom Sheet Panel --}}
        <div class="fixed bottom-0 inset-x-0 z-[90] bg-zinc-900 border-t border-zinc-800 rounded-t-3xl shadow-2xl p-4 space-y-4"
             x-show="$wire.showModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-y-full"
             x-transition:enter-end="translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-y-0"
             x-transition:leave-end="translate-y-full"
             style="display:none;">

            <div class="flex justify-center mb-1">
                <div class="w-10 h-1 rounded-full bg-zinc-700"></div>
            </div>

            @if($showModal)
            <div class="space-y-4">
                <div class="text-center space-y-1">
                    <h4 class="text-white font-bold text-base">{{ $this->modalConfig['title'] }}</h4>
                    <p class="text-zinc-400 text-xs leading-normal">{{ $this->modalConfig['description'] }}</p>
                </div>

                @if ($showVerificationStep)
                    <div class="space-y-4">
                        <div>
                            <label for="otp_code" class="text-zinc-400 text-xs font-medium block mb-1.5">Kode Verifikasi (6 digit)</label>
                            <input id="otp_code" type="text" wire:model="code" maxlength="6" inputmode="numeric" placeholder="123456"
                                   class="w-full bg-zinc-800 border border-zinc-700 text-zinc-200 text-center text-xl tracking-widest rounded-xl px-3 py-2.5 outline-none focus:ring-1 focus:ring-violet-500">
                            @error('code')
                            <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex gap-3">
                            <button type="button" wire:click="resetVerification"
                                    class="flex-1 py-2.5 rounded-xl border border-zinc-700 text-zinc-300 text-sm font-medium hover:bg-zinc-800 transition-colors">
                                Kembali
                            </button>
                            <button type="button" wire:click="confirmTwoFactor"
                                    class="flex-1 py-2.5 rounded-xl bg-violet-600 hover:bg-violet-700 text-white text-sm font-semibold transition-colors active:scale-95">
                                Konfirmasi
                            </button>
                        </div>
                    </div>
                @else
                    @error('setupData')
                        <div class="p-3 bg-rose-500/10 border border-rose-500/20 text-rose-400 rounded-xl text-xs">{{ $message }}</div>
                    @enderror

                    <div class="flex justify-center py-1">
                        <div class="w-48 h-48 border border-zinc-700 rounded-2xl bg-white p-3 flex items-center justify-center">
                            @empty($qrCodeSvg)
                                <div class="text-zinc-400 text-xs animate-pulse">Memuat QR Code...</div>
                            @else
                                <div class="bg-white p-1 rounded">{!! $qrCodeSvg !!}</div>
                            @endempty
                        </div>
                    </div>

                    <button type="button" wire:click="showVerificationIfNecessary"
                            class="w-full py-2.5 rounded-xl bg-violet-600 hover:bg-violet-700 text-white text-sm font-semibold transition-colors active:scale-95 flex items-center justify-center">
                        {{ $this->modalConfig['buttonText'] }}
                    </button>

                    <div class="space-y-2">
                        <div class="relative flex items-center justify-center">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-zinc-800"></div>
                            </div>
                            <span class="relative px-2 text-xs bg-zinc-900 text-zinc-500">atau masukkan kode manual</span>
                        </div>
                        <div class="flex items-stretch border border-zinc-700 rounded-xl overflow-hidden bg-zinc-800"
                             x-data="{
                                 copied: false,
                                 async copy() {
                                     try {
                                         await navigator.clipboard.writeText('{{ $manualSetupKey }}');
                                         this.copied = true;
                                         setTimeout(() => this.copied = false, 1500);
                                     } catch (e) {}
                                 }
                             }">
                            @empty($manualSetupKey)
                                <span class="w-full px-3 py-2.5 text-zinc-500 text-xs italic">Generating...</span>
                            @else
                                <input type="text" readonly value="{{ $manualSetupKey }}"
                                       class="flex-1 min-w-0 px-3 py-2.5 bg-transparent outline-none text-zinc-200 text-xs font-mono">
                                <button type="button" @click="copy()"
                                        class="px-3 border-l border-zinc-700 text-zinc-400 hover:text-white transition-colors text-xs font-medium flex-none">
                                    <span x-show="!copied">Salin</span>
                                    <span x-show="copied" class="text-emerald-400">✓</span>
                                </button>
                            @endempty
                        </div>
                    </div>
                @endif
            </div>
            @endif
        </div>
        @endif

    </div>
</div>
