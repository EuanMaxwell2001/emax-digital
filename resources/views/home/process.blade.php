@php($steps = config('site.process'))

<section id="process" class="bg-ion pt-[130px] pb-[120px] text-ink">
    <div class="wrap grid gap-14 md:grid-cols-[minmax(0,5fr)_minmax(0,7fr)] md:gap-16">
        <div class="flex flex-col gap-6 md:sticky md:top-28 md:self-start">
            <x-section-heading>How it<br>works</x-section-heading>
            <p class="reveal m-0 max-w-[340px]" style="--d: 150ms">Four steps, no surprises. You'll always know what's happening and what comes next.</p>
        </div>

        {{-- Cards pin as you scroll and stack on top of each other --}}
        <ol class="m-0 flex list-none flex-col gap-6 p-0">
            @foreach ($steps as $step)
                @php($dark = $loop->odd)
                <li class="sticky" style="top: calc(6rem + {{ $loop->index }} * 1.75rem)">
                    <article @class([
                        'reveal flex min-h-[280px] flex-col justify-between gap-10 p-7 shadow-[0_-12px_40px_-12px_rgba(18,24,28,.45)] md:min-h-[320px] md:p-10',
                        'bg-ink text-white' => $dark,
                        'bg-white text-ink' => ! $dark,
                    ])>
                        <div class="flex items-start justify-between gap-6">
                            <span @class(['font-display text-[clamp(80px,9vw,128px)] leading-[0.8]', 'text-ion' => $dark, 'text-ink' => ! $dark])>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <span @class(['font-label pt-2 text-xs font-bold tracking-[0.24em]', 'text-muted' => $dark, 'text-slate' => ! $dark])>Step {{ $loop->iteration }} / {{ count($steps) }}</span>
                        </div>
                        <div class="flex flex-col gap-3">
                            <h3 class="m-0 font-display text-[clamp(32px,3.6vw,52px)] leading-none">{{ $step['title'] }}</h3>
                            <p @class(['m-0 max-w-[420px] text-base leading-[26px]', 'text-soft' => $dark, 'text-slate' => ! $dark])>{{ $step['blurb'] }}</p>
                        </div>
                    </article>
                </li>
            @endforeach
        </ol>
    </div>
</section>

{{-- Values marquee --}}
<div class="overflow-clip border-y border-line bg-ink py-[22px]" aria-hidden="true">
    <div class="flex w-max animate-marquee-rev gap-9 font-display text-[30px] leading-9 whitespace-nowrap text-shade">
        @for ($i = 0; $i < 4; $i++)
            @foreach (config('site.values') as $value)
                <span>{{ $value }}</span><span class="text-ion">✦</span>
            @endforeach
        @endfor
    </div>
</div>
