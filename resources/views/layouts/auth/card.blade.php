@props(['title' => config('app.name')])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <title>{{ $title }} — FinArka</title>
        @include('partials.head')
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    </head>
    <body class="min-h-screen bg-zinc-950 antialiased flex flex-col items-center justify-center px-5 py-8">
        <div class="w-full max-w-sm flex flex-col gap-6">

            {{-- Brand --}}
            <div class="flex flex-col items-center gap-3">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-violet-600 to-indigo-700 flex items-center justify-center shadow-lg shadow-violet-900/50">
                    <x-app-logo-icon class="size-8 fill-current text-white" />
                </div>
                <div class="text-center">
                    <p class="text-white font-bold text-xl tracking-tight">FinArka</p>
                    <p class="text-zinc-500 text-xs mt-0.5">Personal Finance</p>
                </div>
            </div>

            {{-- Card --}}
            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-5 shadow-xl">
                {{ $slot }}
            </div>

        </div>
    </body>
</html>
