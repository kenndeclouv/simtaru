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
    <title>@yield('title') | {{ ucwords(str_replace('_', ' ', config('app.name'))) }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('averroes.svg') }}">

    <!-- SEO -->
    @include('layouts.layout_partials.seo')

    <!-- Styles -->
    @include('layouts.layout_partials.styles')

    <!-- Helpers -->
    @include('layouts.layout_partials.helpers')
</head>


<body style="overflow-x: hidden">
    <div class="loader">
        <div class="sk-fold sk-primary d-none d-lg-block m-2" style="width: 80px; height:80px">
            <div class="sk-fold-cube"></div>
            <div class="sk-fold-cube"></div>
            <div class="sk-fold-cube"></div>
            <div class="sk-fold-cube"></div>
        </div>
    </div>
    @include('components.alert')
    <div class="layout-wrapper layout-content-navbar  layout-without-menu">
        <div class="layout-page">
            <!-- Navbar -->
            @include('components.nav')

            <!-- Content -->
            <div class="content-wrapper">
                @yield('content')

                <!-- Footer -->
                @include('components.footer')

                <!-- Content Backdrop -->
                <div class="content-backdrop fade"></div>
            </div>
        </div>


        <div class="layout-overlay layout-menu-toggle"></div>
        <div class="drag-target"></div>
    </div>

    <!-- Scripts -->
    @include('layouts.layout_partials.scripts')
</body>

</html>
