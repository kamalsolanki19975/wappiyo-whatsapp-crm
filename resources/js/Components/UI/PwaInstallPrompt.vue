<template>
    <transition
        enter-active-class="transform ease-out duration-300 transition"
        enter-from-class="translate-y-8 opacity-0 scale-95"
        enter-to-class="translate-y-0 opacity-100 scale-100"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="translate-y-0 opacity-100 scale-100"
        leave-to-class="translate-y-8 opacity-0 scale-95"
    >
        <div
            v-if="showBanner"
            class="fixed bottom-20 md:bottom-6 right-4 left-4 md:left-auto md:max-w-md z-40 bg-white/95 dark:bg-[#111113]/95 backdrop-blur-xl border border-slate-200/80 dark:border-zinc-800 rounded-2xl p-4 shadow-2xl shadow-emerald-950/10 text-slate-900 dark:text-zinc-100 transition-all duration-200"
            role="dialog"
            aria-labelledby="pwa-install-title"
        >
            <div class="flex items-start gap-3.5">
                <img
                    :src="BRAND.mark"
                    alt="Wappiyo"
                    class="w-11 h-11 rounded-xl shadow-md shadow-emerald-500/10 shrink-0"
                />

                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-2">
                        <h4 id="pwa-install-title" class="font-bold text-sm text-slate-900 dark:text-white">
                            Install Wappiyo
                        </h4>
                        <button
                            type="button"
                            class="text-slate-400 hover:text-slate-600 dark:hover:text-zinc-300 p-1 -mr-1 rounded-lg"
                            @click="dismiss"
                            aria-label="Close"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </button>
                    </div>

                    <p class="text-xs text-slate-500 dark:text-zinc-400 mt-0.5 leading-relaxed">
                        Get faster access to your conversations, notifications, and broadcast campaigns.
                    </p>

                    <!-- Android / Chrome / Desktop Installation -->
                    <div v-if="canInstall" class="flex items-center gap-2 mt-3">
                        <button
                            type="button"
                            class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold shadow-sm shadow-emerald-600/30 transition-colors flex items-center gap-1.5"
                            @click="install"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            <span>Install Wappiyo</span>
                        </button>
                        <button
                            type="button"
                            class="px-3 py-1.5 text-xs font-medium text-slate-600 dark:text-zinc-400 hover:text-slate-800 dark:hover:text-zinc-200 transition-colors"
                            @click="dismiss"
                        >
                            Maybe Later
                        </button>
                    </div>

                    <!-- iOS Safari Instructions -->
                    <div v-else-if="isIos && !isStandalone" class="mt-3 p-2.5 rounded-xl bg-slate-50 dark:bg-zinc-900 border border-slate-100 dark:border-zinc-800/80 text-[11px] text-slate-600 dark:text-zinc-400 flex items-center gap-2">
                        <span class="p-1 rounded-lg bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/><polyline points="16 6 12 2 8 6"/><line x1="12" y1="2" x2="12" y2="15"/></svg>
                        </span>
                        <span>Tap <strong>Share</strong> in Safari, then select <strong>Add to Home Screen</strong>.</span>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<script setup>
import { computed } from 'vue';
import { usePwa } from '@/Composables/usePwa';
import { BRAND } from '@/Config/brand';

const {
    canInstall,
    isIos,
    isStandalone,
    isInstallBannerDismissed,
    promptInstall,
    dismissInstallPrompt
} = usePwa();

const showBanner = computed(() => {
    if (isStandalone.value) return false;
    if (isInstallBannerDismissed.value) return false;
    return canInstall.value || isIos.value;
});

const install = async () => {
    await promptInstall();
};

const dismiss = () => {
    dismissInstallPrompt();
};
</script>
