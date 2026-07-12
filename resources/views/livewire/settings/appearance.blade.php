<div class="min-h-screen pb-4">

    {{-- Sticky Header --}}
    <div class="sticky top-0 z-20 bg-zinc-900/95 backdrop-blur-md border-b border-zinc-800/70 px-4 py-3 flex items-center gap-3">
        <a href="{{ url()->previous() }}" class="w-8 h-8 rounded-xl bg-zinc-800 flex items-center justify-center hover:bg-zinc-700 transition-colors flex-none">
            <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-base font-bold text-white">Tampilan</h1>
    </div>

    <div class="px-4 pt-4">
        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-4 space-y-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-zinc-700 flex items-center justify-center flex-none">
                    <svg class="w-5 h-5 text-zinc-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-white font-semibold text-sm">Mode Gelap</p>
                    <p class="text-zinc-400 text-xs">Tampilan bawaan FinArka</p>
                </div>
                <div class="ml-auto">
                    <span class="text-[9px] font-semibold text-emerald-400 bg-emerald-500/20 border border-emerald-500/30 px-1.5 py-0.5 rounded-md">Aktif</span>
                </div>
            </div>
            <p class="text-zinc-500 text-xs leading-relaxed">
                FinArka menggunakan mode gelap secara permanen untuk kenyamanan visual di perangkat mobile dan menghemat baterai layar AMOLED.
            </p>
        </div>
    </div>

</div>
