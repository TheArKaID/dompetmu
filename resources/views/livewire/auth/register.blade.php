<x-layouts::auth :title="__('Register')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Create an account')" :description="__('Enter your details below to create your account')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-5">
            @csrf

            <!-- Name -->
            <div>
                <label for="name" class="text-zinc-400 text-xs font-medium block mb-1.5">{{ __('Name') }}</label>
                <input id="name" type="text" name="name" required autofocus autocomplete="name" placeholder="{{ __('Full name') }}" value="{{ old('name') }}"
                       class="w-full bg-zinc-800 border border-zinc-700 text-zinc-200 text-sm rounded-xl px-3 py-2.5 outline-none focus:ring-1 focus:ring-violet-500">
                @error('name')
                <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email Address -->
            <div>
                <label for="email" class="text-zinc-400 text-xs font-medium block mb-1.5">{{ __('Email address') }}</label>
                <input id="email" type="email" name="email" required autocomplete="email" placeholder="email@example.com" value="{{ old('email') }}"
                       class="w-full bg-zinc-800 border border-zinc-700 text-zinc-200 text-sm rounded-xl px-3 py-2.5 outline-none focus:ring-1 focus:ring-violet-500">
                @error('email')
                <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="text-zinc-400 text-xs font-medium block mb-1.5">{{ __('Password') }}</label>
                <input id="password" type="password" name="password" required autocomplete="new-password" placeholder="{{ __('Password') }}"
                       class="w-full bg-zinc-800 border border-zinc-700 text-zinc-200 text-sm rounded-xl px-3 py-2.5 outline-none focus:ring-1 focus:ring-violet-500">
                @error('password')
                <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation" class="text-zinc-400 text-xs font-medium block mb-1.5">{{ __('Confirm password') }}</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="{{ __('Confirm password') }}"
                       class="w-full bg-zinc-800 border border-zinc-700 text-zinc-200 text-sm rounded-xl px-3 py-2.5 outline-none focus:ring-1 focus:ring-violet-500">
                @error('password_confirmation')
                <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="w-full py-2.5 rounded-xl bg-violet-600 hover:bg-violet-700 text-white text-sm font-semibold transition-colors active:scale-95 flex items-center justify-center">
                {{ __('Create account') }}
            </button>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-400">
            <span>{{ __('Already have an account?') }}</span>
            <a href="{{ route('login') }}" class="text-violet-400 hover:underline font-medium" wire:navigate>{{ __('Log in') }}</a>
        </div>
    </div>
</x-layouts::auth>
