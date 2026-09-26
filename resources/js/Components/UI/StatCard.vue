<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import Card from './Card.vue';
import Badge from './Badge.vue';

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    value: {
        type: [String, Number],
        required: true,
    },
    change: {
        type: [String, Number],
        default: null,
    },
    changeType: {
        type: String,
        default: 'positive', // 'positive', 'negative', 'neutral'
    },
    changeText: {
        type: String,
        default: '',
    },
    iconBg: {
        type: String,
        default: 'bg-purple-50 dark:bg-purple-950/50 text-[#6C5CE7] dark:text-purple-400',
    },
    href: {
        type: String,
        default: null,
    },
    actionLabel: {
        type: String,
        default: '',
    },
    variant: {
        type: String,
        default: 'default', // 'default', 'interactive', 'ai', 'gradient'
    },
    className: {
        type: String,
        default: '',
    },
});

const changeBadgeVariant = computed(() => {
    switch (props.changeType) {
        case 'positive': return 'success';
        case 'negative': return 'danger';
        default: return 'default';
    }
});
</script>

<template>
    <Card
        :variant="href ? 'interactive' : variant"
        :className="className"
        class="group"
    >
        <div class="flex items-start justify-between">
            <div class="space-y-1">
                <span class="text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-zinc-400">
                    {{ title }}
                </span>
                <div class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 dark:text-white flex items-baseline gap-2">
                    {{ value }}
                </div>
            </div>

            <!-- Icon Container -->
            <div
                v-if="$slots.icon"
                :class="[
                    'p-2.5 sm:p-3 rounded-xl flex items-center justify-center transition-transform duration-200 group-hover:scale-105 shrink-0',
                    iconBg
                ]"
            >
                <slot name="icon" />
            </div>
        </div>

        <!-- Footer / Trend / Link -->
        <div v-if="change || changeText || href" class="mt-4 pt-3 border-t border-slate-100 dark:border-zinc-800/80 flex items-center justify-between text-xs">
            <div class="flex items-center gap-1.5 flex-wrap">
                <Badge
                    v-if="change"
                    :variant="changeBadgeVariant"
                    size="xs"
                >
                    <span v-if="changeType === 'positive'">↑</span>
                    <span v-else-if="changeType === 'negative'">↓</span>
                    {{ change }}
                </Badge>
                <span v-if="changeText" class="text-slate-500 dark:text-zinc-400">
                    {{ changeText }}
                </span>
            </div>

            <Link
                v-if="href"
                :href="href"
                class="text-[#6C5CE7] dark:text-purple-400 hover:text-[#5B46D6] dark:hover:text-purple-300 font-medium inline-flex items-center gap-1 group-hover:translate-x-0.5 transition-transform"
            >
                <span>{{ actionLabel || 'View details' }}</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
            </Link>
        </div>
    </Card>
</template>
