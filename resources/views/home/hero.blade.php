<section id="top" class="relative min-h-[680px] overflow-clip md:min-h-[860px]">
    <x-logo-mark class="parallax absolute -top-[60px] -right-[120px] h-[1070px] w-[620px] text-ion-shadow" />

    <div class="hero-out wrap relative flex flex-col gap-12 pt-16 pb-16 md:pt-24">
        <h1 class="m-0 font-display text-[clamp(54px,10.4vw,168px)] leading-[0.88] tracking-[-0.02em]">
            <span class="rise-line"><span class="[animation-delay:.05s]">Websites</span></span>
            <span class="rise-line"><span class="[animation-delay:.17s]">built to</span></span>
            <span class="rise-line"><span class="[animation-delay:.29s]">stand <span class="highlight -ml-[0.04em] px-[0.12em]">out.</span></span></span>
        </h1>

        <div class="flex flex-wrap items-end justify-between gap-10">
            <p class="animate-fade-up m-0 max-w-[520px] text-[20px] leading-8 text-soft">{{ config('site.description') }}</p>

            <div class="animate-fade-up relative size-[170px]">
                <svg class="absolute inset-0 animate-spin-slow" width="170" height="170" viewBox="0 0 170 170" aria-hidden="true">
                    <defs><path id="hero-ring" d="M85,85 m-68,0 a68,68 0 1,1 136,0 a68,68 0 1,1 -136,0"/></defs>
                    <text font-family="Archivo, sans-serif" font-size="12.5" font-weight="700" letter-spacing="3" fill="#fff"><textPath href="#hero-ring" textLength="420" lengthAdjust="spacing">SCROLL · SCROLL · SCROLL · SCROLL ·</textPath></text>
                </svg>
                <a href="#services" x-magnetic="0.35" aria-label="Scroll to services" class="absolute top-[45px] left-[45px] flex size-20 items-center justify-center rounded-full bg-ion text-ink transition duration-300 ease-snap hover:scale-110">
                    <x-icon.arrow-down class="size-[26px]" />
                </a>
            </div>
        </div>
    </div>
</section>

{{-- Tilted marquee band --}}
<div class="relative z-[2] -mx-10 -rotate-2 overflow-clip bg-ion py-[18px] text-ink" aria-hidden="true">
    <div class="flex w-max animate-marquee gap-10 font-display text-[clamp(30px,4vw,44px)] leading-[52px] whitespace-nowrap">
        @for ($i = 0; $i < 4; $i++)
            @foreach (config('site.marquee') as $item)
                <span>{{ $item }} /</span>
            @endforeach
        @endfor
    </div>
</div>
