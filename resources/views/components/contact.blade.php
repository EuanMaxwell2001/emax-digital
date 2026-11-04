@php
    $site = config('site');
    $sent = session('contact_sent');
@endphp
<footer id="contact" class="contact" aria-labelledby="contact-title">
  <div class="contact__slash" aria-hidden="true"></div>
  <div class="contact__top">
    <p class="label">Start a project</p>
    <h2 id="contact-title" class="contact__title" data-split="lines">Got a business worth shouting about?</h2>
    <p class="contact__lead">Tell us what you do and what you need.</p>

    <form class="cform" method="post" action="{{ route('contact.store') }}" data-contact-form @if ($sent) hidden @endif>
      @csrf
      <input type="hidden" name="page" value="{{ request()->getPathInfo() }}">
      <input type="hidden" name="started" value="{{ encrypt(now()->getTimestamp()) }}">
      <div class="cform__hp" aria-hidden="true">
        <label>Leave this empty <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
      </div>
      <div class="cform__field">
        <label class="label" for="cf-name">Your name</label>
        <input id="cf-name" name="name" type="text" autocomplete="name" required maxlength="120" value="{{ old('name') }}" @error('name') aria-invalid="true" aria-describedby="cf-name-err" @enderror>
        <p class="cform__err" id="cf-name-err" data-error="name">@error('name'){{ $message }}@enderror</p>
      </div>
      <div class="cform__field">
        <label class="label" for="cf-email">Email</label>
        <input id="cf-email" name="email" type="email" autocomplete="email" required maxlength="190" value="{{ old('email') }}" @error('email') aria-invalid="true" aria-describedby="cf-email-err" @enderror>
        <p class="cform__err" id="cf-email-err" data-error="email">@error('email'){{ $message }}@enderror</p>
      </div>
      <div class="cform__field cform__field--wide">
        <label class="label" for="cf-message">What do you need?</label>
        <textarea id="cf-message" name="message" rows="4" required maxlength="5000" @error('message') aria-invalid="true" aria-describedby="cf-message-err" @enderror>{{ old('message') }}</textarea>
        <p class="cform__err" id="cf-message-err" data-error="message">@error('message'){{ $message }}@enderror</p>
      </div>
      <div class="cform__foot">
        <button class="btn btn--ink" type="submit"><span class="btn__label">Send message</span></button>
        <p class="cform__status" role="status" data-status>@error('form'){{ $message }}@enderror</p>
      </div>
    </form>
    <div class="cform__done" data-contact-done tabindex="-1" @unless ($sent) hidden @endunless>
      <p class="cform__done-title">Thanks, that's with us.</p>
      <p>We'll be in touch soon. If it's urgent, email us below.</p>
    </div>
  </div>
  <a class="contact__email" href="mailto:{{ $site['email'] }}" data-magnetic="0.12">
    <span class="contact__email-fill" aria-hidden="true"></span>
    <span class="contact__email-text">{{ $site['email'] }}<span class="arrow" aria-hidden="true">↗</span></span>
  </a>
  <div class="contact__base">
    <img class="contact__logo" src="/brand/emax-logo-mono.png" width="800" height="212" alt="{{ $site['name'] }}" loading="lazy" decoding="async" />
    <p>© {{ date('Y') }} {{ $site['name'] }}. Web design studio, UK.</p>
    <p><a class="u-link" href="#main" data-top>Back to top</a></p>
  </div>
</footer>
