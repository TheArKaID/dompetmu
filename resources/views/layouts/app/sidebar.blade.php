<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-zinc-950 text-zinc-100 font-sans antialiased">
        <div class="site-layout flex min-h-screen" x-data="{ sidebarOpen: false }">
            <!-- Backdrop for Mobile Sidebar -->
            <div class="site-backdrop fixed inset-0 z-40 bg-zinc-950/80 backdrop-blur-sm lg:hidden"
                 x-show="sidebarOpen"
                 @click="sidebarOpen = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 style="display: none;"></div>

            <!-- Sidebar Panel -->
            <aside id="site-sidebar" 
                   class="site-sidebar sidebar sidebar--sm bg-zinc-900 border-e border-zinc-800 flex flex-col fixed inset-y-0 left-0 z-50 w-64 transform transition-transform duration-200 ease-in-out lg:translate-x-0 lg:static lg:h-screen lg:w-64"
                   :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
                
                <!-- Sidebar Header (Brand) -->
                <div class="sidebar__header border-b border-zinc-800 pb-3 p-4 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-violet-600 to-indigo-700 flex items-center justify-center flex-none">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <span class="text-white font-bold text-sm tracking-tight">FinArka</span>
                            <span class="text-zinc-500 text-xs block -mt-0.5">Personal Finance</span>
                        </div>
                    </div>
                    <!-- Close mobile sidebar trigger -->
                    <button type="button" @click="sidebarOpen = false" class="lg:hidden text-zinc-400 hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Sidebar Navigation Menu -->
                <div class="sidebar__content flex-1 overflow-y-auto p-3">
                    <nav class="sidebar__menu">
                        
                        <!-- Keuangan Group -->
                        <div class="sidebar__group">
                            <span class="sidebar__group-title px-3 py-1.5 text-[10px] font-semibold text-zinc-500 uppercase tracking-widest block">Keuangan</span>
                            <ul class="sidebar__list space-y-0.5 mt-1">
                                <li class="sidebar__item">
                                    <a href="{{ route('finance.dashboard') }}" 
                                       class="sidebar__button flex items-center px-3 py-2 rounded-lg text-sm transition-all text-zinc-400 hover:text-white hover:bg-zinc-850"
                                       :class="{'bg-zinc-800 text-white font-medium': {{ request()->routeIs('finance.dashboard') ? 'true' : 'false' }} }"
                                       aria-current="{{ request()->routeIs('finance.dashboard') ? 'page' : 'false' }}"
                                       wire:navigate>
                                        <svg class="w-4 h-4 mr-2.5 flex-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                        </svg>
                                        <span>Dashboard</span>
                                    </a>
                                </li>
                                <li class="sidebar__item">
                                    <a href="{{ route('finance.transactions') }}" 
                                       class="sidebar__button flex items-center px-3 py-2 rounded-lg text-sm transition-all text-zinc-400 hover:text-white hover:bg-zinc-850"
                                       :class="{'bg-zinc-800 text-white font-medium': {{ request()->routeIs('finance.transactions') ? 'true' : 'false' }} }"
                                       aria-current="{{ request()->routeIs('finance.transactions') ? 'page' : 'false' }}"
                                       wire:navigate>
                                        <svg class="w-4 h-4 mr-2.5 flex-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                                        </svg>
                                        <span>Transaksi</span>
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <!-- Pengaturan Group -->
                        <div class="sidebar__group mt-4">
                            <span class="sidebar__group-title px-3 py-1.5 text-[10px] font-semibold text-zinc-500 uppercase tracking-widest block">Pengaturan</span>
                            <ul class="sidebar__list space-y-0.5 mt-1">
                                <li class="sidebar__item">
                                    <a href="{{ route('finance.accounts') }}" 
                                       class="sidebar__button flex items-center px-3 py-2 rounded-lg text-sm transition-all text-zinc-400 hover:text-white hover:bg-zinc-855"
                                       :class="{'bg-zinc-800 text-white font-medium': {{ request()->routeIs('finance.accounts') ? 'true' : 'false' }} }"
                                       aria-current="{{ request()->routeIs('finance.accounts') ? 'page' : 'false' }}"
                                       wire:navigate>
                                        <svg class="w-4 h-4 mr-2.5 flex-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                        </svg>
                                        <span>Rekening</span>
                                    </a>
                                </li>
                                <li class="sidebar__item">
                                    <a href="{{ route('finance.categories') }}" 
                                       class="sidebar__button flex items-center px-3 py-2 rounded-lg text-sm transition-all text-zinc-400 hover:text-white hover:bg-zinc-855"
                                       :class="{'bg-zinc-800 text-white font-medium': {{ request()->routeIs('finance.categories') ? 'true' : 'false' }} }"
                                       aria-current="{{ request()->routeIs('finance.categories') ? 'page' : 'false' }}"
                                       wire:navigate>
                                        <svg class="w-4 h-4 mr-2.5 flex-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.562 3.224l10.536 10.536a2.182 2.182 0 010 3.086l-4.14 4.14a2.182 2.182 0 01-3.086 0L2.336 10.45A2.182 2.182 0 011.664 8.9V3.8c0-1.1.9-2 2-2h5.1c.6 0 1.2.224 1.664.674zM6.5 6.5h.01"/>
                                        </svg>
                                        <span>Kategori</span>
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <!-- Data Group -->
                        <div class="sidebar__group mt-4">
                            <span class="sidebar__group-title px-3 py-1.5 text-[10px] font-semibold text-zinc-500 uppercase tracking-widest block">Data</span>
                            <ul class="sidebar__list space-y-0.5 mt-1">
                                <li class="sidebar__item">
                                    <a href="{{ route('finance.import') }}" 
                                       class="sidebar__button flex items-center px-3 py-2 rounded-lg text-sm transition-all text-zinc-400 hover:text-white hover:bg-zinc-855"
                                       :class="{'bg-zinc-800 text-white font-medium': {{ request()->routeIs('finance.import') ? 'true' : 'false' }} }"
                                       aria-current="{{ request()->routeIs('finance.import') ? 'page' : 'false' }}"
                                       wire:navigate>
                                        <svg class="w-4 h-4 mr-2.5 flex-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                        </svg>
                                        <span>Import CSV</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </nav>
                </div>

                <!-- Sidebar Footer -->
                <div class="sidebar__footer p-3 border-t border-zinc-800 space-y-2">
                    <a href="{{ route('profile.edit') }}" 
                       class="sidebar__button flex items-center px-3 py-2 rounded-lg text-sm transition-all text-zinc-400 hover:text-white hover:bg-zinc-800"
                       :class="{'bg-zinc-800 text-white font-medium': {{ request()->routeIs('profile.edit') ? 'true' : 'false' }} }"
                       wire:navigate>
                        <svg class="w-4 h-4 mr-2.5 flex-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065zM12 15a3 3 0 100-6 3 3 0 000 6z"/>
                        </svg>
                        <span>Pengaturan Profil</span>
                    </a>
                    
                    <x-desktop-user-menu class="hidden lg:block" />
                </div>
            </aside>

            <!-- Main Section Wrapper -->
            <div class="flex-1 flex flex-col min-w-0 min-h-screen">
                
                <!-- Mobile Header (Navbar) -->
                <header class="site-navbar navbar lg:hidden bg-zinc-900 border-b border-zinc-800 px-4 py-2.5 flex items-center justify-between z-30">
                    <button type="button" @click="sidebarOpen = true" class="text-zinc-400 hover:text-white p-1">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>

                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-md bg-gradient-to-br from-violet-600 to-indigo-700 flex items-center justify-center">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <span class="text-white font-semibold text-sm">FinArka</span>
                    </div>

                    <!-- Mobile Profile Menu Dropdown -->
                    <div class="relative" x-data="{ openMobileMenu: false }">
                        <button type="button" 
                                class="flex items-center gap-1 hover:bg-zinc-800 p-1.5 rounded-lg transition-colors"
                                @click="openMobileMenu = !openMobileMenu">
                            <div class="w-7 h-7 rounded-full bg-zinc-800 flex items-center justify-center">
                                <span class="text-zinc-300 text-[11px] font-bold">{{ auth()->user()->initials() }}</span>
                            </div>
                        </button>

                        <div class="menu__popup absolute right-0 mt-2 bg-zinc-900 border border-zinc-800 rounded-xl shadow-xl w-52 py-1.5 z-50" 
                             x-show="openMobileMenu" 
                             @click.outside="openMobileMenu = false" 
                             x-transition 
                             style="display: none;">
                            <div class="flex flex-col px-3 py-1.5 border-b border-zinc-800 mb-1 leading-tight">
                                <span class="text-white text-xs font-semibold truncate">{{ auth()->user()->name }}</span>
                                <span class="text-zinc-500 text-[10px] truncate mt-0.5">{{ auth()->user()->email }}</span>
                            </div>
                            
                            <a href="{{ route('profile.edit') }}" class="menu__item flex items-center px-3 py-1.5 text-xs text-zinc-300 hover:bg-zinc-800 hover:text-white" role="menuitem" wire:navigate>
                                <span>Pengaturan</span>
                            </a>

                            <div class="border-t border-zinc-800 my-1"></div>

                            <form method="POST" action="{{ route('logout') }}" class="w-full">
                                @csrf
                                <button type="submit" class="menu__item menu__item--danger flex items-center w-full px-3 py-1.5 text-xs text-rose-400 hover:bg-rose-500/10 text-start" role="menuitem">
                                    <span>Keluar</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </header>

                <!-- Main Content Slot -->
                <main class="site-main flex-1 overflow-y-auto">
                    {{ $slot }}
                </main>
            </div>
        </div>

        @persist('toast')
        <div x-data="{ 
                toasts: [],
                addToast(message, type = 'success') {
                    const id = Date.now();
                    this.toasts.push({ id, message, type });
                    setTimeout(() => this.removeToast(id), 4000);
                },
                removeToast(id) {
                    this.toasts = this.toasts.filter(t => t.id !== id);
                }
             }"
             @toast.window="addToast($event.detail.message, $event.detail.type)"
             class="fixed bottom-4 right-4 z-50 flex flex-col gap-2 pointer-events-none"
             id="toast-container">
            <template x-for="t in toasts" :key="t.id">
                <div class="pointer-events-auto flex items-center gap-2.5 px-4 py-3 rounded-xl border shadow-lg transform transition-all duration-300"
                     :class="{
                        'bg-emerald-950/90 border-emerald-800 text-emerald-300': t.type === 'success',
                        'bg-rose-950/90 border-rose-800 text-rose-300': t.type === 'danger' || t.type === 'error',
                        'bg-zinc-900/90 border-zinc-800 text-zinc-300': t.type === 'info'
                     }"
                     x-transition>
                    <span class="text-sm font-medium" x-text="t.message"></span>
                    <button @click="removeToast(t.id)" class="ml-2 hover:opacity-80 text-zinc-400 hover:text-white">
                        &times;
                    </button>
                </div>
            </template>
        </div>
        @endpersist
    </body>
</html>
