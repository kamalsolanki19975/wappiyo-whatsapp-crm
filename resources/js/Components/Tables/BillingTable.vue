<script setup>
    import { ref } from 'vue';
    import debounce from 'lodash/debounce';
    import { router } from '@inertiajs/vue3';
    import 'vue3-toastify/dist/index.css';

    const props = defineProps({
        rows: {
            type: Object,
            required: true,
        },
        filters: {
            type: Object,
            default: () => ({})
        },
        uuid: {
            type: String,
        }
    });
    
    const params = ref({
        search: props.filters?.search || null,
    });

    const isSearching = ref(false);

    const clearSearch = () => {
        params.value.search = null;
        runSearch();
    };

    const search = debounce(() => {
        isSearching.value = true;
        runSearch();
    }, 500);

    const runSearch = () => {
        router.visit(window.location.pathname, {
            method: 'get',
            data: params.value,
            preserveState: true,
            preserveScroll: true,
            onFinish: () => {
                isSearching.value = false;
            }
        });
    };
</script>

<template>
    <div class="space-y-4">
        <!-- Search Bar -->
        <div class="flex items-center justify-between">
            <div class="relative w-full max-w-xs">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 dark:text-zinc-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </div>
                <input 
                    @input="search" 
                    v-model="params.search" 
                    type="text" 
                    class="block w-full pl-9 pr-8 py-2 text-xs rounded-xl bg-slate-50 dark:bg-zinc-800/80 border border-slate-200 dark:border-zinc-700/80 text-slate-900 dark:text-zinc-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#6C5CE7]/50" 
                    :placeholder="$t('Search invoices...')"
                />
                <button 
                    v-if="params.search" 
                    @click="clearSearch" 
                    type="button" 
                    class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-zinc-300"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <span v-if="isSearching" class="text-xs text-slate-400 flex items-center gap-1.5">
                <svg class="animate-spin w-3 h-3 text-[#6C5CE7]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>{{ $t('Filtering...') }}</span>
            </span>
        </div>

        <!-- Invoices Table -->
        <div class="overflow-x-auto rounded-xl border border-slate-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-2xs">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-slate-200/80 dark:border-zinc-800 bg-slate-50/75 dark:bg-zinc-800/40 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-zinc-400">
                        <th scope="col" class="py-3 pl-4 pr-3 sm:pl-6">{{ $t('Date') }}</th>
                        <th scope="col" class="hidden sm:table-cell px-3 py-3">{{ $t('Organization') }}</th>
                        <th scope="col" class="px-3 py-3">{{ $t('Description') }}</th>
                        <th scope="col" class="py-3 pl-3 pr-4 sm:pr-6 text-right">{{ $t('Amount') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-zinc-800/60">
                    <tr 
                        v-for="(item, index) in rows.data" 
                        :key="index"
                        class="hover:bg-slate-50/80 dark:hover:bg-zinc-800/30 transition-colors"
                    >
                        <!-- Date -->
                        <td class="py-3.5 pl-4 pr-3 sm:pl-6 whitespace-nowrap">
                            <span class="text-xs font-medium text-slate-800 dark:text-zinc-200">{{ item.created_at }}</span>
                        </td>

                        <!-- Organization -->
                        <td class="hidden sm:table-cell px-3 py-3.5 whitespace-nowrap">
                            <span class="text-xs text-slate-600 dark:text-zinc-400">{{ item.organization?.name || '—' }}</span>
                        </td>

                        <!-- Description -->
                        <td class="px-3 py-3.5">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                                <span class="text-xs font-medium text-slate-800 dark:text-zinc-200">{{ item.description }}</span>
                            </div>
                        </td>

                        <!-- Amount -->
                        <td class="py-3.5 pl-3 pr-4 sm:pr-6 text-right whitespace-nowrap">
                            <span class="text-xs font-bold text-slate-900 dark:text-white">{{ item.amount }}</span>
                        </td>
                    </tr>

                    <!-- Empty state -->
                    <tr v-if="!rows.data || rows.data.length === 0">
                        <td colspan="4" class="py-12 text-center">
                            <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-zinc-800 text-slate-400 dark:text-zinc-500 mx-auto flex items-center justify-center mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <rect x="2" y="4" width="20" height="16" rx="2"></rect>
                                    <line x1="2" y1="10" x2="22" y2="10"></line>
                                </svg>
                            </div>
                            <h4 class="text-xs font-semibold text-slate-800 dark:text-zinc-200">{{ $t('No invoices found') }}</h4>
                            <p class="text-[11px] text-slate-400 dark:text-zinc-500 mt-0.5">{{ $t('Transactions and payment receipts will be displayed here.') }}</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>