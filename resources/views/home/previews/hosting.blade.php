{{-- Status panel --}}
<div class="flex h-full flex-col gap-1.5 bg-ink p-4 font-mono text-[12px] leading-5 text-soft ring-1 ring-line ring-inset">
    <span class="text-muted">$ emax status</span>
    <span class="flex justify-between"><span>yourbusiness.co.uk</span><span class="text-ion">● online</span></span>
    <span class="flex justify-between"><span>uptime</span><span class="text-white">99.98%</span></span>
    <span class="flex justify-between"><span>ssl</span><span class="text-white">valid</span></span>
    <span class="flex justify-between"><span>backup</span><span class="text-white">02:00 ✓</span></span>
    <div class="mt-auto flex h-8 items-end gap-[3px]">
        @foreach ([40, 55, 35, 70, 60, 85, 50, 65, 90, 45, 75, 60, 80, 55, 95, 70] as $h)
            <span class="flex-1 bg-ion/80" style="height: {{ $h }}%"></span>
        @endforeach
    </div>
</div>
