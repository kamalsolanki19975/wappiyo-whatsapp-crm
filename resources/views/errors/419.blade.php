<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>419 — Page Expired | Wappiyo</title>
    <meta name="robots" content="noindex, follow">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#22C55E',
                        darkBrand: '#022828',
                    },
                    fontFamily: {
                        sans: ['Outfit', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="bg-slate-50 dark:bg-[#09090B] text-slate-900 dark:text-zinc-100 font-sans min-h-screen flex items-center justify-center p-6 antialiased selection:bg-emerald-500 selection:text-white relative overflow-hidden">
    <!-- Ambient Background Glows -->
    <div class="absolute -top-32 left-1/2 -translate-x-1/2 w-[600px] h-[350px] bg-gradient-to-tr from-[#22C55E]/15 via-[#10B981]/10 to-transparent blur-3xl pointer-events-none -z-10"></div>

    <div class="max-w-lg w-full text-center space-y-6">
        <!-- Official Wappiyo Logo -->
        <div class="flex justify-center mb-6">
            <a href="/" class="focus:outline-none">
                <img src="/images/logo.png" alt="Wappiyo" class="h-9 w-auto dark:hidden object-contain" />
                <img src="/images/logo-dark.png" alt="Wappiyo" class="h-9 w-auto hidden dark:block object-contain" />
            </a>
        </div>

        <!-- Brand Badge -->
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-amber-50 dark:bg-amber-950/50 border border-amber-200/60 dark:border-amber-800/60 text-amber-700 dark:text-amber-400 text-xs font-bold uppercase tracking-wider">
            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
            <span>419 Error</span>
        </div>

        <!-- Visual Hero -->
        <div class="relative flex items-center justify-center">
            <div class="text-[120px] sm:text-[160px] font-extrabold tracking-tighter text-slate-200 dark:text-zinc-800/80 select-none">
                419
            </div>
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="w-20 h-20 rounded-3xl bg-gradient-to-tr from-[#22C55E] to-[#022828] text-white flex items-center justify-center shadow-xl shadow-emerald-600/25">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
            </div>
        </div>

        <div class="space-y-2">
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                {{ __('Session Expired') }}
            </h1>
            <p class="text-sm text-slate-500 dark:text-zinc-400 max-w-sm mx-auto leading-relaxed">
                {{ __('Your security token has expired due to inactivity. Please refresh the page and try again.') }}
            </p>
        </div>

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
            <a href="javascript:window.location.reload()" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-[#22C55E] via-[#16A34A] to-[#022828] hover:from-[#15803D] hover:to-[#011d1d] shadow-md shadow-emerald-500/20 active:scale-[0.98] transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg>
                <span>{{ __('Refresh Page') }}</span>
            </a>
            <a href="/login" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl text-sm font-semibold text-slate-700 dark:text-zinc-300 bg-white dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 hover:bg-slate-50 dark:hover:bg-zinc-700/60 transition-all shadow-2xs">
                <span>{{ __('Sign In Again') }}</span>
            </a>
        </div>
    </div>
</body>
</html>
