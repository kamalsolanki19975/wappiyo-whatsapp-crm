<template>
    <AppLayout>
        <div class="h-full flex flex-col md:flex-row overflow-hidden bg-slate-50/70 dark:bg-[#09090B]">
            <!-- Left Panel: API Keys Management -->
            <div class="flex-1 overflow-y-auto p-6 md:p-8 space-y-6">
                <Menu v-if="webhookModule" />

                <!-- Header -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-200/80 dark:border-zinc-800 pb-5">
                    <div>
                        <h2 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white flex items-center gap-2">
                            <span>{{ $t('API Keys & Developer Access') }}</span>
                        </h2>
                        <p class="text-sm text-slate-500 dark:text-zinc-400 mt-1">
                            {{ $t('Generate secure bearer tokens to integrate Wappiyo into external CRMs, bots, and services.') }}
                        </p>
                    </div>

                    <div>
                        <button 
                            @click="generateToken()" 
                            type="button" 
                            :disabled="loadIcon"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-gradient-to-r from-[#6C5CE7] to-[#8B5CF6] hover:from-[#5b4bc4] hover:to-[#7c4deb] shadow-sm shadow-indigo-500/20 active:scale-[0.98] transition-all disabled:opacity-50"
                        >
                            <svg v-if="!loadIcon" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="16"></line>
                                <line x1="8" y1="12" x2="16" y2="12"></line>
                            </svg>
                            <svg v-else class="animate-spin w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>{{ $t('Generate API Key') }}</span>
                        </button>
                    </div>
                </div>

                <!-- Token Table Card -->
                <div class="bg-white dark:bg-zinc-900 border border-slate-200/80 dark:border-zinc-800 rounded-2xl p-6 shadow-sm space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-semibold text-slate-900 dark:text-white">{{ $t('Active Access Tokens') }}</h3>
                            <p class="text-xs text-slate-500 dark:text-zinc-400 mt-0.5">
                                {{ $t('Tokens are masked for security. Click the reveal icon to inspect or copy to clipboard.') }}
                            </p>
                        </div>
                    </div>

                    <TokenTable :rows="props.rows" />
                </div>
            </div>

            <!-- Right Panel: Interactive API Documentation -->
            <div class="hidden lg:block w-[42%] border-l border-slate-200/80 dark:border-zinc-800 bg-[#111113] overflow-y-auto h-screen">
                <Documentation :apirequests="apirequests"/>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from "./../Layout/App.vue";
import Documentation from "./Documentation.vue";
import Menu from "./Menu.vue";
import { useForm } from "@inertiajs/vue3";
import TokenTable from '@/Components/Tables/TokenTable.vue';
import { ref } from 'vue';

const props = defineProps(['rows', 'url', 'apirequests', 'webhookModule']);
const loadIcon = ref(false);

const form = useForm({
    'name': null,
});

const generateToken = () => {
    loadIcon.value = true;

    form.post('/developer-tools/access-tokens', {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onFinish: () => {
            loadIcon.value = false;
        }
    });
};
</script>