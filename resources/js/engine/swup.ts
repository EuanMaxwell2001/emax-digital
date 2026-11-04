// Swup adapter (replaces the Astro ClientRouter adapter). Call start(setup) once.
// The leave animation runs in parallel with fetching the next page, as before:
//   animation:out:await  → leave()    curtain covers the screen while the page loads
//   content:replace      → cleanup() before the swap, syncFlags() after it
//   animation:in:await   → enter()    init the new page, uncover, play its intro
// #curtain and #cursor sit outside the containers, so they persist across visits.
import Swup from 'swup';
import SwupHeadPlugin from '@swup/head-plugin';
import SwupPreloadPlugin from '@swup/preload-plugin';
import { boot, cleanup, enter, leave, scrollToTarget, syncFlags } from './index';

export const containers = ['[data-header]', '#menu', '#main', '#contact'];

export function start(setup?: () => void) {
  const swup = new Swup({
    containers,
    animationSelector: false, // timing comes from the engine, not CSS transitions
    animateHistoryBrowsing: true, // back/forward get the slash wipe too
    linkToSelf: 'navigate', // the logo on the homepage replays the transition
    ignoreVisit: (url, { el } = {}) =>
      !!el?.closest('[data-no-swup]') || /^\/(admin|storage|livewire)(\/|$)/.test(url),
    plugins: [new SwupHeadPlugin({ persistAssets: true }), new SwupPreloadPlugin({ preloadInitialPage: false })],
  });

  swup.hooks.replace('animation:out:await', () => leave());
  swup.hooks.before('content:replace', () => cleanup());
  swup.hooks.on('content:replace', () => syncFlags());
  // The engine owns scrolling (Lenis): enter() resets it and lands on #hash targets.
  swup.hooks.replace('content:scroll', () => {});
  swup.hooks.replace('animation:in:await', () => enter());
  // Same-page anchors (#work, #contact) glide with Lenis.
  swup.hooks.replace('scroll:anchor', (_visit, { hash }) => {
    const el = swup.getAnchorElement(hash);
    if (el) scrollToTarget(el);
    return !!el;
  });

  // Announce the new page title to screen readers, as Astro's route announcer did.
  const announcer = document.createElement('div');
  announcer.className = 'sr-only';
  announcer.setAttribute('aria-live', 'assertive');
  announcer.setAttribute('aria-atomic', 'true');
  document.body.append(announcer);
  swup.hooks.on('page:view', () => {
    announcer.textContent = document.querySelector('h1')?.textContent?.trim() || document.title;
  });

  boot(setup);
  return swup;
}
