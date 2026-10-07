@php
    $projects = config('site.projects');
    $feature = array_shift($projects);
@endphp

<section id="work" class="bg-mist py-[140px] text-ink">
    <div class="wrap flex flex-col gap-[72px]">
        <div class="flex flex-wrap items-end justify-between gap-6">
            <x-section-heading>Selected<br>work</x-section-heading>
            <span style="--d: 150ms" class="reveal font-label text-[13px] font-bold tracking-[0.3em] text-slate">{{ date('Y') }} —</span>
        </div>

        @if ($feature)
            <x-project-card :project="$feature" feature />
        @endif

        @if ($projects)
            <div class="grid grid-cols-[repeat(auto-fit,minmax(min(100%,420px),1fr))] gap-10">
                @foreach ($projects as $project)
                    <x-project-card :project="$project" :tone="$loop->odd ? 'ink' : 'pale'" :class="$loop->even ? 'md:pt-20' : ''" :style="'--d: ' . ($loop->index * 150) . 'ms'" />
                @endforeach
            </div>
        @endif
    </div>
</section>
