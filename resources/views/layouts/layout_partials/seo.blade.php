<meta name="description"
    content="SIMTARU (Sistem Informasi Tata Ruang), Karang Taruna merupakan wadah pembinaan dan pengembangan generasi muda di desa atau kelurahan yang tumbuh atas dasar kesadaran dan rasa tanggung jawab.">
<meta name="keywords"
    content="SIMTARU, Sistem Informasi Tata Ruang, Karang Taruna, generasi muda, desa, kelurahan, pembinaan, pengembangan, tata ruang">
<meta name="robots" content="index, follow">
<meta name="author" content="SIMTARU & Karang Taruna Team">

<!-- Open Graph meta tags -->
<meta property="og:title" content="{{ ucwords(str_replace('_', ' ', config('app.name'))) }}">
<meta property="og:description"
    content="SIMTARU (Sistem Informasi Tata Ruang) dan Karang Taruna: wadah pembinaan dan pengembangan generasi muda di desa atau kelurahan.">
<meta property="og:image" content="{{ asset('assets/img/og.png') }}">
<meta property="og:image:alt" content="Logo SIMTARU dan Karang Taruna">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:type" content="website">
<meta property="og:locale" content="{{ config('app.faker_locale') }}">
<meta property="og:locale:alternate" content="en_US">

<!-- Twitter Card meta tags -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ ucwords(str_replace('_', ' ', config('app.name'))) }}">
<meta name="twitter:description"
    content="SIMTARU (Sistem Informasi Tata Ruang) dan Karang Taruna: wadah pembinaan dan pengembangan generasi muda di desa atau kelurahan.">
<meta name="twitter:image" content="{{ asset('assets/img/og.png') }}">
<meta name="twitter:url" content="{{ url()->current() }}">

<!-- Canonical URL -->
<link rel="canonical" href="{{ url()->current() }}">
