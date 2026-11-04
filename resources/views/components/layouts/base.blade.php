@props([
    'title' => null,
    'description' => config('site.description'),
    'image' => '/og.png',
    'current' => 'none',
    'jsonLd' => null,
])
@php
    $site = config('site');
    $fullTitle = $title ? "{$title} | {$site['name']}" : "{$site['name']} | Websites built to stand out";
    $canonical = url()->current();
    $ogImage = url($image);
    $org = [
        '@context' => 'https://schema.org',
        '@type' => 'ProfessionalService',
        'name' => $site['name'],
        'description' => $site['description'],
        'url' => url('/'),
        'email' => $site['email'],
        'logo' => url('/favicon.svg'),
        'areaServed' => 'GB',
        'knowsAbout' => ['Web design', 'Web development', 'Branding', 'Website hosting', 'Web apps'],
    ];
    $json = fn ($data) => json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG);
@endphp
<!doctype html>
<html lang="en-GB">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <title>{{ $fullTitle }}</title>
    <meta name="description" content="{{ $description }}" />
    <link rel="canonical" href="{{ $canonical }}" />
    <meta name="theme-color" content="#12181c" />
    <link rel="icon" href="/favicon.svg" type="image/svg+xml" />
    <link rel="icon" href="/favicon-32.png" sizes="32x32" type="image/png" />
    <link rel="apple-touch-icon" href="/apple-touch-icon.png" />
    <link rel="sitemap" href="/sitemap.xml" />
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="{{ $site['name'] }}" />
    <meta property="og:locale" content="en_GB" />
    <meta property="og:title" content="{{ $fullTitle }}" />
    <meta property="og:description" content="{{ $description }}" />
    <meta property="og:url" content="{{ $canonical }}" />
    <meta property="og:image" content="{{ $ogImage }}" />
    <meta property="og:image:width" content="1200" />
    <meta property="og:image:height" content="630" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="{{ $fullTitle }}" />
    <meta name="twitter:description" content="{{ $description }}" />
    <meta name="twitter:image" content="{{ $ogImage }}" />
    <script type="application/ld+json">{!! $json($org) !!}</script>
    @if ($jsonLd)
    <script type="application/ld+json">{!! $json($jsonLd) !!}</script>
    @endif
    <link rel="preload" href="/fonts/mona-italic-display.woff2" as="font" type="font/woff2" crossorigin />
    <link rel="preload" href="/fonts/mona-text.woff2" as="font" type="font/woff2" crossorigin />
    <script>
      document.documentElement.classList.add('js');
      if (!matchMedia('(prefers-reduced-motion: reduce)').matches) document.documentElement.classList.add('motion');
    </script>
    @if (Vite::isRunningHot())
      @vite(['resources/css/app.css', 'resources/js/app.ts'])
    @else
      {{-- Built CSS is inlined (as Astro did) so first paint needs no extra request. --}}
      <style>{!! Vite::content('resources/css/app.css') !!}</style>
      @vite('resources/js/app.ts')
    @endif
  </head>
  <body>
    <a class="skip-link" href="#main">Skip to content</a>
    <div id="curtain" aria-hidden="true">
      <div class="curtain__slash curtain__slash--mint"></div>
      <div class="curtain__slash curtain__slash--ink"><img class="curtain__mark" src="/brand/emax-logo-light.png" width="800" height="212" alt="" /></div>
    </div>
    <div id="cursor" aria-hidden="true"><span data-cursor-label></span></div>
    <x-header :current="$current" />
    <main id="main" tabindex="-1">
      {{ $slot }}
    </main>
    <x-contact />
  </body>
</html>
