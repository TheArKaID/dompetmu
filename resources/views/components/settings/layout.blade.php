<div class="flex items-start max-md:flex-col">
    <div class="me-10 w-full pb-4 md:w-[220px]">
        <nav class="space-y-1">
            <a href="{{ route('profile.edit') }}" wire:navigate class="block px-3 py-2 rounded-lg text-sm {{ request()->routeIs('profile.edit') ? 'bg-zinc-800 text-white font-medium' : 'text-zinc-400 hover:text-white hover:bg-zinc-900' }}">{{ __('Profile') }}</a>
            <a href="{{ route('security.edit') }}" wire:navigate class="block px-3 py-2 rounded-lg text-sm {{ request()->routeIs('security.edit') ? 'bg-zinc-800 text-white font-medium' : 'text-zinc-400 hover:text-white hover:bg-zinc-900' }}">{{ __('Security') }}</a>
            <a href="{{ route('appearance.edit') }}" wire:navigate class="block px-3 py-2 rounded-lg text-sm {{ request()->routeIs('appearance.edit') ? 'bg-zinc-800 text-white font-medium' : 'text-zinc-400 hover:text-white hover:bg-zinc-900' }}">{{ __('Appearance') }}</a>
        </nav>
    </div>

    <div class="w-full border-t border-zinc-800 md:hidden my-4"></div>

    <div class="flex-1 self-stretch max-md:pt-6">
        <h2 class="text-lg font-bold text-white">{{ $heading ?? '' }}</h2>
        <p class="text-sm text-zinc-400 mt-1">{{ $subheading ?? '' }}</p>

        <div class="mt-5 w-full max-w-lg">
            {{ $slot }}
        </div>
    </div>
</div>
