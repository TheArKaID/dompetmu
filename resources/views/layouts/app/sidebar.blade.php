<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-zinc-950">
        <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-800 bg-zinc-950 dark:border-zinc-800 dark:bg-zinc-950">
            <flux:sidebar.header class="border-b border-zinc-800 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-violet-600 to-indigo-700 flex items-center justify-center flex-none">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <span class="text-white font-bold text-sm tracking-tight">FinArka</span>
                        <span class="text-zinc-500 text-xs block -mt-0.5">Personal Finance</span>
                    </div>
                </div>
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav class="mt-2">

                {{-- ── Finance ──────────────────────────────────────────────── --}}
                <flux:sidebar.group heading="Keuangan" class="grid">
                    <flux:sidebar.item
                        icon="home"
                        :href="route('finance.dashboard')"
                        :current="request()->routeIs('finance.dashboard')"
                        wire:navigate
                    >
                        Dashboard
                    </flux:sidebar.item>

                    <flux:sidebar.item
                        icon="list-bullet"
                        :href="route('finance.transactions')"
                        :current="request()->routeIs('finance.transactions')"
                        wire:navigate
                    >
                        Transaksi
                    </flux:sidebar.item>
                </flux:sidebar.group>

                {{-- ── Management ──────────────────────────────────────────── --}}
                <flux:sidebar.group heading="Pengaturan" class="grid">
                    <flux:sidebar.item
                        icon="credit-card"
                        :href="route('finance.accounts')"
                        :current="request()->routeIs('finance.accounts')"
                        wire:navigate
                    >
                        Rekening
                    </flux:sidebar.item>

                    <flux:sidebar.item
                        icon="tag"
                        :href="route('finance.categories')"
                        :current="request()->routeIs('finance.categories')"
                        wire:navigate
                    >
                        Kategori
                    </flux:sidebar.item>
                </flux:sidebar.group>

                {{-- ── Data ────────────────────────────────────────────────── --}}
                <flux:sidebar.group heading="Data" class="grid">
                    <flux:sidebar.item
                        icon="arrow-up-tray"
                        :href="route('finance.import')"
                        :current="request()->routeIs('finance.import')"
                        wire:navigate
                    >
                        Import CSV
                    </flux:sidebar.item>
                </flux:sidebar.group>

            </flux:sidebar.nav>

            <flux:spacer />

            <flux:sidebar.nav>
                <flux:sidebar.item icon="cog-6-tooth" :href="route('profile.edit')" wire:navigate>
                    {{ __('Pengaturan Profil') }}
                </flux:sidebar.item>
            </flux:sidebar.nav>

            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
        </flux:sidebar>

        {{-- Mobile Header --}}
        <flux:header class="lg:hidden border-b border-zinc-800 bg-zinc-950">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <div class="flex items-center gap-2 ml-2">
                <div class="w-6 h-6 rounded-md bg-gradient-to-br from-violet-600 to-indigo-700 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="text-white font-semibold text-sm">FinArka</span>
            </div>

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />
                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                        >
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
