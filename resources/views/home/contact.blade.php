@php($email = config('site.email'))

<section id="contact" class="bg-ink pt-[150px] pb-20">
    <div class="wrap flex flex-col gap-14">
        <h2 class="slide-in m-0 font-display text-[clamp(54px,10vw,160px)] leading-[0.88] tracking-[-0.02em]">Got a<br>project<span class="text-ion">?</span></h2>

        <div class="flex flex-wrap items-center justify-between gap-10">
            <div class="reveal flex flex-col gap-2.5" style="--d: 150ms">
                <span class="text-muted">Tell me what you need. I'll get back to you within a day.</span>
                <div class="flex flex-wrap items-center gap-x-5 gap-y-3">
                    <a href="mailto:{{ $email }}" class="[font-stretch:125%] text-[clamp(22px,2.6vw,36px)] leading-[1.2] font-extrabold break-all text-white underline decoration-ion decoration-4 underline-offset-8 transition-colors hover:text-ion">{{ $email }}</a>
                    <button type="button" x-data="copy(@js($email))" @click="copy()"
                            class="border border-line px-3 py-1.5 font-label text-[11px] font-bold tracking-[0.2em] text-muted transition-colors hover:border-ion hover:text-ion">
                        <span x-text="copied ? 'Copied ✓' : 'Copy'">Copy</span>
                    </button>
                </div>
            </div>

            <div x-magnetic="0.25">
                <a href="mailto:{{ $email }}" style="--d: 300ms" class="grow-in flex size-[220px] flex-col items-center justify-center gap-2 rounded-full bg-ion text-center font-display text-[22px] leading-6 text-ink transition duration-300 ease-snap hover:-rotate-[4deg] hover:bg-white">
                    Start a<br>project
                    <x-icon.arrow-up-right class="size-[34px]" stroke-width="2.6" />
                </a>
            </div>
        </div>
    </div>
</section>
