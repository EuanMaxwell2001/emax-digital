<section id="services" class="bg-ink pt-[140px] pb-[120px]">
    <div class="wrap flex flex-wrap items-end justify-between gap-6 pb-14">
        <x-section-heading>What<br>I do</x-section-heading>
        <p class="reveal m-0 max-w-[380px] text-muted" style="--d: 150ms">Everything you need to get online and stay there, from first sketch to keeping the lights on.</p>
    </div>

    <div x-data="servicePreview" @mousemove="move($event)" @mouseleave="leave()" class="border-t border-line">
        @foreach (config('site.services') as $service)
            <a href="#contact" @mouseenter="enter({{ $loop->index }}, $event)" style="--d: {{ $loop->index * 70 }}ms" class="slide-in group block border-b border-line text-white transition-colors duration-350 ease-snap hover:bg-ion hover:text-ink">
                <div class="wrap flex flex-wrap items-center gap-x-10 gap-y-6 py-[34px]">
                    <span class="w-10 font-label text-sm font-bold text-ion group-hover:text-ink">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    <span class="flex-[1_1_420px] font-display text-[clamp(36px,5.6vw,88px)] leading-none transition-transform duration-500 ease-snap group-hover:translate-x-7">{{ $service['title'] }}</span>
                    <span class="flex-[0_1_320px] text-base leading-[26px] text-muted group-hover:text-ink">{{ $service['blurb'] }}</span>
                    <x-icon.arrow-up-right class="hidden size-11 -translate-x-[30px] opacity-0 transition duration-500 ease-snap group-hover:translate-x-0 group-hover:opacity-100 sm:block" />
                </div>
            </a>
        @endforeach

        {{-- Cursor-following preview (mouse users only) --}}
        <template x-if="enabled">
            <div x-ref="card" class="pointer-events-none fixed top-0 left-0 z-30" aria-hidden="true">
                <div class="relative h-[220px] w-[300px] overflow-hidden shadow-[0_30px_60px_-20px_rgba(0,0,0,.6)] transition duration-300 ease-snap"
                     :class="active === null ? 'scale-50 opacity-0' : 'scale-100 opacity-100'">
                    @foreach (config('site.services') as $service)
                        <div class="absolute inset-0 transition duration-300 ease-snap"
                             :class="active === {{ $loop->index }} ? 'opacity-100' : ({{ $loop->index }} < active ? 'opacity-0 -translate-y-6' : 'opacity-0 translate-y-6')">
                            @include('home.previews.' . $service['preview'])
                        </div>
                    @endforeach
                </div>
            </div>
        </template>
    </div>
</section>
