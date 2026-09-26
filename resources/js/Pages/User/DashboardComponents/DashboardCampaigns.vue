<script setup>
import { Link } from '@inertiajs/vue3';
import EmptyState from '@/Components/UI/EmptyState.vue';
import Badge from '@/Components/UI/Badge.vue';
import Button from '@/Components/UI/Button.vue';

const props = defineProps({
    campaigns: {
        type: Array,
        default: () => [],
    },
});

const getStatusVariant = (status) => {
    switch (status?.toLowerCase()) {
        case 'scheduled':
            return 'info';
        case 'pending':
            return 'warning';
        case 'processing':
        case 'active':
            return 'primary';
        case 'completed':
            return 'success';
        default:
            return 'neutral';
    }
};
</script>

<template>
    <div class="flex flex-col h-full rounded-2xl border border-slate-200/80 dark:border-zinc-800 bg-white dark:bg-[#111113] p-5 sm:p-6 shadow-card transition-all">
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-zinc-800/80 pb-4">
            <div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white">
                    {{ $t('Upcoming Campaigns') }}
                </h3>
                <p class="text-xs text-slate-500 dark:text-zinc-400">
                    {{ $t('Scheduled or pending broadcast dispatches') }}
                </p>
            </div>

            <Link href="/campaigns/create">
                <Button variant="ghost" size="xs">
                    <template #icon>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"/>
                            <line x1="5" y1="12" x2="19" y2="12"/>
                        </svg>
                    </template>
                    {{ $t('New') }}
                </Button>
            </Link>
        </div>

        <!-- Body: Campaigns list or Empty state -->
        <div class="mt-4 flex-1">
            <div v-if="campaigns.length === 0" class="py-6">
                <EmptyState
                    title="No Scheduled Campaigns"
                    description="You do not have any pending or scheduled broadcasts right now. Prepare a campaign to reach your contact segments."
                    action-text="Create Campaign"
                    action-route="/campaigns/create"
                >
                    <template #icon>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-slate-400 dark:text-zinc-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m3 11 19-9-9 19-2-8-8-2z"/>
                        </svg>
                    </template>
                </EmptyState>
            </div>

            <div v-else class="space-y-2.5">
                <div
                    v-for="(item, index) in campaigns"
                    :key="index"
                    class="group flex items-center justify-between p-3 rounded-xl border border-slate-100 dark:border-zinc-800 bg-slate-50/60 dark:bg-zinc-900/40 hover:bg-slate-100/80 dark:hover:bg-zinc-800/60 transition-colors"
                >
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-pink-50 dark:bg-pink-950/40 text-[#EC4899]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="m3 11 19-9-9 19-2-8-8-2z"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <h4 class="text-sm font-semibold text-slate-800 dark:text-zinc-200 truncate capitalize">
                                {{ item.name }}
                            </h4>
                            <p class="text-[11px] text-slate-400 dark:text-zinc-500">
                                {{ item.created_at ? new Date(item.created_at).toLocaleDateString() : 'Active queue' }}
                            </p>
                        </div>
                    </div>

                    <div class="shrink-0">
                        <Badge :variant="getStatusVariant(item.status)" size="sm">
                            {{ item.status }}
                        </Badge>
                    </div>
                </div>

                <div class="pt-2">
                    <Link
                        href="/campaigns"
                        class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#6C5CE7] dark:text-purple-400 hover:underline"
                    >
                        <span>{{ $t('View all campaigns') }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
