@php
    $jsonLd = array_filter([
        '@context' => 'https://schema.org',
        '@type' => 'CreativeWork',
        'name' => $p->title.': '.implode(', ', $p->services),
        'description' => $p->summary,
        'creator' => ['@type' => 'Organization', 'name' => config('site.name'), 'url' => url('/')],
        'dateCreated' => $p->year ? (string) $p->year : null,
    ]);
@endphp
<x-layouts.base :title="$p->title.' case study'" :description="$p->description" current="work" :json-ld="$jsonLd">
  <article class="case">
    <header class="case__head">
      <p class="case__kicker label"><a class="u-link" href="/#work">Selected work</a></p>
      <h1 class="case__title">@foreach (explode(' ', $p->title) as $w)<span class="case__word"><span>{{ $w }}</span></span> @endforeach</h1>
      <p class="case__summary">{{ $p->summary }}</p>
      <dl class="case__meta">
        <div><dt>Client</dt><dd>{{ $p->sector }}</dd></div>
        <div><dt>What EMAX did</dt><dd>{{ implode(', ', $p->services) }}</dd></div>
        @if ($p->year)<div><dt>Year</dt><dd>{{ $p->year }}</dd></div>@endif
      </dl>
    </header>

    <figure class="case__hero"><div class="case__hero-mask" data-hero-open>
      <x-picture :src="$p->cover_path" :meta="$p->cover_meta" :alt="$p->cover_alt"
        :widths="[640, 960, 1280, 1600]" sizes="(max-width: 767px) 100vw, 92vw"
        loading="eager" fetchpriority="high" />
    </div></figure>

    <section class="case__story" aria-label="The brief and the idea">
      <div class="case__block">
        <h2 class="case__h">The brief</h2>
        <p>{{ $p->context }}</p>
      </div>
      <div class="case__block case__block--b">
        <h2 class="case__h">The idea</h2>
        <p>{{ $p->idea }}</p>
      </div>
    </section>

    @if ($p->details->isNotEmpty())
    <section class="case__details" aria-labelledby="details-title">
      <h2 id="details-title" class="case__h case__h--wide">Up close</h2>
      @foreach ($p->details as $d)
        <figure class="detail detail--{{ $d->shape }}">
          <div class="detail__frame" data-open>
            <x-picture :src="$d->image_path" :meta="$d->image_meta" :alt="$d->alt"
              :widths="[480, 960, 1400]" sizes="(max-width: 767px) 100vw, 70vw" />
          </div>
          @if ($d->caption)<figcaption>{{ $d->caption }}</figcaption>@endif
        </figure>
      @endforeach
    </section>
    @endif

    <section class="case__outcome" aria-labelledby="outcome-title">
      <h2 id="outcome-title" class="case__h">The result</h2>
      <p class="case__outcome-text">{{ $p->outcome }}</p>
    </section>

    @if ($next && $next->isNot($p))
    <nav class="case__next" aria-label="Next project">
      <a class="next" href="{{ $next->url() }}" data-cursor="Next">
        <span class="next__fill" aria-hidden="true"></span>
        <span class="next__label label">Next project</span>
        <span class="next__title">{{ $next->title }}</span>
        <span class="next__sector">{{ $next->sector }} <span class="arrow" aria-hidden="true">→</span></span>
      </a>
    </nav>
    @endif
  </article>
</x-layouts.base>
