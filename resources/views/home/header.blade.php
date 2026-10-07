@php
    $links = ['work' => 'Work', 'services' => 'Services', 'process' => 'Process'];
    $menuLinks = $links + ['contact' => 'Contact'];
    $email = config('site.email');
@endphp

<div x-data="mobileMenu" @keydown.escape.window="close()">
    <header class="relative z-20 border-b border-line">
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

            <button type="button" class="relative -mr-2 size-11 text-white md:hidden" @click="show()" :aria-expanded="open" aria-controls="mobile-menu">
                <span class="sr-only">Open menu</span>
                <span class="absolute top-1/2 left-2 h-[2.4px] w-7 -translate-y-[6px] bg-current"></span>
                <span class="absolute top-1/2 left-2 h-[2.4px] w-7 translate-y-[4px] bg-current"></span>
            </button>
        </nav>
    </header>

    {{-- Floating trigger once the header has scrolled away (thumb reach) --}}
    <button type="button" x-show="scrolled && !open && !closing" x-cloak
            x-transition:enter="transition duration-400 ease-snap" x-transition:enter-start="translate-y-6 opacity-0 scale-90"
            x-transition:leave="transition duration-200" x-transition:leave-end="translate-y-6 opacity-0"
            @click="show()" :aria-expanded="open" aria-controls="mobile-menu"
            class="fixed right-4 z-40 flex items-center gap-3 rounded-full bg-ion py-3.5 pr-5 pl-4 font-label text-xs font-bold tracking-[0.2em] text-ink shadow-[0_12px_30px_-8px_rgba(0,0,0,.6)] active:scale-95 md:hidden"
            style="bottom: max(1rem, env(safe-area-inset-bottom))">
        <span class="flex flex-col gap-[5px]" aria-hidden="true"><span class="h-[2px] w-4 bg-ink"></span><span class="h-[2px] w-4 bg-ink"></span></span>
        Menu
    </button>

    {{-- Full-screen menu --}}
    <div id="mobile-menu" x-ref="menu" role="dialog" aria-modal="true" aria-label="Menu"
         class="menu fixed inset-0 z-50 overflow-hidden md:hidden"
         :class="{ 'is-open': open, 'is-closing': closing }"
         @keydown.tab="trap($event)">
        <div class="menu-layer bg-ion" style="--layer: 0"></div>
        <div class="menu-layer bg-ink" style="--layer: 1"></div>

        <div class="relative z-10 flex h-full flex-col">
            <x-logo-mark class="menu-fade pointer-events-none absolute top-[12%] -right-[30vw] h-[130vw] w-[75vw] text-ion-shadow" />

            <div class="wrap flex w-full items-center justify-between gap-5 border-b border-line py-[22px]">
                <a href="#top" @click="close()" aria-label="Emax Digital home" class="text-white">
                    <x-logo class="text-[30px]" />
                </a>
                <button type="button" x-ref="close" @click="close()" class="relative -mr-2 size-11 text-white">
                    <span class="sr-only">Close menu</span>
                    <span class="absolute top-1/2 left-2 h-[2.4px] w-7 bg-current transition duration-500 ease-snap"
                          :class="open ? 'rotate-45 translate-y-[-1px] delay-300' : '-translate-y-[6px]'"></span>
                    <span class="absolute top-1/2 left-2 h-[2.4px] w-7 bg-current transition duration-500 ease-snap"
                          :class="open ? '-rotate-45 translate-y-[-1px] delay-300' : 'translate-y-[4px]'"></span>
                </button>
            </div>

            <nav class="wrap relative flex w-full flex-1 flex-col justify-center" aria-label="Mobile">
                <ol class="m-0 list-none p-0">
                    @foreach ($menuLinks as $id => $label)
                        <li class="overflow-hidden">
                            <a href="#{{ $id }}" @click="close()" style="--i: {{ $loop->index }}"
                               class="menu-item group flex items-center gap-4 py-1.5 text-white">
                                <span class="w-7 shrink-0 font-label text-xs font-bold text-ion">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                <span class="min-w-0 font-display text-[clamp(38px,11.5vw,76px)] leading-[0.95] transition-colors duration-200 group-active:text-ion">{{ $label }}</span>
                                <x-icon.arrow-up-right class="ml-auto size-8 shrink-0 text-ion opacity-0 transition duration-200 group-active:opacity-100" />
                            </a>
                        </li>
                    @endforeach
                </ol>
            </nav>

            <div class="menu-fade wrap flex w-full flex-col gap-5 pb-[max(1.5rem,env(safe-area-inset-bottom))]">
                <a href="#contact" @click="close()" class="flex items-center justify-between bg-ion px-5 py-4 font-display text-xl text-ink active:bg-white">
                    Start a project
                    <x-icon.arrow-up-right class="size-6" stroke-width="2.6" />
                </a>
                <a href="mailto:{{ $email }}" class="self-start text-[13px] text-soft underline decoration-ion underline-offset-4">{{ $email }}</a>
            </div>
        </div>
    </div>
</div>
