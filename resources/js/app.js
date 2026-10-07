import Alpine from 'alpinejs';
import Lenis from 'lenis';

// Lights up the statement word by word as it scrolls through the viewport.
Alpine.data('wordReveal', (count) => ({
    lit: 0,
    init() {
        const update = () => {
            const r = this.$el.getBoundingClientRect();
            const vh = window.innerHeight || document.documentElement.clientHeight;
            const start = vh * 0.85;
            const end = vh * 0.35;
            const p = Math.max(0, Math.min(1, (start - r.top) / ((start - end) + r.height)));
            this.lit = p * count;
        };
        window.addEventListener('scroll', update, { passive: true });
        window.addEventListener('resize', update);
        update();
    },
}));

const finePointer = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

// Smooth wheel scrolling (mouse/trackpad only; touch keeps native scrolling).
// It drives the real scroll position, so scroll events and CSS scroll timelines still work.
if (finePointer && !reducedMotion) {
    window.lenis = new Lenis({ autoRaf: true, anchors: true, lerp: 0.09 });
}

// Floating preview card that trails the cursor over the services list.
Alpine.data('servicePreview', () => ({
    enabled: finePointer,
    active: null,
    x: 0, y: 0, cx: 0, cy: 0,
    raf: null,
    enter(i, e) {
        if (!this.enabled) return;
        if (this.active === null) {
            this.cx = this.x = e.clientX;
            this.cy = this.y = e.clientY;
            this.render(0);
        }
        this.active = i;
    },
    leave() {
        this.active = null;
    },
    move(e) {
        if (!this.enabled) return;
        this.x = e.clientX;
        this.y = e.clientY;
        if (!this.raf) this.raf = requestAnimationFrame(() => this.tick());
    },
    tick() {
        const ease = reducedMotion ? 1 : 0.14;
        const dx = this.x - this.cx;
        const dy = this.y - this.cy;
        this.cx += dx * ease;
        this.cy += dy * ease;
        this.render(Math.max(-14, Math.min(14, dx * 0.12)));
        this.raf = Math.abs(dx) + Math.abs(dy) > 0.5 ? requestAnimationFrame(() => this.tick()) : null;
    },
    render(tilt) {
        this.$refs.card.style.transform = `translate3d(${this.cx}px, ${this.cy}px, 0) translate(-50%, -50%) rotate(${reducedMotion ? 0 : tilt}deg)`;
    },
}));

// Full-screen mobile menu: open/close state, scroll lock, focus handling.
// `closing` keeps the panels visible while they wipe out (see .menu in app.css).
Alpine.data('mobileMenu', () => ({
    open: false,
    closing: false,
    scrolled: false,
    returnTo: null,
    timer: null,
    init() {
        const onScroll = () => { this.scrolled = window.scrollY > 400; };
        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();

        this.$watch('open', (isOpen) => {
            document.documentElement.classList.toggle('menu-open', isOpen);
            isOpen ? window.lenis?.stop() : window.lenis?.start();
        });

        window.matchMedia('(min-width: 48rem)').addEventListener('change', (e) => e.matches && this.close());
    },
    show() {
        clearTimeout(this.timer);
        this.closing = false;
        this.returnTo = document.activeElement;
        this.open = true;
        this.$nextTick(() => this.$refs.close.focus({ preventScroll: true }));
    },
    close() {
        if (!this.open) return;
        this.open = false;
        this.closing = true;
        this.timer = setTimeout(() => { this.closing = false; }, reducedMotion ? 0 : 950);
        this.returnTo?.focus?.({ preventScroll: true });
    },
    trap(e) {
        const items = [...this.$refs.menu.querySelectorAll('a[href], button')];
        const first = items[0];
        const last = items[items.length - 1];
        if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
        else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
    },
}));

// Copy-to-clipboard button with a short "copied" state.
Alpine.data('copy', (text) => ({
    copied: false,
    async copy() {
        try {
            await navigator.clipboard.writeText(text);
            this.copied = true;
            setTimeout(() => { this.copied = false; }, 2000);
        } catch {
            window.location.href = `mailto:${text}`;
        }
    },
}));

// x-magnetic: element drifts a little toward the cursor while hovered.
Alpine.directive('magnetic', (el, { expression }) => {
    if (!finePointer || reducedMotion) return;
    const strength = parseFloat(expression) || 0.3;
    el.style.transition = 'transform .5s cubic-bezier(.2,.8,.2,1), scale .3s ease, background-color .3s ease, color .3s ease';
    el.addEventListener('mousemove', (e) => {
        const r = el.getBoundingClientRect();
        const x = (e.clientX - (r.left + r.width / 2)) * strength;
        const y = (e.clientY - (r.top + r.height / 2)) * strength;
        el.style.transform = `translate(${x}px, ${y}px)`;
    });
    el.addEventListener('mouseleave', () => { el.style.transform = ''; });
});

window.Alpine = Alpine;
Alpine.start();

// Appear on scroll: add .is-in once an element enters view (CSS does the animating).
// An element whose start position is clipped out of view can name a wrapper to watch
// instead, via a [data-reveal-trigger] ancestor.
const reveals = document.querySelectorAll('.reveal, .slide-in, .grow-in, .bar-in, .clip-in, .giant-in');

if ('IntersectionObserver' in window) {
    const targets = new Map();

    const io = new IntersectionObserver((entries) => {
        entries.forEach((e) => {
            if (e.isIntersecting) {
                targets.get(e.target).forEach((el) => el.classList.add('is-in'));
                io.unobserve(e.target);
            }
        });
    }, { threshold: 0.15, rootMargin: '0px 0px -8% 0px' });

    reveals.forEach((el) => {
        const trigger = el.closest('[data-reveal-trigger]') ?? el;
        if (!targets.has(trigger)) {
            targets.set(trigger, []);
            io.observe(trigger);
        }
        targets.get(trigger).push(el);
    });
} else {
    reveals.forEach((el) => el.classList.add('is-in'));
}
