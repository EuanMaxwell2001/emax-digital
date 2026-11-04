// The concept driver: the EMAX slash.
// One mint parallelogram per stage. Its x follows the pointer (desktop), sways by itself at rest
// (and on touch), shoots in from the left on the intro, and on the hero widens with scroll until
// it covers the screen. Inside it, an aria-hidden copy of the stage text is counter-transformed so
// it lines up exactly with the real text underneath: whatever the slash crosses turns ink-on-mint.
// Transforms only (plus the width during the widen), one clock, paused off screen.
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { ease, isMobile, isTouch, onPage, prefersReducedMotion } from '../engine';

const K = Math.tan((15 * Math.PI) / 180);

export function startSlash() {
  onPage(({ intro }) => {
    const stage = document.querySelector<HTMLElement>('[data-stage]');
    if (!stage) return;
    const sticky = stage.querySelector<HTMLElement>('.stage__sticky')!;
    const slash = stage.querySelector<HTMLElement>('[data-slash]')!;
    const inner = stage.querySelector<HTMLElement>('[data-slash-inner]')!;
    const ui = stage.querySelector<HTMLElement>('.stage__ui');
    const widen = stage.dataset.widen === 'true';

    // The ink copy of the stage text, inside the slash. Headings become plain divs, ids are dropped.
    if (!inner.firstElementChild) {
      const copy = stage.querySelector<HTMLElement>('.stage__layer--base')!.cloneNode(true) as HTMLElement;
      copy.classList.replace('stage__layer--base', 'stage__layer--cut');
      copy.querySelectorAll('h1, h2').forEach((h) => {
        const d = document.createElement('div');
        d.className = h.className;
        d.innerHTML = h.innerHTML;
        h.replaceWith(d);
      });
      copy.querySelectorAll('[id]').forEach((el) => el.removeAttribute('id'));
      copy.setAttribute('inert', '');
      inner.append(copy);
    }
    const reduced = prefersReducedMotion();
    const touch = isTouch();

    let W = 0, H = 0;
    const restFrac = () => (isMobile() ? 0.58 : stage.classList.contains('nf') ? 0.52 : 0.6);
    const restX = () => W * restFrac();
    const restW = () => W * (isMobile() ? 0.22 : 0.13);
    const measure = () => {
      W = sticky.clientWidth;
      H = sticky.clientHeight;
      stage.style.setProperty('--stage-w', `${W}px`);
    };

    let lastX = NaN, lastW = NaN;
    const apply = (x: number, w: number) => {
      if (!(Math.abs(x - lastX) <= 0.05)) {
        slash.style.transform = `translate3d(${x.toFixed(2)}px,0,0) skewX(-15deg)`;
        inner.style.transform = `skewX(15deg) translate3d(${(-x).toFixed(2)}px,0,0)`;
        lastX = x;
      }
      if (!(Math.abs(w - lastW) <= 0.05)) { slash.style.width = `${w.toFixed(2)}px`; lastW = w; }
    };

    measure();
    if (reduced) {
      apply(restX(), restW());
      const onResize = () => { measure(); apply(restX(), restW()); };
      addEventListener('resize', onResize);
      return () => removeEventListener('resize', onResize);
    }

    // State
    const s = { x: -W, sweep: 1, open: 0 }; // sweep: 1 = still off to the left, 0 = arrived
    let started = false;
    let aim: number | null = null;
    let movedAt = 0;

    // Intro: the slash cuts in from the left edge; the lines drive in behind it.
    intro.fromTo(s, { sweep: 1 }, { sweep: 0, duration: 1.5, ease: ease.out }, 0.05);
    stage.querySelectorAll<HTMLElement>('.stage__layer').forEach((layer) => {
      intro.fromTo(layer.querySelectorAll('.stage__line-in'), { x: '-14%' }, { x: '0%', duration: 1.4, ease: ease.out, stagger: 0.07 }, 0.12);
    });

    const move = (e: PointerEvent) => {
      const r = sticky.getBoundingClientRect();
      const w = restW();
      // centre the slash under the pointer at the pointer's height (the slash leans)
      aim = e.clientX - r.left - w / 2 + K * (e.clientY - r.top - H / 2);
      movedAt = gsap.ticker.time;
    };
    const away = () => { aim = null; };
    if (!touch) {
      sticky.addEventListener('pointermove', move);
      sticky.addEventListener('pointerleave', away);
    }

    let st: ScrollTrigger | null = null;
    if (widen) {
      st = ScrollTrigger.create({
        trigger: stage, start: 'top top', end: 'bottom bottom', invalidateOnRefresh: true,
        onUpdate: (self) => { s.open = gsap.utils.clamp(0, 1, (self.progress - 0.08) / 0.72); },
      });
    }

    const tick = (t: number) => {
      const r = gsap.ticker.deltaRatio();
      if (aim !== null && t - movedAt > 2) aim = null;
      const sway = restX() + Math.sin(t * (touch ? 0.55 : 0.45)) * W * (touch ? 0.26 : 0.12);
      const target = aim ?? sway;
      if (!started) { s.x = target; started = true; }
      s.x += (target - s.x) * (1 - Math.pow(aim === null ? 0.965 : 0.86, r));

      // intro offset: from fully off screen on the left
      const w0 = restW();
      let x = s.x - s.sweep * (s.x + w0 + K * H + 40);
      let w = w0;
      if (s.open > 0) {
        const e = gsap.parseEase('power2.inOut')(s.open);
        const xEnd = -K * H * 0.5 - 6;
        const wEnd = W + K * H + 12;
        x += (xEnd - x) * e;
        w += (wEnd - w) * e;
      }
      apply(x, w);
      if (ui) ui.style.opacity = String(1 - gsap.utils.clamp(0, 1, s.open * 2.2));
    };

    let running = false;
    const run = (on: boolean) => {
      if (on === running) return;
      running = on;
      on ? gsap.ticker.add(tick) : gsap.ticker.remove(tick);
    };
    const vis = ScrollTrigger.create({ trigger: stage, start: 'top bottom', end: 'bottom top', onToggle: (self) => run(self.isActive) });
    run(true);

    const onResize = () => { measure(); lastX = lastW = NaN; };
    addEventListener('resize', onResize);

    return () => {
      run(false);
      vis.kill();
      st?.kill();
      removeEventListener('resize', onResize);
      sticky.removeEventListener('pointermove', move);
      sticky.removeEventListener('pointerleave', away);
    };
  });
}
