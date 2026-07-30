<!-- ====== HEAD (paste di layout Blade) ====== -->
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=edge">

@php
    // === Domain canonical production ===
    $domainBase = 'https://prism.sda.id';

    // === Deteksi halaman yang wajib NOINDEX ===
    $mustNoIndex =
        request()->routeIs('pricelists.*') ||
        request()->routeIs('login') ||
        request()->routeIs('register') ||
        request()->routeIs('password.*') ||
        request()->routeIs('admin.*') ||
        request()->is('pricelists*') ||
        request()->is('login') ||
        request()->is('register') ||
        request()->is('password/*') ||
        request()->is('admin/*');

    // Bisa dioverride dari view dengan @section('robots', '...')
    $robotsFromView = trim($__env->yieldContent('robots'));
    $robots =
        $robotsFromView !== '' ? $robotsFromView : ($mustNoIndex ? 'noindex, nofollow, noarchive' : 'index, follow');

    $title = trim($__env->yieldContent('title', 'PRISM SDA'));
    $metaDescription = trim(
        $__env->yieldContent(
            'meta_description',
            'Platform internal PRISM SDA untuk pengelolaan price list dan data terkait.',
        ),
    );
    $canonical = $domainBase . request()->getPathInfo();
@endphp

<title>{{ $title }}</title>
<meta name="description" content="{{ $metaDescription }}">
<meta name="robots" content="{{ $robots }}">
<meta name="googlebot" content="{{ $robots }}">
<meta name="bingbot" content="{{ $robots }}">
<link rel="canonical" href="{{ $canonical }}" />

<!-- Open Graph -->
<meta property="og:type" content="website" />
<meta property="og:title" content="{{ trim($__env->yieldContent('og_title', $title)) }}" />
<meta property="og:description" content="{{ trim($__env->yieldContent('og_description', $metaDescription)) }}" />
<meta property="og:url" content="{{ $canonical }}" />
<meta property="og:image" content="https://sda.co.id/assets/img/icon-sda.png" />

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:title" content="{{ trim($__env->yieldContent('twitter_title', $title)) }}" />
<meta name="twitter:description" content="{{ trim($__env->yieldContent('twitter_description', $metaDescription)) }}" />
<meta name="twitter:image" content="https://sda.co.id/assets/img/icon-sda.png" />

<!-- Icons (pakai aset sda.co.id) -->
<link rel="icon" href="https://sda.co.id/assets/img/logo/favicon.png" type="image/png">
<link rel="shortcut icon" href="https://sda.co.id/assets/img/logo/favicon.png" type="image/png">
<link rel="apple-touch-icon" href="https://sda.co.id/assets/img/icon-sda.png">

<!-- PWA -->
<link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
<meta name="theme-color" content="#ffffff" media="(prefers-color-scheme: light)">
<meta name="theme-color" content="#0b1220" media="(prefers-color-scheme: dark)">
<meta name="color-scheme" content="light dark">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-title" content="PRISM SDA">
<meta name="apple-mobile-web-app-status-bar-style" content="default">

<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">

<!-- Core CSS (punyamu) -->
<script src="{{ asset('assets/js/color-modes.js') }}"></script>
<link rel="stylesheet" href="{{ asset('assets/css/core.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/iconfont.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/mobile-styles.css') }}?v={{ filemtime(public_path('assets/css/mobile-styles.css')) }}">
<link rel="stylesheet" href="{{ asset('assets/css/flatpickr.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/dataTables.bootstrap5.css') }}">
{{-- <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet" /> --}}
{{-- <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet" /> --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

<!-- Service Worker register -->
{{-- <script>
    if ('serviceWorker' in navigator) {
        addEventListener('load', () => {
            navigator.serviceWorker.register('{{ asset('sw.js') }}', {
                    scope: '/'
                })
                .catch(err => console.log('SW register failed:', err));
        });
    }
</script> --}}

@stack('head')
