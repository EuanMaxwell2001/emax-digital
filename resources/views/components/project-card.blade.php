@props(['project', 'feature' => false, 'tone' => 'ion'])

@php
    $placeholder = [
        'ion' => 'bg-ion text-ink',
        'ink' => 'bg-ink text-muted',
        'pale' => 'bg-pale text-slate',
    ][$tone];
    $tag = $project['url'] ? 'a' : 'div';
@endphp

<{{ $tag }} @if ($project['url']) href="{{ $project['url'] }}" target="_blank" rel="noopener" @endif
    {{ $attributes->class(['group flex flex-col text-ink', $feature ? 'gap-[22px]' : 'gap-5']) }}>
    <div @class(['clip-in overflow-hidden bg-ink', $feature ? 'aspect-[16/8]' : 'aspect-[4/3]'])>
        @if ($project['image'])
            <img src="{{ asset($project['image']) }}" alt="{{ $project['client'] }} website" loading="lazy"
                 class="size-full object-cover transition-transform duration-800 ease-snap group-hover:scale-[1.04]">
        @else
            <div class="flex size-full items-center justify-center font-label text-[13px] font-bold tracking-[0.3em] transition-transform duration-800 ease-snap group-hover:scale-[1.04] {{ $placeholder }}">
                [ {{ $project['client'] }} screenshot ]
            </div>
        @endif
    </div>
    <div @class(['reveal flex items-baseline justify-between gap-3', 'flex-wrap' => $feature])>
        <span @class(['font-display leading-none', $feature ? 'text-[clamp(30px,3.4vw,52px)]' : 'text-[32px]'])>{{ $project['client'] }}</span>
        <span @class(['font-label font-bold tracking-[0.24em] text-slate', $feature ? 'text-[13px]' : 'text-xs'])>{{ $project['type'] }}</span>
    </div>
</{{ $tag }}>
