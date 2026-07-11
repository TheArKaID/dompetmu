<div class="menu w-full relative" id="user-menu-dropdown" data-stisla-menu data-stisla-menu-placement="bottom-start" x-data="{ openMenu: false }">
    <!-- Trigger Button -->
    <button type="button" 
            class="sidebar__profile flex items-center justify-between w-full px-3 py-2 rounded-lg text-sm font-medium hover:bg-zinc-900 transition-colors text-start" 
            data-stisla-menu-trigger="user-menu-dropdown"
            @click="openMenu = !openMenu">
        <div class="flex items-center gap-2.5 min-w-0">
            <!-- Initials Avatar -->
            <div class="w-8 h-8 rounded-full bg-zinc-800 flex items-center justify-center flex-none">
                <span class="text-zinc-300 text-xs font-semibold">{{ auth()->user()->initials() }}</span>
            </div>
            <div class="min-w-0">
                <span class="text-white text-sm font-medium truncate block">{{ auth()->user()->name }}</span>
            </div>
        </div>
        <!-- chevron icon -->
        <svg class="w-4 h-4 text-zinc-500 flex-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    <!-- Menu Popup -->
    <div class="menu__popup absolute left-0 bottom-full mb-2 bg-zinc-900 border border-zinc-800 rounded-xl shadow-xl w-60 py-1.5 z-50" x-show="openMenu" @click.outside="openMenu = false" x-transition style="display: none;">
        <div class="flex items-center gap-2 px-3 py-2 text-start text-sm border-b border-zinc-800 mb-1">
            <div class="w-8 h-8 rounded-full bg-zinc-800 flex items-center justify-center flex-none">
                <span class="text-zinc-300 text-xs font-semibold">{{ auth()->user()->initials() }}</span>
            </div>
            <div class="grid flex-1 text-start text-xs leading-tight min-w-0">
                <span class="text-white font-medium truncate">{{ auth()->user()->name }}</span>
                <span class="text-zinc-500 truncate">{{ auth()->user()->email }}</span>
            </div>
        </div>

        <a href="{{ route('profile.edit') }}" class="menu__item flex items-center px-3 py-2 text-sm text-zinc-300 hover:bg-zinc-800 hover:text-white transition-colors" role="menuitem" wire:navigate>
            <svg class="w-4 h-4 mr-2.5 text-zinc-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065zM12 15a3 3 0 100-6 3 3 0 000 6z"/>
            </svg>
            <span>Pengaturan</span>
        </a>

        <div class="border-t border-zinc-800 my-1"></div>

        <form method="POST" action="{{ route('logout') }}" class="w-full">
            @csrf
            <button type="submit" class="menu__item menu__item--danger flex items-center w-full px-3 py-2 text-sm text-rose-400 hover:bg-rose-500/10 transition-colors text-start" role="menuitem">
                <svg class="w-4 h-4 mr-2.5 text-rose-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span>Keluar</span>
            </button>
        </form>
    </div>
</div>
