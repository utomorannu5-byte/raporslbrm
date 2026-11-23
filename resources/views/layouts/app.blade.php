<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'Dashboard' }} &middot; {{ config('app.name') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('head')
    </head>
    <body class="app-body">
        <div class="app-shell">
            <aside class="app-sidebar-desktop d-none d-lg-block">
                <div class="sidebar-inner">
                    @include('layouts.partials.sidebar-brand')
                    <div class="sidebar-scroll">
                        @include('layouts.partials.sidebar-nav')
                    </div>
                    @include('layouts.partials.sidebar-footer')
                </div>
            </aside>

            <div class="offcanvas offcanvas-start erapor-offcanvas" tabindex="-1" id="sidebarOffcanvas" aria-labelledby="sidebarOffcanvasLabel">
                <div class="sidebar-inner">
                    @include('layouts.partials.sidebar-brand', ['offcanvas' => true])
                    <div class="sidebar-scroll">
                        @include('layouts.partials.sidebar-nav')
                    </div>
                    @include('layouts.partials.sidebar-footer')
                </div>
            </div>

            <div class="app-main">
                @include('layouts.partials.topbar')

                <main class="app-content">
                    {{ $slot }}
                </main>

                <footer class="app-footer">
                    &copy; {{ now()->year }} {{ config('app.name') }} &middot; Sistem Informasi Hasil Belajar
                </footer>
            </div>
        </div>

        @stack('scripts')
    </body>
</html>
