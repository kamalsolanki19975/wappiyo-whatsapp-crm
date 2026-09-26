<template>
    <div class="rounded-3xl border border-slate-200/80 dark:border-white/10 bg-white dark:bg-slate-900 p-6 shadow-sm flex flex-col justify-between">
        <!-- Header -->
        <div class="border-b border-slate-100 dark:border-white/5 pb-4">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">
                    {{ $t('Delivery & Read Rates') }}
                </h3>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                    {{ $t('WhatsApp SLA') }}
                </span>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                {{ $t('Calculated based on actual outbound message delivery events') }}
            </p>
        </div>

        <!-- Big Rate Numbers -->
        <div class="grid grid-cols-2 gap-4 my-6">
            <!-- Delivery Rate -->
            <div class="p-4 rounded-2xl bg-emerald-500/5 border border-emerald-500/20 text-center">
                <div class="text-3xl font-black text-emerald-600 dark:text-emerald-400">
                    {{ deliveryStats.delivery_rate }}%
                </div>
                <div class="text-xs font-bold text-slate-700 dark:text-slate-300 mt-1">
                    {{ $t('Delivery Rate') }}
                </div>
                <div class="text-[10px] text-slate-400 mt-0.5">
                    {{ $t('Delivered') }} / {{ $t('Sent') }}
                </div>
            </div>

            <!-- Read Rate -->
            <div class="p-4 rounded-2xl bg-primary/5 border border-primary/20 text-center">
                <div class="text-3xl font-black text-primary">
                    {{ deliveryStats.read_rate }}%
                </div>
                <div class="text-xs font-bold text-slate-700 dark:text-slate-300 mt-1">
                    {{ $t('Read Rate') }}
                </div>
                <div class="text-[10px] text-slate-400 mt-0.5">
                    {{ $t('Read') }} / {{ $t('Delivered') }}
                </div>
            </div>
        </div>

        <!-- Funnel Progress Breakdown -->
        <div class="space-y-3.5">
            <!-- Sent -->
            <div>
                <div class="flex justify-between text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">
                    <span>{{ $t('Outbound Sent') }}</span>
                    <span class="font-bold text-slate-900 dark:text-white">{{ formatNumber(deliveryStats.outbound) }}</span>
                </div>
                <div class="h-2 w-full rounded-full bg-slate-100 dark:bg-white/5 overflow-hidden">
                    <div class="h-full bg-slate-400 rounded-full w-full"></div>
                </div>
            </div>

            <!-- Delivered -->
            <div>
                <div class="flex justify-between text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">
                    <span class="text-emerald-600 dark:text-emerald-400">{{ $t('Delivered (✓✓)') }}</span>
                    <span class="font-bold text-slate-900 dark:text-white">{{ formatNumber(deliveryStats.delivered) }} ({{ deliveryStats.delivery_rate }}%)</span>
                </div>
                <div class="h-2 w-full rounded-full bg-slate-100 dark:bg-white/5 overflow-hidden">
                    <div class="h-full bg-emerald-500 rounded-full transition-all duration-500" :style="{ width: `${deliveryStats.delivery_rate}%` }"></div>
                </div>
            </div>

            <!-- Read -->
            <div>
                <div class="flex justify-between text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">
                    <span class="text-primary">{{ $t('Read / Opened') }}</span>
                    <span class="font-bold text-slate-900 dark:text-white">{{ formatNumber(deliveryStats.read) }} ({{ deliveryStats.read_rate }}%)</span>
                </div>
                <div class="h-2 w-full rounded-full bg-slate-100 dark:bg-white/5 overflow-hidden">
                    <div class="h-full bg-primary rounded-full transition-all duration-500" :style="{ width: `${deliveryStats.read_rate}%` }"></div>
                </div>
            </div>

            <!-- Failed -->
            <div>
                <div class="flex justify-between text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">
                    <span class="text-rose-500">{{ $t('Failed / Undelivered') }}</span>
                    <span class="font-bold text-slate-900 dark:text-white">{{ formatNumber(deliveryStats.failed) }} ({{ deliveryStats.failed_rate }}%)</span>
                </div>
                <div class="h-2 w-full rounded-full bg-slate-100 dark:bg-white/5 overflow-hidden">
                    <div class="h-full bg-rose-500 rounded-full transition-all duration-500" :style="{ width: `${deliveryStats.failed_rate}%` }"></div>
                </div>
            </div>
        </div>

        <!-- Footer note -->
        <div class="pt-4 mt-4 border-t border-slate-100 dark:border-white/5 flex items-center justify-between text-[11px] text-slate-400">
            <span>{{ $t('Total Inbound inquiries:') }} <strong class="text-slate-700 dark:text-slate-200">{{ formatNumber(deliveryStats.inbound) }}</strong></span>
            <span>{{ $t('Real-time Webhook data') }}</span>
        </div>
    </div>
</template>

<script setup>
defineProps({
    deliveryStats: {
        type: Object,
        default: () => ({
            inbound: 0,
            outbound: 0,
            delivered: 0,
            read: 0,
            failed: 0,
            delivery_rate: 0,
            read_rate: 0,
            failed_rate: 0,
        }),
    },
});

const formatNumber = (num) => {
    return Number(num || 0).toLocaleString();
};
</script>
