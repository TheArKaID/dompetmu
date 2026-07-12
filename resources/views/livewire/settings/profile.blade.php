<div class="min-h-screen pb-4">

    {{-- Sticky Header --}}
    <div class="sticky top-0 z-20 bg-zinc-900/95 backdrop-blur-md border-b border-zinc-800/70 px-4 py-3 flex items-center gap-3">
        <a href="{{ url()->previous() }}" class="w-8 h-8 rounded-xl bg-zinc-800 flex items-center justify-center hover:bg-zinc-700 transition-colors flex-none">
            <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-base font-bold text-white">Profil & Password</h1>
    </div>

    <div class="px-4 pt-4 space-y-4">

        {{-- Notification on save --}}
        @if (session('status') === 'profile-information-updated')
            <div class="flex items-center gap-2 px-3 py-2 bg-emerald-900/40 border border-emerald-700/40 rounded-xl text-emerald-300 text-xs">
                <svg class="w-4 h-4 flex-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                Profil berhasil disimpan.
            </div>
        @endif

        {{-- Profile Card --}}
        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-4">
            <p class="text-zinc-400 text-xs font-semibold uppercase tracking-widest mb-3">Informasi Profil</p>
            <form wire:submit="updateProfileInformation" class="space-y-3">
                <div>
                    <label for="profile_name" class="text-zinc-400 text-xs font-medium block mb-1.5">Nama</label>
                    <input id="profile_name" type="text" wire:model="name" required autofocus autocomplete="name"
                           class="w-full bg-zinc-800 border border-zinc-700 text-zinc-200 text-sm rounded-xl px-3 py-2.5 outline-none focus:ring-1 focus:ring-violet-500">
                    @error('name')
                    <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="profile_email" class="text-zinc-400 text-xs font-medium block mb-1.5">Email</label>
                    <input id="profile_email" type="email" wire:model="email" required autocomplete="email"
                           class="w-full bg-zinc-800 border border-zinc-700 text-zinc-200 text-sm rounded-xl px-3 py-2.5 outline-none focus:ring-1 focus:ring-violet-500">
                    @error('email')
                    <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                    @enderror

                    @if ($this->hasUnverifiedEmail)
                    <div class="mt-2 p-2.5 bg-amber-900/30 border border-amber-700/40 rounded-xl">
                        <p class="text-amber-300 text-xs">
                            Email belum terverifikasi.
                            <button type="button" class="text-amber-200 underline font-medium ml-1" wire:click.prevent="resendVerificationNotification">
                                Kirim ulang email verifikasi.
                            </button>
                        </p>
                    </div>
                    @endif
                </div>

                <button type="submit"
                        class="w-full py-2.5 rounded-xl bg-violet-600 hover:bg-violet-700 text-white text-sm font-semibold transition-colors active:scale-95 flex items-center justify-center gap-2">
                    <span wire:loading.remove wire:target="updateProfileInformation">Simpan Perubahan</span>
                    <span wire:loading wire:target="updateProfileInformation">Menyimpan...</span>
                </button>
            </form>
        </div>

        {{-- Delete user --}}
        @if ($this->showDeleteUser)
        <livewire:settings.delete-user-form />
        @endif

    </div>
</div>
