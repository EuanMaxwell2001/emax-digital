@php($words = explode(' ', config('site.statement')))

<section class="-mt-10 bg-mist pt-[160px] pb-[180px] text-ink md:pt-[220px] md:pb-[240px]">
    <div class="wrap flex flex-col gap-12">
        <span class="font-label text-[13px] font-bold tracking-[0.3em] text-slate">The idea</span>

        <p x-data="wordReveal({{ count($words) }})" aria-label="{{ config('site.statement') }}"
           class="m-0 flex flex-wrap gap-x-[0.26em] [font-stretch:125%] text-[clamp(32px,5vw,76px)] leading-[1.08] font-extrabold tracking-[-0.015em]">
            @foreach ($words as $i => $word)
                <span aria-hidden="true" class="text-ghost transition-colors duration-250" :class="{{ $i }} < lit && 'text-ink!'">{{ $word }}</span>
            @endforeach
        </p>
    </div>
</section>
