{{--
  The slash stage: the real content renders once (white on ink); a mint slash moves across it and
  shows an inert copy of the same text in ink (cloned in the browser by motion/slash.ts, so the
  served HTML has the text once).
    lines   display lines of the heading      id     heading id
    tag     h1 | h2                           meta   small label above the heading
    aside   second label                      sub    paragraph under the heading
    widen   the slash widens to fill the screen as the stage scrolls away (hero only)
--}}
@props(['lines', 'id', 'tag' => 'h1', 'meta' => null, 'aside' => null, 'sub' => null, 'widen' => false])
<section {{ $attributes->class(['stage', 'stage--widen' => $widen]) }} aria-labelledby="{{ $id }}" data-stage @if ($widen) data-widen="true" @endif>
  <div class="stage__sticky">
    <div class="stage__layer stage__layer--base">
      @if ($meta || $aside)<div class="stage__meta"><p class="label">{{ $meta }}</p>@if ($aside)<p class="label stage__aside">{{ $aside }}</p>@endif</div>@endif
      <{{ $tag }} id="{{ $id }}" class="stage__title">
        @foreach ($lines as $l)<span class="stage__line"><span class="stage__line-in">{{ $l }}</span></span>@endforeach
      </{{ $tag }}>
      @if ($sub)<p class="stage__sub">{{ $sub }}</p>@endif
    </div>
    <div class="slash" data-slash aria-hidden="true">
      <div class="slash__inner" data-slash-inner></div>
    </div>
    <div class="stage__ui">{{ $slot }}</div>
  </div>
</section>
