<section class="mt-10 space-y-6" x-data="{ showDeleteModal: false }">
    <div class="relative mb-5">
        <h3 class="text-base font-bold text-white">{{ __('Delete account') }}</h3>
        <p class="text-xs text-zinc-400 mt-1">{{ __('Delete your account and all of its resources') }}</p>
    </div>

    <button type="button" @click="showDeleteModal = true" class="py-2.5 px-4 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-sm font-semibold transition-colors">
        {{ __('Delete account') }}
    </button>

    {{-- Backdrop --}}
    <div class="fixed inset-0 z-[80] bg-zinc-950/70 backdrop-blur-sm"
         x-show="showDeleteModal"
         @click="showDeleteModal = false"
         x-transition style="display:none;"></div>

    {{-- Modal panel --}}
    <div class="fixed bottom-0 inset-x-0 z-[90] bg-zinc-900 border-t border-zinc-800 rounded-t-3xl shadow-2xl p-4 space-y-4"
         x-show="showDeleteModal"
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

        <form method="POST" wire:submit="deleteUser" class="space-y-4">
            <div>
                <h4 class="text-white font-bold text-base">{{ __('Are you sure you want to delete your account?') }}</h4>
                <p class="text-zinc-400 text-xs mt-1 leading-normal">
                    {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                </p>
            </div>

            <div>
                <label for="delete_password" class="text-zinc-400 text-xs font-medium block mb-1.5">{{ __('Password') }}</label>
                <input id="delete_password" type="password" wire:model="password" required placeholder="{{ __('Password') }}"
                       class="w-full bg-zinc-800 border border-zinc-700 text-zinc-200 text-sm rounded-xl px-3 py-2.5 outline-none focus:ring-1 focus:ring-rose-500">
                @error('password')
                <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-3">
                <button type="button" @click="showDeleteModal = false"
                        class="flex-1 py-2.5 rounded-xl border border-zinc-700 text-zinc-300 text-sm font-medium hover:bg-zinc-800 transition-colors">
                    {{ __('Cancel') }}
                </button>
                <button type="submit"
                        class="flex-1 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-sm font-semibold transition-colors active:scale-95 flex items-center justify-center">
                    {{ __('Delete account') }}
                </button>
            </div>
        </form>
    </div>
</section>
