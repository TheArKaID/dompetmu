<x-layouts::auth :title="__('Confirm password')">
    <div class="flex flex-col gap-6">
        <x-auth-header
            :title="__('Confirm password')"
            :description="__('This is a secure area of the application. Please confirm your password before continuing.')"
        />

        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('password.confirm.store') }}" class="flex flex-col gap-5">
            @csrf

            <div>
                <label for="password" class="text-zinc-400 text-xs font-medium block mb-1.5">{{ __('Password') }}</label>
                <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="{{ __('Password') }}"
                       class="w-full bg-zinc-800 border border-zinc-700 text-zinc-200 text-sm rounded-xl px-3 py-2.5 outline-none focus:ring-1 focus:ring-violet-500">
                @error('password')
                <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="w-full py-2.5 rounded-xl bg-violet-600 hover:bg-violet-700 text-white text-sm font-semibold transition-colors active:scale-95 flex items-center justify-center">
                {{ __('Confirm') }}
            </button>
        </form>
    </div>
</x-layouts::auth>
