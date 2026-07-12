@props([
    'title',
    'description',
])

<div class="flex w-full flex-col text-center">
    <h1 class="text-xl font-bold text-white tracking-tight">{{ $title }}</h1>
    <p class="text-sm text-zinc-400 mt-1 leading-normal">{{ $description }}</p>
</div>
