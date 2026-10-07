<x-layouts.app>
    @include('home.header')

    <main id="main">
        @include('home.hero')
        @include('home.statement')
        @include('home.services')
        @include('home.work')
        @include('home.process')
        @include('home.contact')
    </main>

    @include('home.footer')
</x-layouts.app>
