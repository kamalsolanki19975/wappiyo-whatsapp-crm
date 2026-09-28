<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        @if(app()->environment('production'))
            <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
        @endif
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @php
            $config = collect($page['props']['config'] ?? []);
            $google_analytics = $config->firstWhere('key', 'google_analytics_tracking_id')['value'] ?? null;
            $customFavicon = $config->firstWhere('key', 'favicon')['value'] ?? null;
            if ($customFavicon) {
                $faviconUrl = (str_starts_with($customFavicon, '/') || str_starts_with($customFavicon, 'http'))
                    ? $customFavicon
                    : '/media/' . $customFavicon;
            } else {
                $faviconUrl = '/favicon.ico';
            }
        @endphp
        <!-- Favicon & Brand Icons -->
        <link rel="icon" type="image/x-icon" href="{{ url($faviconUrl) }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
        <link rel="manifest" href="{{ asset('site.webmanifest') }}">
        <meta name="theme-color" content="#22C55E">
        <meta name="application-name" content="Wappiyo">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="apple-mobile-web-app-title" content="Wappiyo">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="format-detection" content="telephone=no">
        <meta name="msapplication-TileColor" content="#09090B">
        <link rel="mask-icon" href="{{ asset('images/logo-mark.png') }}" color="#22C55E">
        <meta property="og:site_name" content="Wappiyo">
        <meta property="og:image" content="{{ asset('images/og-image.png') }}">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:image" content="{{ asset('images/og-image.png') }}">

        <!-- Google Fonts: Outfit & Plus Jakarta Sans -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

        <!-- Theme Initialization to prevent flash -->
        <script>
            try {
                const storedTheme = localStorage.getItem('wappiyo_theme');
                if (storedTheme === 'dark' || (!storedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            } catch (_) {}
        </script>

        @vite(['resources/js/app.js', 'resources/css/app.css'])
        @inertiaHead
        @if (!empty($google_analytics))
        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $google_analytics }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{{ $google_analytics }}');
        </script>
        @endif
    </head>
    <body class="h-full font-sans antialiased text-slate-900 dark:text-zinc-100 bg-slate-50/70 dark:bg-[#09090B] transition-colors duration-200">
        @inertia
    </body>
</html>