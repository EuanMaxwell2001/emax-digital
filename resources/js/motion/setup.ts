// Global motion for EMAX Digital: registered once on the client, before the first page initializes.
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { ease, isMobile, onPage, onScroll, prefersReducedMotion, setTransition, startScroll, stopScroll, scrollToTarget } from '../engine';
import { startSlash } from './slash';

const SLASH = 'polygon(44% 0%, 60% 0%, 56% 100%, 40% 100%)';
const FULL = 'polygon(0% 0%, 100% 0%, 100% 100%, 0% 100%)';

/** Page transition: a mint slash then an ink slash cut across the screen, left to right. */
function transition() {
  const parts = (c: HTMLElement) => ({
    mint: c.querySelector<HTMLElement>('.curtain__slash--mint')!,
    ink: c.querySelector<HTMLElement>('.curtain__slash--ink')!,
    mark: c.querySelector<HTMLElement>('.curtain__mark')!,
  });
  setTransition({
    leave: (c) => {
      closeMenu(false);
      const { mint, ink, mark } = parts(c);
      return gsap.timeline()
        .set(c, { autoAlpha: 1 })
        .set([mint, ink], { skewX: -15, xPercent: -135 })
        .set(mark, { skewX: 15, autoAlpha: 0, x: -30 })
        .to(mint, { xPercent: 0, duration: 0.7, ease: ease.inOut })
        .to(ink, { xPercent: 0, duration: 0.7, ease: ease.inOut }, 0.12)
        .to(mark, { autoAlpha: 1, x: 0, duration: 0.45, ease: ease.out }, 0.55);
    },
    enter: (c) => {
      const { mint, ink, mark } = parts(c);
      return gsap.timeline()
        .to(mark, { autoAlpha: 0, x: 30, duration: 0.3, ease: ease.soft })
        .to(ink, { xPercent: 135, duration: 0.75, ease: ease.inOut }, 0.05)
        .to(mint, { xPercent: 135, duration: 0.75, ease: ease.inOut }, 0.17)
        .set(c, { autoAlpha: 0 });
    },
  });
}

/** Case pages: the title drives in and the hero image opens from a slash. */
function caseIntro() {
  onPage(({ intro }) => {
    if (prefersReducedMotion()) return;
    const words = document.querySelectorAll('.case__word > span');
    if (words.length) intro.fromTo(words, { x: '-20%' }, { x: '0%', duration: 1.3, ease: ease.out, stagger: 0.08 }, 0.05);
    const hero = document.querySelector('[data-hero-open]');
    if (hero) intro.fromTo(hero, { clipPath: SLASH }, { clipPath: FULL, duration: isMobile() ? 0.9 : 1.3, ease: ease.inOut }, 0.1);
  });
}

/** Images open from a slash to a full frame as they scroll in. */
function openMasks() {
  onPage(() => {
    if (prefersReducedMotion()) return;
    document.querySelectorAll<HTMLElement>('[data-open]').forEach((el) => {
      gsap.fromTo(el, { clipPath: SLASH }, {
        clipPath: FULL, ease: 'none',
        scrollTrigger: { trigger: el, start: 'top 92%', end: 'top 42%', scrub: 0.6 },
      });
    });
  });
}

/** Statement: the words fill in as you scroll through the mint section. */
function statement() {
  onPage(() => {
    const sec = document.querySelector<HTMLElement>('[data-statement]');
    if (!sec || prefersReducedMotion()) return;
    const words = sec.querySelectorAll('.statement__word');
    const mobile = isMobile();
    gsap.to(words, {
      opacity: 1, ease: 'none', stagger: 0.12,
      scrollTrigger: mobile
        ? { trigger: sec.querySelector('.statement__title'), start: 'top 85%', end: 'bottom 45%', scrub: 0.5 }
        : { trigger: sec, start: 'top 55%', end: 'top -55%', scrub: 0.5 },
    });
    gsap.from(sec.querySelectorAll('.fact'), {
      y: 40, autoAlpha: 0, duration: 1, ease: ease.out, stagger: 0.1,
      scrollTrigger: { trigger: sec.querySelector('.facts'), start: 'top 92%', once: true },
    });
  });
}

/** Marquee: runs on its own and speeds up (or reverses) with scroll velocity. */
function marquee() {
  onPage(() => {
    const track = document.querySelector<HTMLElement>('[data-marquee] [data-track]');
    if (!track || prefersReducedMotion()) return;
    const loop = gsap.to(track, { xPercent: -50, ease: 'none', duration: 30, repeat: -1 });
    const off = onScroll(({ velocity }) => {
      const dir = velocity < 0 ? -1 : 1;
      gsap.to(loop, { timeScale: dir * (1 + Math.min(Math.abs(velocity) / 6, 6)), duration: 0.4, overwrite: true });
    });
    const st = ScrollTrigger.create({ trigger: track, start: 'top bottom', end: 'bottom top', onToggle: (s) => (s.isActive ? loop.resume() : loop.pause()) });
    return () => { off(); st.kill(); };
  });
}

/** Work: the cards stack; each one sinks back as the next slides over it. */
function cards() {
  onPage(() => {
    const list = document.querySelector('[data-cards]');
    if (!list || prefersReducedMotion() || isMobile()) return;
    const items = [...list.querySelectorAll<HTMLElement>('.card')];
    items.slice(0, -1).forEach((card, i) => {
      const next = items[i + 1];
      gsap.to(card.querySelector('.card__link'), {
        scale: 0.92, '--dim': 0.7, ease: 'none',
        scrollTrigger: { trigger: next, start: 'top bottom', end: () => `top ${next.getBoundingClientRect().height ? parseFloat(getComputedStyle(next).top) : 0}px`, scrub: true, invalidateOnRefresh: true },
      });
    });
  });
}

/** Process: a horizontal run on desktop, the logo's rule filling as you go. */
function process() {
  onPage(() => {
    const sec = document.querySelector<HTMLElement>('[data-process]');
    if (!sec || prefersReducedMotion() || isMobile()) return;
    const pin = sec.querySelector<HTMLElement>('.process__pin')!;
    const steps = sec.querySelector<HTMLElement>('[data-steps]')!;
    const fill = sec.querySelector<HTMLElement>('[data-rule]')!;
    const mark = sec.querySelector<HTMLElement>('[data-rule-mark]')!;
    const rule = mark.parentElement!;
    const dist = () => Math.max(0, steps.scrollWidth - innerWidth);
    gsap.set(mark, { skewX: -15 });
    gsap.timeline({
      scrollTrigger: { trigger: sec, start: 'top top', end: () => `+=${dist() + innerHeight * 0.4}`, pin, scrub: 0.8, invalidateOnRefresh: true },
    })
      .to(steps, { x: () => -dist(), ease: 'none' }, 0)
      .fromTo(fill, { scaleX: 0 }, { scaleX: 1, ease: 'none' }, 0)
      .fromTo(mark, { x: 0 }, { x: () => rule.clientWidth, ease: 'none' }, 0);
  });
}

/** Header: solid once you leave the top, steps aside on the way down, returns on the way up. */
function header() {
  let last = 0;
  onScroll(({ y }) => {
    const h = document.documentElement.classList;
    h.toggle('hdr-solid', y > 24);
    if (Math.abs(y - last) < 6) return;
    h.toggle('hdr-away', y > 240 && y > last && !h.contains('menu-open'));
    last = y;
  });
  onPage(() => {
    document.documentElement.classList.remove('hdr-away');
    document.documentElement.classList.toggle('hdr-solid', scrollY > 24);
  });
}

/* ---------- Menu (phones) ---------- */
let menuKey: ((e: KeyboardEvent) => void) | null = null;
function closeMenu(restoreFocus = true) {
  const menu = document.querySelector<HTMLElement>('[data-menu]');
  const btn = document.querySelector<HTMLButtonElement>('[data-menu-btn]');
  if (!menu || menu.hidden) return;
  menu.hidden = true;
  btn?.setAttribute('aria-expanded', 'false');
  btn?.querySelector('.menu-btn__label')?.replaceChildren('Menu');
  document.documentElement.classList.remove('menu-open');
  if (menuKey) document.removeEventListener('keydown', menuKey);
  menuKey = null;
  startScroll();
  if (restoreFocus) btn?.focus();
}
function menu() {
  onPage(() => {
    const btn = document.querySelector<HTMLButtonElement>('[data-menu-btn]');
    const panel = document.querySelector<HTMLElement>('[data-menu]');
    if (!btn || !panel) return;
    const open = () => {
      panel.hidden = false;
      btn.setAttribute('aria-expanded', 'true');
      btn.querySelector('.menu-btn__label')?.replaceChildren('Close');
      document.documentElement.classList.add('menu-open');
      stopScroll();
      const links = panel.querySelectorAll<HTMLElement>('.menu__link');
      if (!prefersReducedMotion()) {
        gsap.fromTo(panel, { clipPath: 'inset(0% 0% 100% 0%)' }, { clipPath: 'inset(0% 0% 0% 0%)', duration: 0.6, ease: ease.inOut });
        gsap.fromTo(links, { yPercent: 110 }, { yPercent: 0, duration: 0.8, ease: ease.out, stagger: 0.05, delay: 0.2 });
      }
      links[0]?.focus();
      menuKey = (e: KeyboardEvent) => {
        if (e.key === 'Escape') return closeMenu();
        if (e.key !== 'Tab') return;
        const focusables = [btn, ...panel.querySelectorAll<HTMLElement>('a')];
        const i = focusables.indexOf(document.activeElement as HTMLElement);
        const next = e.shiftKey ? (i <= 0 ? focusables.length - 1 : i - 1) : (i === focusables.length - 1 ? 0 : i + 1);
        e.preventDefault();
        focusables[next].focus();
      };
      document.addEventListener('keydown', menuKey);
    };
    const toggle = () => (panel.hidden ? open() : closeMenu());
    // Same-page anchors (#work) close the menu, then scroll.
    const onLink = (e: Event) => {
      const a = (e.target as HTMLElement).closest('a');
      if (!a) return;
      const url = new URL(a.href, location.href);
      if (url.pathname === location.pathname && url.hash) {
        e.preventDefault();
        closeMenu(false);
        scrollToTarget(url.hash);
      }
    };
    btn.addEventListener('click', toggle);
    panel.addEventListener('click', onLink);
    return () => { btn.removeEventListener('click', toggle); panel.removeEventListener('click', onLink); closeMenu(false); };
  });
}

export function setupMotion() {
  gsap.registerPlugin(ScrollTrigger);
  transition();
  header();
  menu();
  startSlash();
  caseIntro();
  openMasks();
  statement();
  marquee();
  cards();
  process();
}
