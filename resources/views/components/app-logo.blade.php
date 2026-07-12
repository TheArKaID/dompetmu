@props([
    'sidebar' => false,
])

<div {{ $attributes->merge(['class' => 'flex items-center gap-2.5']) }}>
    <div class="flex aspect-square size-8 items-center justify-center rounded-md bg-violet-600 text-white flex-none">
        <x-app-logo-icon class="size-5 fill-current text-white" />
    </div>
    <span class="text-white font-bold text-sm tracking-tight">FinArka</span>
</div>
