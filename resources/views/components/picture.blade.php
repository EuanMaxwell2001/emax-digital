@if ($src)
<picture>@foreach ($sources as $s)<source srcset="{{ $s['srcset'] }}" type="{{ $s['type'] }}" sizes="{{ $sizes }}">@endforeach<img src="{{ $fallback }}"@if ($fallbackSrcset) srcset="{{ $fallbackSrcset }}"@endif alt="{{ $alt }}" sizes="{{ $sizes }}" loading="{{ $loading }}"@if ($fetchpriority) fetchpriority="{{ $fetchpriority }}"@endif decoding="{{ $decoding }}"@if ($width) width="{{ $width }}" height="{{ $height }}"@endif></picture>
@endif
