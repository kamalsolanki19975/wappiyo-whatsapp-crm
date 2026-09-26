<script setup>
    import { ref } from 'vue';
    import { useForm } from "@inertiajs/vue3";
    import AlertModal from '@/Components/AlertModal.vue';
    import { useAlertModal } from '@/Composables/useAlertModal';
    import 'vue3-toastify/dist/index.css';

    const props = defineProps({
        rows: {
            type: Object,
            required: true,
        }
    });

    const { isOpenAlert, openAlert, confirmAlert } = useAlertModal();
    const form = useForm({'test': null});
    const copiedRef = ref(null);
    const unMaskedRef = ref(null);

    const deleteAction = (key) => {
        form.delete('/developer-tools/access-tokens/' + key);
    };

    const copyRow = async (token) => {
        copiedRef.value = token;

        try {
            if (navigator?.clipboard?.writeText) {
                await navigator.clipboard.writeText(token);
            } else {
                const tempInput = document.createElement("textarea");
                tempInput.value = token;
                document.body.appendChild(tempInput);
                tempInput.select();
                document.execCommand("copy");
                document.body.removeChild(tempInput);
            }
        } catch (_) {}

        setTimeout(() => {
            copiedRef.value = null;
        }, 2000);   
    };

    const maskToken = (token) => {
        if (!token) return '';
        if (unMaskedRef.value === token) {
            return token;
        }
        // Show first 4 characters and mask the rest
        if (token.length > 8) {
            return token.substring(0, 4) + '•'.repeat(Math.min(token.length - 8, 20)) + token.substring(token.length - 4);
        }
        return '•'.repeat(token.length);
    };

    const toggleMask = (token) => {
        unMaskedRef.value = unMaskedRef.value === token ? null : token;
    };
</script>

<template>
    <div class="space-y-4">
        <div class="overflow-x-auto rounded-xl border border-slate-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-2xs">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-slate-200/80 dark:border-zinc-800 bg-slate-50/75 dark:bg-zinc-800/40 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-zinc-400">
                        <th scope="col" class="py-3 pl-4 pr-3 sm:pl-6">{{ $t('Bearer Token') }}</th>
                        <th scope="col" class="hidden sm:table-cell px-3 py-3">{{ $t('Created') }}</th>
                        <th scope="col" class="py-3 pl-3 pr-4 sm:pr-6 text-right"><span class="sr-only">{{ $t('Actions') }}</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-zinc-800/60 font-mono text-xs">
                    <tr 
                        v-for="(item, index) in rows.data" 
                        :key="index"
                        class="hover:bg-slate-50/80 dark:hover:bg-zinc-800/30 transition-colors"
                    >
                        <!-- Masked Token & Actions -->
                        <td class="py-3.5 pl-4 pr-3 sm:pl-6">
                            <div class="flex items-center gap-3">
                                <div class="relative flex items-center gap-2 bg-slate-100 dark:bg-zinc-800 px-3 py-1.5 rounded-lg border border-slate-200/60 dark:border-zinc-700/60 max-w-md">
                                    <span class="text-slate-800 dark:text-zinc-200 truncate select-all">{{ maskToken(item.token) }}</span>
                                    
                                    <!-- Copied feedback pill -->
                                    <span 
                                        v-if="copiedRef === item.token" 
                                        class="absolute right-2 text-[10px] font-sans font-bold bg-emerald-500 text-white px-1.5 py-0.5 rounded shadow-sm animate-fade-in"
                                    >
                                        {{ $t('Copied!') }}
                                    </span>
                                </div>

                                <div class="flex items-center gap-1 font-sans">
                                    <!-- Reveal / Hide Toggle -->
                                    <button 
                                        type="button"
                                        @click="toggleMask(item.token)"
                                        class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-zinc-200 hover:bg-slate-100 dark:hover:bg-zinc-800 transition-colors"
                                        :title="unMaskedRef === item.token ? $t('Mask token') : $t('Reveal token')"
                                    >
                                        <svg v-if="unMaskedRef !== item.token" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path>
                                            <circle cx="12" cy="12" r="3"></circle>
                                        </svg>
                                        <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"></path>
                                            <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"></path>
                                            <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"></path>
                                            <line x1="2" y1="2" x2="22" y2="22"></line>
                                        </svg>
                                    </button>

                                    <!-- Copy Button -->
                                    <button 
                                        type="button"
                                        @click="copyRow(item.token)"
                                        class="p-1.5 rounded-lg text-slate-400 hover:text-[#6C5CE7] hover:bg-indigo-50 dark:hover:bg-indigo-950/40 transition-colors"
                                        :title="$t('Copy Token')"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                                            <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </td>

                        <!-- Created On -->
                        <td class="hidden sm:table-cell px-3 py-3.5 whitespace-nowrap font-sans text-xs text-slate-500 dark:text-zinc-400">
                            {{ item.created_at }}
                        </td>

                        <!-- Revoke Action -->
                        <td class="py-3.5 pl-3 pr-4 sm:pr-6 text-right whitespace-nowrap font-sans">
                            <button 
                                type="button"
                                @click="openAlert(item.uuid)"
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-colors"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 6h18"></path><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                                </svg>
                                <span>{{ $t('Revoke') }}</span>
                            </button>
                        </td>
                    </tr>

                    <!-- Empty state -->
                    <tr v-if="!rows.data || rows.data.length === 0">
                        <td colspan="3" class="py-12 text-center font-sans">
                            <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-zinc-800 text-slate-400 dark:text-zinc-500 mx-auto flex items-center justify-center mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="8" x2="12" y2="12"></line>
                                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                </svg>
                            </div>
                            <h4 class="text-xs font-semibold text-slate-800 dark:text-zinc-200">{{ $t('No API keys generated') }}</h4>
                            <p class="text-[11px] text-slate-400 dark:text-zinc-500 mt-0.5">{{ $t('Click "Generate API Key" above to create your first authentication token.') }}</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Revoke Alert Modal -->
        <AlertModal 
            v-model="isOpenAlert" 
            @confirm="() => confirmAlert(deleteAction)"
            :label="$t('Revoke API Token')" 
            :description="$t('Are you sure you want to revoke this access token? Any external integrations, cron jobs, or webhooks using this token will immediately stop working.')"
        />
    </div>
</template>