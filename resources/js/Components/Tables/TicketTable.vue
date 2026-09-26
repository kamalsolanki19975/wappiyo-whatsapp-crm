<template>
    <div class="rounded-3xl border border-slate-200/80 dark:border-white/10 bg-white dark:bg-slate-900 shadow-sm overflow-hidden">
        <!-- Desktop Table View -->
        <div class="overflow-x-auto">
            <table v-if="rows?.data?.length > 0" class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-white/5 bg-slate-50/60 dark:bg-white/[0.02] text-slate-400 font-bold uppercase tracking-wider text-[10px]">
                        <th class="py-3 px-4">{{ $t('Reference') }}</th>
                        <th class="py-3 px-4">{{ $t('Subject') }}</th>
                        <th class="py-3 px-4">{{ $t('Category') }}</th>
                        <th class="py-3 px-4">{{ $t('Priority') }}</th>
                        <th v-if="isAdmin" class="py-3 px-4">{{ $t('Customer') }}</th>
                        <th v-if="isAdmin" class="py-3 px-4">{{ $t('Assigned To') }}</th>
                        <th class="py-3 px-4">{{ $t('Status') }}</th>
                        <th class="py-3 px-4 text-right">{{ $t('Last Updated') }}</th>
                        <th class="py-3 px-4 text-right">{{ $t('Action') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                    <tr
                        v-for="item in rows.data"
                        :key="item.uuid || item.id"
                        class="hover:bg-slate-50/80 dark:hover:bg-white/5 transition group"
                    >
                        <!-- Reference Code -->
                        <td class="py-3.5 px-4">
                            <Link :href="ticketUrl(item.uuid)" class="inline-flex items-center gap-1.5 font-mono text-[11px] font-bold text-primary hover:underline">
                                <span>{{ item.reference }}</span>
                            </Link>
                        </td>

                        <!-- Subject -->
                        <td class="py-3.5 px-4 max-w-xs sm:max-w-sm">
                            <Link :href="ticketUrl(item.uuid)" class="block">
                                <div class="font-bold text-slate-900 dark:text-white truncate group-hover:text-primary transition-colors">
                                    {{ item.subject }}
                                </div>
                                <div v-if="item.message" class="text-[11px] text-slate-400 truncate mt-0.5 max-w-xs">
                                    {{ item.message }}
                                </div>
                            </Link>
                        </td>

                        <!-- Category -->
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <span class="px-2 py-0.5 rounded-lg text-[10px] font-semibold bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-slate-300">
                                {{ item.category?.name || 'General' }}
                            </span>
                        </td>

                        <!-- Priority -->
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <TicketPriorityBadge :priority="item.priority" />
                        </td>

                        <!-- Customer (Admin only) -->
                        <td v-if="isAdmin" class="py-3.5 px-4 whitespace-nowrap">
                            <div class="font-semibold text-slate-900 dark:text-white">
                                {{ item.user ? (item.user.first_name + ' ' + item.user.last_name) : 'User' }}
                            </div>
                            <div class="text-[10px] text-slate-400">
                                {{ item.user?.email }}
                            </div>
                        </td>

                        <!-- Assigned Agent (Admin only) -->
                        <td v-if="isAdmin" class="py-3.5 px-4 whitespace-nowrap">
                            <span v-if="item.agent" class="inline-flex items-center gap-1.5 text-xs text-slate-700 dark:text-slate-300 font-medium">
                                <span class="w-1.5 h-1.5 rounded-full bg-cyan-500"></span>
                                {{ item.agent.first_name + ' ' + item.agent.last_name }}
                            </span>
                            <span v-else class="text-slate-400 italic text-[11px]">
                                {{ $t('Unassigned') }}
                            </span>
                        </td>

                        <!-- Status -->
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <TicketStatusBadge :status="item.status" />
                        </td>

                        <!-- Updated Date -->
                        <td class="py-3.5 px-4 text-right text-slate-500 dark:text-slate-400 whitespace-nowrap text-[11px]">
                            {{ item.updated_at }}
                        </td>

                        <!-- Action -->
                        <td class="py-3.5 px-4 text-right">
                            <Link
                                :href="ticketUrl(item.uuid)"
                                class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-slate-400 hover:text-primary hover:bg-primary/10 transition"
                                :title="$t('Open Ticket Details')"
                            >
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </Link>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Empty State -->
            <div
                v-else
                class="py-16 flex flex-col items-center justify-center text-center p-6"
            >
                <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-white/5 text-slate-400 flex items-center justify-center mb-3">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h4 class="text-sm font-bold text-slate-900 dark:text-white mb-1">
                    {{ $t('No support tickets found') }}
                </h4>
                <p class="text-xs text-slate-400 max-w-sm mb-4">
                    {{ $t('You have no open tickets in this category. Need help? Create a new support ticket anytime.') }}
                </p>
                <Link
                    href="/support/create"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-primary text-white text-xs font-semibold shadow-sm hover:bg-primary/90 transition"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>{{ $t('New Support Ticket') }}</span>
                </Link>
            </div>
        </div>

        <!-- Pagination Controls -->
        <div
            v-if="rows?.links?.length > 3"
            class="px-4 py-3 border-t border-slate-100 dark:border-white/5 flex items-center justify-between gap-4 text-xs"
        >
            <div class="text-slate-500 dark:text-slate-400">
                {{ $t('Showing') }}
                <span class="font-bold text-slate-900 dark:text-white">{{ rows.meta?.from || 1 }}</span>
                {{ $t('to') }}
                <span class="font-bold text-slate-900 dark:text-white">{{ rows.meta?.to || rows.data.length }}</span>
                {{ $t('of') }}
                <span class="font-bold text-slate-900 dark:text-white">{{ rows.meta?.total || rows.data.length }}</span>
                {{ $t('tickets') }}
            </div>

            <div class="flex items-center gap-1">
                <template v-for="(link, i) in rows.links" :key="i">
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        v-html="link.label"
                        class="px-2.5 py-1 rounded-lg text-xs font-semibold transition"
                        :class="link.active ? 'bg-primary text-white' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-white/5'"
                    />
                    <span
                        v-else
                        v-html="link.label"
                        class="px-2.5 py-1 text-slate-300 dark:text-slate-600 cursor-not-allowed text-xs"
                    />
                </template>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import TicketStatusBadge from '@/Components/Support/TicketStatusBadge.vue';
import TicketPriorityBadge from '@/Components/Support/TicketPriorityBadge.vue';

const props = defineProps({
    rows: {
        type: Object,
        required: true,
    },
});

const user = computed(() => usePage().props.auth?.user);
const isAdmin = computed(() => user.value?.role !== 'user');

const ticketUrl = (uuid) => {
    return isAdmin.value ? '/admin/support/' + uuid : '/support/' + uuid;
};
</script>