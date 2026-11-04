@php
    $site = config('site');
    $statement = $site['statement'];
    $marquee = ['Websites', 'Branding', 'Hosting', 'Apps'];
@endphp
<x-layouts.base>
  <x-cut-stage
    id="hero-title"
    :lines="['Websites', 'built to', 'stand out.']"
    :meta="$site['hero']['meta']"
    aside="Websites / Branding / Hosting / Apps"
    :sub="$site['hero']['sub']"
    :widen="true"
    class="hero"
  >
    <div class="hero__ctas">
      <a class="btn btn--mint" href="#contact" data-magnetic="0.25"><span class="btn__label">Start a project</span></a>
      <a class="btn btn--ghost" href="#work"><span class="btn__label">See the work</span></a>
    </div>
    <p class="hero__cue" aria-hidden="true"><span class="hero__cue-line"></span>Scroll</p>
  </x-cut-stage>

  <section class="statement" aria-labelledby="statement-title" data-statement>
    <div class="statement__pin">
      <h2 id="statement-title" class="statement__title" aria-label="{{ $statement['words'] }}">@foreach (explode(' ', $statement['words']) as $w)<span class="statement__word" aria-hidden="true">{{ $w }} </span>@endforeach</h2>
      <div class="statement__foot">
        <p class="statement__lead">{{ $statement['lead'] }}</p>
        <ul class="facts">
          @foreach ($statement['facts'] as $f)
            <li class="fact">
              <span class="fact__figure">{{ $f['figure'] }}</span>
              <span class="fact__label">{{ $f['label'] }}</span>
            </li>
          @endforeach
        </ul>
      </div>
    </div>
  </section>

  <div class="marquee" data-marquee aria-hidden="true">
    <div class="marquee__track" data-track>
      @foreach ([0, 1] as $_)
        <div class="marquee__set">@foreach ([0, 1] as $__)@foreach ($marquee as $i => $m)<span @class(['marquee__item', 'marquee__item--solid' => $i % 2 === 1])>{{ $m }}<i class="marquee__slash"></i></span>@endforeach @endforeach</div>
      @endforeach
    </div>
  </div>

  <section id="work" class="work" aria-labelledby="work-title">
    <div class="sec-head">
      <p class="label">Selected work</p>
      <h2 id="work-title" class="sec-title" data-split="lines">Recent projects</h2>
      <p class="sec-note">Three small businesses that now have a website they are proud to send people to. All three are still hosted and looked after by EMAX.</p>
    </div>
    <ol class="cards" data-cards>
      @foreach ($projects as $i => $p)
        <li class="card" style="--i: {{ $i }}">
          <a class="card__link" href="{{ $p->url() }}" data-cursor="View">
            <div class="card__info">
              <p class="label card__sector">{{ $p->sector }}</p>
              <h3 class="card__title">{{ $p->title }}</h3>
              <p class="card__summary">{{ $p->summary }}</p>
              <ul class="tags" aria-label="Services">
                @foreach ($p->services as $s)<li>{{ $s }}</li>@endforeach
                @if ($p->year)<li class="tags__year">{{ $p->year }}</li>@endif
              </ul>
              <span class="card__cta">View project <span class="arrow" aria-hidden="true">→</span></span>
            </div>
            <div class="card__media">
              <div class="card__mask" data-open>
                <x-picture :src="$p->cover_path" :meta="$p->cover_meta" :alt="$p->cover_alt"
                  :widths="[640, 960, 1280, 1600]" sizes="(max-width: 767px) 100vw, 58vw" />
              </div>
            </div>
          </a>
        </li>
      @endforeach
    </ol>
  </section>

  <section id="services" class="services" aria-labelledby="services-title">
    <div class="sec-head">
      <p class="label">Services</p>
      <h2 id="services-title" class="sec-title" data-split="lines">What I do</h2>
      <p class="sec-note">Pick one or all four. Each row opens an email about it.</p>
    </div>
    <ul class="svc">
      @foreach ($site['services'] as $s)
        <li>
          <a class="svc__row" href="mailto:{{ $site['email'] }}?subject={{ rawurlencode($s['name'].' enquiry') }}">
            <span class="svc__fill" aria-hidden="true"></span>
            <span class="svc__name">{{ $s['name'] }}</span>
            <span class="svc__line">{{ $s['line'] }}</span>
            <span class="svc__detail">{{ $s['detail'] }}</span>
            <span class="svc__ask">Ask about {{ strtolower($s['name']) }} <span class="arrow" aria-hidden="true">↗</span></span>
          </a>
        </li>
      @endforeach
    </ul>
  </section>

  <section id="process" class="process" aria-labelledby="process-title" data-process>
    <div class="process__pin">
      <div class="process__head">
        <p class="label">Process</p>
        <h2 id="process-title" class="sec-title">Four steps, no surprises.</h2>
      </div>
      <ol class="steps" data-steps>
        @foreach ($site['process'] as $i => $s)
          <li class="step">
            <span class="step__n" aria-hidden="true">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
            <h3 class="step__name">{{ $s['step'] }}</h3>
            <p class="step__text">{{ $s['text'] }}</p>
          </li>
        @endforeach
      </ol>
      <div class="process__rule" aria-hidden="true"><span class="process__fill" data-rule></span><span class="process__slash" data-rule-mark></span></div>
    </div>
  </section>

  <section id="about" class="about" aria-labelledby="about-title">
    <div class="about__text">
      <p class="label">About</p>
      <h2 id="about-title" class="sec-title" data-split="lines">{{ $site['about']['heading'] }}</h2>
      @foreach ($site['about']['body'] as $para)<p>{{ $para }}</p>@endforeach
    </div>
    <figure class="about__photo">
      <div class="about__mask">
        <x-picture src="me.jpg" disk="static" alt="Portrait of the founder of EMAX Digital, smiling, arms folded, in a white T-shirt."
          :widths="[320, 480, 640]" sizes="(max-width: 767px) 70vw, 26vw" />
      </div>
      <figcaption>{{ $site['about']['caption'] }}</figcaption>
    </figure>
  </section>
</x-layouts.base>
