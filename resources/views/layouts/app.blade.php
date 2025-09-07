<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="layout-navbar-fixed layout-menu-fixed" dir="ltr"
    data-theme="{{ config('app.theme') }}" data-assets-path="{{ config('app.assets_path') }}"
    data-template="{{ config('app.template') }}" data-style="{{ config('app.style') }}"
    data-primary-color="{{ config('app.primary_color') }}" data-themes="{{ config('app.themes') }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- <meta name="pusher-app-key" content="{{ config('broadcasting.connections.pusher.key') }}"> --}}
    <title>@yield('title') | {{ ucwords(str_replace('_', ' ', config('app.name'))) }}</title>
    {{-- <link rel="icon" type="image/x-icon" href="{{ asset('averroes.svg') }}"> --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <!-- SEO -->
    @include('layouts.layout_partials.seo')

    <!-- Styles -->
    @include('layouts.layout_partials.styles')

    <!-- Helpers -->
    @include('layouts.layout_partials.helpers')
</head>


<body>
    @include('components.alert')
    {{-- <div class="loader">
        <div class="sk-fold sk-primary d-none d-lg-block m-2" style="width: 80px; height:80px">
            <div class="sk-fold-cube"></div>
            <div class="sk-fold-cube"></div>
            <div class="sk-fold-cube"></div>
            <div class="sk-fold-cube"></div>
        </div>
    </div> --}}
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- sidebar -->
            @include('components.sidebar')

            <div class="layout-page">
                <!-- Navbar -->
                @include('components.nav')

                <!-- Content -->
                <div class="content-wrapper">
                    @yield('content')

                    <!-- Footer -->
                    {{-- @include('components.footer') --}}

                    <!-- Content Backdrop -->
                    <div class="content-backdrop fade"></div>
                </div>
            </div>
        </div>

        <div class="layout-overlay layout-menu-toggle"></div>
        <div class="drag-target"></div>
    </div>

    <!-- Scripts -->
    @include('layouts.layout_partials.scripts')
</body>

</html>
