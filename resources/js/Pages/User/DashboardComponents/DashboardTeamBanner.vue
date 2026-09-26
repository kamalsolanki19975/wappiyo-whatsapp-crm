<script setup>
import { Link } from '@inertiajs/vue3';
import Button from '@/Components/UI/Button.vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['dismiss']);
</script>

<template>
    <div
        v-if="show"
        class="relative overflow-hidden rounded-2xl border border-purple-200/80 dark:border-purple-900/50 bg-gradient-to-r from-purple-50/80 via-white to-indigo-50/50 dark:from-purple-950/30 dark:via-[#111113] dark:to-indigo-950/20 p-5 shadow-card transition-all"
    >
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-start gap-3.5">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-purple-100 dark:bg-purple-900/60 text-[#6C5CE7] dark:text-purple-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">
                        {{ $t('Expand Your Workspace Team') }}
                    </h3>
                    <p class="text-xs text-slate-600 dark:text-zinc-400 mt-0.5 max-w-xl">
                        {{ $t('Invite team members, assign WhatsApp customer inboxes, and collaborate with role-based access.') }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3 self-end sm:self-center shrink-0">
                <button
                    type="button"
                    class="text-xs font-medium text-slate-400 hover:text-slate-600 dark:hover:text-zinc-200 hover:underline transition-colors"
                    @click="emit('dismiss')"
                >
                    {{ $t('Dismiss') }}
                </button>

                <Link href="/team">
                    <Button variant="secondary" size="sm">
                        <template #icon>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        </template>
                        {{ $t('Invite Members') }}
                    </Button>
                </Link>
            </div>
        </div>
    </div>
</template>
