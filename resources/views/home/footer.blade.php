<footer class="overflow-clip bg-ink">
    <div class="wrap">
        <div class="flex flex-wrap justify-between gap-4 border-t border-line pt-7 font-label text-xs font-medium tracking-[0.2em] text-muted">
            <span>© {{ date('Y') }} {{ config('app.name') }}</span>
            <span>Web design · Branding · Hosting</span>
        </div>
    </div>

    {{-- The logo starts below the fold, so the wrapper is what triggers the reveal. --}}
    <div data-reveal-trigger class="pt-10" aria-hidden="true">
        <div class="giant-in flex origin-bottom justify-center pb-[0.08em] text-[clamp(56px,17vw,300px)] text-ion">
            <x-logo />
        </div>
    </div>
</footer>
