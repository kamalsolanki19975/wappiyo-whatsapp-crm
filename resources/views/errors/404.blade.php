<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 — Page Not Found | Wappiyo</title>
    <meta name="robots" content="noindex, follow">
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
                        primary: '#6C5CE7',
                        violet: '#8B5CF6',
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
<body class="bg-slate-50 dark:bg-[#09090B] text-slate-900 dark:text-zinc-100 font-sans min-h-screen flex items-center justify-center p-6 antialiased selection:bg-[#6C5CE7] selection:text-white">
    <div class="max-w-lg w-full text-center space-y-6">
        <!-- Brand Badge -->
        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-indigo-50 dark:bg-indigo-950/50 border border-indigo-200/60 dark:border-indigo-900/50 text-[#6C5CE7] dark:text-indigo-400 text-xs font-bold uppercase tracking-wider">
            <span class="w-2 h-2 rounded-full bg-[#6C5CE7] animate-pulse"></span>
            <span>404 Error</span>
        </div>

        <!-- Visual Hero -->
        <div class="relative flex items-center justify-center">
            <div class="text-[120px] sm:text-[160px] font-extrabold tracking-tighter text-slate-200 dark:text-zinc-800/80 select-none">
                404
            </div>
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="w-20 h-20 rounded-3xl bg-gradient-to-tr from-[#6C5CE7] to-[#8B5CF6] text-white flex items-center justify-center shadow-xl shadow-indigo-500/25">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                </div>
            </div>
        </div>

        <div class="space-y-2">
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                {{ __('Page not found') }}
            </h1>
            <p class="text-sm text-slate-500 dark:text-zinc-400 max-w-sm mx-auto leading-relaxed">
                {{ __('The link you followed may be broken, or the page may have been moved or removed from Wappiyo.') }}
            </p>
        </div>

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
            <a href="/dashboard" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-[#6C5CE7] to-[#8B5CF6] hover:from-[#5b4bc4] hover:to-[#7c4deb] shadow-md shadow-indigo-500/20 active:scale-[0.98] transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                <span>{{ __('Return to Dashboard') }}</span>
            </a>
            <a href="javascript:history.back()" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl text-sm font-semibold text-slate-700 dark:text-zinc-300 bg-white dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 hover:bg-slate-50 dark:hover:bg-zinc-700/60 transition-all shadow-2xs">
                <span>{{ __('Go Back') }}</span>
            </a>
        </div>
    </div>
</body>
</html>