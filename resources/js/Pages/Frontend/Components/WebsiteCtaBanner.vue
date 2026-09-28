<template>
    <section class="py-20 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
        <div class="max-w-7xl mx-auto">
            <div class="relative rounded-3xl overflow-hidden bg-gradient-to-tr from-[#022828] via-[#064E3B] to-[#22C55E] p-8 sm:p-12 lg:p-16 text-white shadow-2xl shadow-emerald-950/30">
                <!-- Ambient circles -->
                <div class="absolute -top-24 -right-24 w-96 h-96 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
                <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-emerald-950/30 rounded-full blur-2xl pointer-events-none"></div>

                <div class="relative z-10 max-w-2xl">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/20 backdrop-blur-md text-xs font-semibold uppercase tracking-wider mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                        <span>{{ subtitle || $t('Start your free trial today') }}</span>
                    </span>

                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight mb-4 leading-tight">
                        {{ title || $t('Transform every WhatsApp conversation into revenue') }}
                    </h2>

                    <p class="text-white/90 text-sm sm:text-base mb-8 max-w-xl leading-relaxed">
                        {{ description || $t('Join forward-thinking companies automating support, sales, and broadcasts through Wappiyo. No complex setup, live in minutes.') }}
                    </p>

                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                        <Link
                            href="/signup"
                            class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-white text-slate-900 hover:bg-slate-100 font-bold text-sm shadow-lg shadow-black/10 transition-all hover:scale-[1.02] cursor-pointer"
                        >
                            <span>{{ $t('Get started for free') }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                        </Link>
                        <Link
                            href="/pricing"
                            class="inline-flex items-center justify-center px-6 py-3.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 backdrop-blur-md text-white font-semibold text-sm transition-all cursor-pointer"
                        >
                            {{ $t('Explore pricing plans') }}
                        </Link>
                    </div>

                    <div class="mt-8 flex flex-wrap items-center gap-6 text-xs text-white/80">
                        <span class="flex items-center gap-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            <span>{{ $t('14-day full feature trial') }}</span>
                        </span>
                        <span class="flex items-center gap-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            <span>{{ $t('No credit card required') }}</span>
                        </span>
                        <span class="flex items-center gap-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            <span>{{ $t('Cancel anytime') }}</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    title: String,
    subtitle: String,
    description: String,
});
</script>
