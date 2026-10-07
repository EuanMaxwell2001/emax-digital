{{--
    Full lockup from design/logo-sheet: slash + EMAX, with DIGITAL and a rule underneath.
    Everything is in em, so set the size with a font-size class (e.g. text-[30px]).
--}}
<span {{ $attributes->class('inline-flex flex-col gap-[0.15em] whitespace-nowrap') }}>
    <span class="flex items-center gap-[0.18em]">
        <x-logo-mark class="h-[0.91em] w-[0.515em] flex-none text-ion" />
        <span class="font-display leading-[0.97]">Emax</span>
    </span>
    <span class="flex items-center gap-[0.15em] pl-[0.7em]">
        {{-- Sheet ratio is 1/6 of EMAX; floor it so it stays legible at nav size --}}
        <span class="font-label text-[max(0.167em,8px)] leading-none font-medium tracking-[0.5em]">Digital</span>
        <span class="h-[max(0.03em,1.5px)] flex-1 bg-ion"></span>
    </span>
</span>
