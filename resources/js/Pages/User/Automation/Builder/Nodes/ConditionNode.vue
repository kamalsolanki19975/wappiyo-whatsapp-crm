<template>
    <div
        class="relative min-w-[240px] max-w-[280px] bg-white dark:bg-slate-900 rounded-2xl border-2 transition-all duration-200 shadow-md select-none"
        :class="selected
            ? 'border-amber-500 ring-4 ring-amber-500/20 shadow-lg shadow-amber-500/10'
            : 'border-slate-200 dark:border-white/10 hover:border-amber-500/50'"
    >
        <!-- Target Handle (Input) -->
        <Handle
            type="target"
            :position="Position.Top"
            class="!w-3.5 !h-3.5 !bg-amber-500 !border-2 !border-white dark:!border-slate-900 !rounded-full hover:!scale-125 !transition-transform"
        />

        <!-- Header -->
        <div class="p-3 bg-gradient-to-r from-amber-500/10 via-amber-500/5 to-transparent rounded-t-2xl border-b border-slate-100 dark:border-white/5 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <img :src="iconCondition" alt="Condition" class="w-6 h-6 object-contain" />
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">
                        {{ $t('Condition') }}
                    </span>
                    <h4 class="text-xs font-bold text-slate-900 dark:text-white leading-tight">
                        {{ data.title || $t('Check Condition') }}
                    </h4>
                </div>
            </div>
            <span class="text-[10px] font-mono font-semibold px-2 py-0.5 rounded bg-amber-500/15 text-amber-700 dark:text-amber-300">
                IF
            </span>
        </div>

        <!-- Body -->
        <div class="p-3 space-y-2">
            <!-- Condition Rule Preview -->
            <div class="p-2 rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-200/60 dark:border-white/5 text-xs">
                <div class="text-[10px] text-slate-400 uppercase tracking-wider mb-0.5">{{ $t('Evaluate') }}</div>
                <div class="font-mono text-[11px] font-medium text-slate-800 dark:text-slate-200 break-words">
                    <span class="text-primary font-semibold">{{ data.field || 'message' }}</span>
                    <span class="text-amber-600 dark:text-amber-400 mx-1">{{ data.operator || 'contains' }}</span>
                    <span class="text-emerald-600 dark:text-emerald-400">"{{ data.value || 'yes' }}"</span>
                </div>
            </div>

            <!-- Branch Footers: YES / NO -->
            <div class="flex items-center justify-between pt-1 px-1 text-[11px] font-bold">
                <div class="flex items-center gap-1 text-emerald-600 dark:text-emerald-400">
                    <span>YES</span>
                    <span class="text-xs">↓</span>
                </div>
                <div class="flex items-center gap-1 text-rose-500">
                    <span>NO</span>
                    <span class="text-xs">↓</span>
                </div>
            </div>
        </div>

        <!-- Source Handles: YES (Left) & NO (Right) -->
        <Handle
            id="yes"
            type="source"
            :position="Position.Bottom"
            style="left: 30%;"
            class="!w-3.5 !h-3.5 !bg-emerald-500 !border-2 !border-white dark:!border-slate-900 !rounded-full hover:!scale-125 !transition-transform"
        />

        <Handle
            id="no"
            type="source"
            :position="Position.Bottom"
            style="left: 70%;"
            class="!w-3.5 !h-3.5 !bg-rose-500 !border-2 !border-white dark:!border-slate-900 !rounded-full hover:!scale-125 !transition-transform"
        />
    </div>
</template>

<script setup>
import { Handle, Position } from '@vue-flow/core';
import iconCondition from '@/assets/images/icon_Condition.png';

defineProps({
    id: { type: String, required: true },
    data: { type: Object, default: () => ({}) },
    selected: { type: Boolean, default: false },
});
</script>
