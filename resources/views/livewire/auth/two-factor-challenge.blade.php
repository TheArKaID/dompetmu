<x-layouts::auth :title="__('Two-factor authentication')">
    <div class="flex flex-col gap-6">
        <div
            class="relative w-full h-auto"
            x-cloak
            x-data="{
                showRecoveryInput: @js($errors->has('recovery_code')),
                code: '',
                recovery_code: '',
                toggleInput() {
                    this.showRecoveryInput = !this.showRecoveryInput;
                    this.code = '';
                    this.recovery_code = '';
                    $nextTick(() => {
                        this.showRecoveryInput
                            ? this.$refs.recovery_code?.focus()
                            : this.$refs.code?.focus();
                    });
                },
            }"
        >
            <div x-show="!showRecoveryInput">
                <x-auth-header
                    :title="__('Authentication code')"
                    :description="__('Enter the authentication code provided by your authenticator application.')"
                />
            </div>

            <div x-show="showRecoveryInput">
                <x-auth-header
                    :title="__('Recovery code')"
                    :description="__('Please confirm access to your account by entering one of your emergency recovery codes.')"
                />
            </div>

            <form method="POST" action="{{ route('two-factor.login.store') }}" class="mt-4">
                @csrf

                <div class="space-y-4">
                    <div x-show="!showRecoveryInput">
                        <div>
                            <label for="code" class="text-zinc-400 text-xs font-medium block mb-1.5">{{ __('Verification Code') }}</label>
                            <input id="code" type="text" name="code" x-ref="code" x-model="code" maxlength="6" inputmode="numeric" placeholder="123456" autocomplete="one-time-code"
                                   class="w-full bg-zinc-800 border border-zinc-700 text-zinc-200 text-center text-xl tracking-widest rounded-xl px-3 py-2.5 outline-none focus:ring-1 focus:ring-violet-500">
                            @error('code')
                            <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div x-show="showRecoveryInput">
                        <div>
                            <label for="recovery_code" class="text-zinc-400 text-xs font-medium block mb-1.5">{{ __('Recovery Code') }}</label>
                            <input id="recovery_code" type="text" name="recovery_code" x-ref="recovery_code" x-model="recovery_code" autocomplete="one-time-code" placeholder="abcdef-123456"
                                   class="w-full bg-zinc-800 border border-zinc-700 text-zinc-200 text-center text-sm rounded-xl px-3 py-2.5 outline-none focus:ring-1 focus:ring-violet-500">
                            @error('recovery_code')
                            <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <button type="submit" class="w-full py-2.5 rounded-xl bg-violet-600 hover:bg-violet-700 text-white text-sm font-semibold transition-colors active:scale-95 flex items-center justify-center">
                        {{ __('Continue') }}
                    </button>
                </div>

                <div class="mt-5 space-x-0.5 text-sm leading-5 text-center text-zinc-400">
                    <span class="opacity-50">{{ __('or you can') }}</span>
                    <div class="inline font-medium underline cursor-pointer text-violet-400 hover:text-violet-300">
                        <span x-show="!showRecoveryInput" @click="toggleInput()">{{ __('login using a recovery code') }}</span>
                        <span x-show="showRecoveryInput" @click="toggleInput()">{{ __('login using an authentication code') }}</span>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layouts::auth>
