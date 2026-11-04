@props(['current' => 'none'])
@php
    // On the homepage, sections are same-page anchors (smooth scroll); elsewhere they lead home.
    $base = request()->is('/') ? '' : '/';
    $links = [
        ['id' => 'work', 'label' => 'Work', 'href' => "{$base}#work"],
        ['id' => 'services', 'label' => 'Services', 'href' => "{$base}#services"],
        ['id' => 'process', 'label' => 'Process', 'href' => "{$base}#process"],
        ['id' => 'about', 'label' => 'About', 'href' => "{$base}#about"],
    ];
@endphp
<header class="hdr" data-header>
  <a class="brand" href="/">
    <img class="brand__logo" src="/brand/emax-logo-light.png" width="800" height="212" alt="{{ config('site.name') }}" fetchpriority="high" />
  </a>
  <nav class="nav" aria-label="Main">
    <ul>
      @foreach ($links as $l)
        <li><a class="roll" href="{{ $l['href'] }}"@if ($current === $l['id']) aria-current="page"@endif><span class="roll__in"><span>{{ $l['label'] }}</span><span aria-hidden="true">{{ $l['label'] }}</span></span></a></li>
      @endforeach
    </ul>
  </nav>
  <a class="btn btn--sm hdr__cta" href="#contact" data-magnetic="0.2"><span class="btn__label">Start a project</span></a>
  <button class="menu-btn" type="button" aria-expanded="false" aria-controls="menu" data-menu-btn>
    <span class="menu-btn__label">Menu</span>
    <span class="menu-btn__slash" aria-hidden="true"></span>
  </button>
</header>

<div id="menu" class="menu" hidden data-menu>
  <nav aria-label="Menu">
    <ul class="menu__list">
      @foreach ($links as $l)
        <li><a class="menu__link" href="{{ $l['href'] }}"@if ($current === $l['id']) aria-current="page"@endif>{{ $l['label'] }}</a></li>
      @endforeach
      <li><a class="menu__link menu__link--mint" href="#contact">Start a project</a></li>
    </ul>
    <p class="menu__foot"><a href="mailto:{{ config('site.email') }}">{{ config('site.email') }}</a></p>
  </nav>
</div>
