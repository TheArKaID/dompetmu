<div
    class="py-6 space-y-6 border shadow-sm rounded-xl border-zinc-200 dark:border-white/10"
    wire:cloak
    x-data="{ showRecoveryCodes: false }"
>
    <div class="px-6 space-y-2">
        <div class="flex items-center gap-2">
            <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            <h4 class="text-sm font-semibold text-white">{{ __('2FA recovery codes') }}</h4>
        </div>
        <p class="text-xs text-zinc-400">
            {{ __('Recovery codes let you regain access if you lose your 2FA device. Store them in a secure password manager.') }}
        </p>
    </div>

    <div class="px-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <button type="button"
                x-show="!showRecoveryCodes"
                class="py-2 px-4 rounded-xl bg-violet-600 hover:bg-violet-700 text-white text-xs font-semibold transition-colors"
                @click="showRecoveryCodes = true;">
                {{ __('View recovery codes') }}
            </button>

            <button type="button"
                x-show="showRecoveryCodes"
                class="py-2 px-4 rounded-xl bg-violet-600 hover:bg-violet-700 text-white text-xs font-semibold transition-colors"
                @click="showRecoveryCodes = false">
                {{ __('Hide recovery codes') }}
            </button>

            @if (filled($recoveryCodes))
                <button type="button"
                    x-show="showRecoveryCodes"
                    class="py-2 px-4 rounded-xl border border-zinc-700 hover:bg-zinc-800 text-zinc-300 text-xs font-medium transition-colors"
                    wire:click="regenerateRecoveryCodes">
                    {{ __('Regenerate codes') }}
                </button>
            @endif
        </div>

        <div
            x-show="showRecoveryCodes"
            x-transition
            id="recovery-codes-section"
            class="relative overflow-hidden"
        >
            <div class="mt-3 space-y-3">
                @error('recoveryCodes')
                    <div class="p-3 bg-rose-500/10 border border-rose-500/20 text-rose-400 rounded-xl text-xs">{{ $message }}</div>
                @enderror

                @if (filled($recoveryCodes))
                    <div
                        class="grid gap-1 p-4 font-mono text-sm rounded-lg bg-zinc-900 border border-zinc-800 text-zinc-300"
                        role="list"
                    >
                        @foreach($recoveryCodes as $code)
                            <div
                                role="listitem"
                                class="select-text"
                                wire:loading.class="opacity-50 animate-pulse"
                            >
                                {{ $code }}
                            </div>
                        @endforeach
                    </div>
                    <p class="text-zinc-500 text-[10px]">
                        {{ __('Each recovery code can be used once to access your account and will be removed after use. If you need more, click Regenerate codes above.') }}
                    </p>
                @endif
            </div>
        </div>
    </div>
</div>
