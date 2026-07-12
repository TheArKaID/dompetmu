<x-layouts::auth :title="__('Forgot password')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Forgot password')" :description="__('Enter your email to receive a password reset link')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-5">
            @csrf

            <!-- Email Address -->
            <div>
                <label for="email" class="text-zinc-400 text-xs font-medium block mb-1.5">{{ __('Email address') }}</label>
                <input id="email" type="email" name="email" required autofocus placeholder="email@example.com" value="{{ old('email') }}"
                       class="w-full bg-zinc-800 border border-zinc-700 text-zinc-200 text-sm rounded-xl px-3 py-2.5 outline-none focus:ring-1 focus:ring-violet-500">
                @error('email')
                <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="w-full py-2.5 rounded-xl bg-violet-600 hover:bg-violet-700 text-white text-sm font-semibold transition-colors active:scale-95 flex items-center justify-center">
                {{ __('Email password reset link') }}
            </button>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-400">
            <span>{{ __('Or, return to') }}</span>
            <a href="{{ route('login') }}" class="text-violet-400 hover:underline font-medium" wire:navigate>{{ __('log in') }}</a>
        </div>
    </div>
</x-layouts::auth>
