@php($links = ['work' => 'Work', 'services' => 'Services', 'process' => 'Process'])

<header x-data="{ open: false }" @keydown.escape.window="open = false" class="relative z-20 border-b border-line">
    <nav class="wrap flex items-center justify-between gap-5 py-[22px]" aria-label="Main">
        <a href="#top" aria-label="Emax Digital home" class="text-white">
            <x-logo class="text-[30px]" />
        </a>

        <div class="hidden items-center gap-9 font-label text-[13px] font-medium tracking-[0.18em] md:flex">
            @foreach ($links as $id => $label)
                <a href="#{{ $id }}" class="nav-link text-white">{{ $label }}</a>
            @endforeach
            <a href="#contact" x-magnetic="0.2" class="bg-ion px-5 py-[13px] font-bold text-ink hover:bg-white">Start a project</a>
        </div>

        <button type="button" class="-mr-2 p-2 text-white md:hidden" @click="open = !open" :aria-expanded="open" aria-controls="mobile-menu">
            <span class="sr-only">Menu</span>
            <svg x-show="!open" class="size-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="square" aria-hidden="true"><path d="M3 7h18M3 17h18"/></svg>
            <svg x-show="open" x-cloak class="size-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="square" aria-hidden="true"><path d="M5 5l14 14M19 5L5 19"/></svg>
        </button>
    </nav>

    <div id="mobile-menu" x-show="open" x-cloak
         x-transition:enter="transition duration-300 ease-snap" x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:leave="transition duration-200" x-transition:leave-end="opacity-0 -translate-y-2"
         class="absolute inset-x-0 top-full border-b border-line bg-ink md:hidden">
        <div class="wrap flex flex-col gap-1 py-6">
            @foreach ($links as $id => $label)
                <a href="#{{ $id }}" @click="open = false" class="font-display py-2 text-[40px] leading-none text-white">{{ $label }}</a>
            @endforeach
            <a href="#contact" @click="open = false" class="mt-4 self-start bg-ion px-5 py-[13px] font-label text-[13px] font-bold tracking-[0.18em] text-ink">Start a project</a>
        </div>
    </div>
</header>
