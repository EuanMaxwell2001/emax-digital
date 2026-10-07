{{-- Brand kit: mark, palette, type --}}
<div class="flex h-full flex-col justify-between bg-ion p-4 text-ink">
    <div class="flex items-start justify-between">
        <svg class="size-16" viewBox="0 0 120 120" aria-hidden="true"><rect width="120" height="120" rx="26" fill="#12181c"/><g transform="translate(44 20) skewX(-12.7)" fill="#3df5c4"><rect width="19" height="80"/><rect width="52" height="18"/><rect y="31" width="42" height="18"/><rect y="62" width="52" height="18"/></g></svg>
        <span class="text-[40px] leading-none font-black italic [font-stretch:125%]">Aa</span>
    </div>
    <div class="grid grid-cols-4 gap-1.5">
        @foreach (['bg-ink', 'bg-white', 'bg-deep-ion', 'bg-mist'] as $swatch)
            <span class="h-9 {{ $swatch }} ring-1 ring-ink/15"></span>
        @endforeach
    </div>
    <span class="font-label text-[10px] font-bold tracking-[0.24em]">Logo · Colour · Type</span>
</div>
