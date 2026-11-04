<x-layouts.base title="Page not found" description="This page does not exist on the EMAX Digital website. Head back to the homepage to see the work, the services and how to start a project.">
  <x-cut-stage
    id="nf-title"
    :lines="['This page', 'didn’t make', 'the cut.']"
    meta="Error 404"
    sub="The link may be old, or the page has moved. Everything that did make the cut is on the homepage."
    class="nf"
  >
    <div class="hero__ctas">
      <a class="btn btn--mint" href="/" data-magnetic="0.25"><span class="btn__label">Back to the homepage</span></a>
    </div>
  </x-cut-stage>
</x-layouts.base>
