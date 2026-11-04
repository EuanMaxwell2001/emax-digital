// Engine core: framework-agnostic page lifecycle. This site has no WebGL and no loader:
// the hero intro is the first moment.
//   boot(setup)        once, on first load: setup → page init → intro
//   leave() → cleanup() before the route swaps (leave runs in parallel with the fetch)
//   enter()            after the new page is in the DOM: init → enter animation → intro
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { initScroll, resetScroll, scrollToTarget, startScroll, stopScroll } from './scroll';
import { initReveals } from './reveal';
import { playEnter, playLeave } from './transition';
import { prefersReducedMotion } from './env';
import { initCursor, initMagnetic } from './interactions';

export { onScroll, scrollState, scrollToTarget, stopScroll, startScroll } from './scroll';
export { setTransition } from './transition';
export { ease, isTouch, isMobile, prefersReducedMotion } from './env';

export type PageFn = (page: { intro: gsap.core.Timeline; path: string }) => void | (() => void);
const pageFns = new Set<PageFn>();
const cleanups: (() => void)[] = [];
let ctx: gsap.Context | null = null;
let intro: gsap.core.Timeline | null = null;
let ready = false;
let booted = false;

/** Run fn on every page load (and now, if a page is ready). Returns an unsubscribe. */
export function onPage(fn: PageFn) {
  pageFns.add(fn);
  if (ready) runPageFn(fn);
  return () => { pageFns.delete(fn); };
}

function runPageFn(fn: PageFn) {
  ctx?.add(() => {
    const c = fn({ intro: intro!, path: location.pathname });
    if (typeof c === 'function') cleanups.push(c);
  });
}

// Flags on <html>. Re-applied on every page in case the router touches root attributes.
export function syncFlags() {
  const h = document.documentElement.classList;
  h.add('js', 'is-booted', 'is-loaded');
  h.toggle('motion', !prefersReducedMotion());
}

function initPage() {
  syncFlags();
  ctx = gsap.context(() => {
    intro = initReveals(document);
    initMagnetic(document);
  });
  document.getElementById('cursor')?.classList.remove('is-active');
  ready = true;
  pageFns.forEach(runPageFn);
  requestAnimationFrame(() => ScrollTrigger.refresh());
}

export async function boot(setup?: () => void) {
  if (booted) return;
  booted = true;
  syncFlags();
  initScroll();
  initCursor();
  setup?.();
  initPage();
  startScroll();
  intro?.play();
}

export async function leave() {
  stopScroll();
  await playLeave();
}

/** Tear down the current page: reverts every tween, ScrollTrigger and split. */
export function cleanup() {
  if (!ready) return;
  ready = false;
  cleanups.splice(0).forEach((c) => c());
  ctx?.revert();
  ctx = null;
}

export async function enter({ transition = true, scrollTop = true } = {}) {
  if (!booted) return;
  cleanup();
  if (scrollTop) resetScroll();
  initPage();
  // Links like /#work land on their section, under the curtain.
  const target = location.hash ? document.getElementById(location.hash.slice(1)) : null;
  if (target) {
    ScrollTrigger.refresh();
    scrollToTarget(target, true);
  }
  if (transition) await playEnter();
  startScroll();
  intro?.play();
}
