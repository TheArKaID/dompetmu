<x-layouts::app.sidebar :title="$title ?? null">
    <div class="main-container">
        {{ $slot }}
    </div>
</x-layouts::app.sidebar>
