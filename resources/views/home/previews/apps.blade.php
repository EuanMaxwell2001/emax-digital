{{-- Phone with a booking screen --}}
<div class="flex h-full items-end justify-center overflow-hidden bg-mist pt-4">
    <div class="flex h-[200px] w-[128px] flex-col gap-2 rounded-t-[20px] border-[5px] border-b-0 border-ink bg-white p-2.5 text-ink">
        <span class="mx-auto h-1 w-8 rounded-full bg-ink"></span>
        <span class="font-display text-[13px] leading-none">Book a slot</span>
        <div class="grid grid-cols-4 gap-1">
            @foreach ([0, 1, 0, 0, 2, 0, 1, 0, 0, 0, 1, 2] as $slot)
                <span @class(['h-4', 'bg-pale' => $slot === 0, 'bg-ink' => $slot === 1, 'bg-ion' => $slot === 2])></span>
            @endforeach
        </div>
        <span class="mt-auto bg-ion py-1.5 text-center font-label text-[8px] font-bold tracking-[0.2em]">Confirm</span>
    </div>
</div>
