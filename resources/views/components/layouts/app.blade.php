@props(['title' => null, 'description' => config('site.description')])

@php($pageTitle = $title ? "{$title} — " . config('app.name') : config('app.name') . ' — Web design, branding & hosting')

<!DOCTYPE html>
<html lang="en-GB">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script>document.documentElement.classList.add('js')</script>
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $description }}">
    <meta name="theme-color" content="#12181c">
    <link rel="canonical" href="{{ url()->current() }}">

    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ config('app.name') }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:locale" content="en_GB">

    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo:ital,wdth,wght@0,100,400;0,100,500;0,125,500;0,125,700;0,125,800;0,125,900;1,125,900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-50 focus:bg-ion focus:px-4 focus:py-2 focus:text-ink">Skip to content</a>

    <div class="overflow-x-clip">
        {{ $slot }}
    </div>
</body>
</html>
