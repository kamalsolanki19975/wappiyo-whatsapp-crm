<template>
    <div
        class="relative min-w-[240px] max-w-[280px] bg-white dark:bg-slate-900 rounded-2xl border-2 transition-all duration-200 shadow-md select-none"
        :class="selected
            ? 'border-cyan-500 ring-4 ring-cyan-500/20 shadow-lg shadow-cyan-500/10'
            : 'border-slate-200 dark:border-white/10 hover:border-cyan-500/50'"
    >
        <!-- Target Handle (Input) -->
        <Handle
            type="target"
            :position="Position.Top"
            class="!w-3.5 !h-3.5 !bg-cyan-500 !border-2 !border-white dark:!border-slate-900 !rounded-full hover:!scale-125 !transition-transform"
        />

        <!-- Header -->
        <div class="p-3 bg-gradient-to-r from-cyan-500/10 via-cyan-500/5 to-transparent rounded-t-2xl border-b border-slate-100 dark:border-white/5 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <img :src="iconVariable" alt="Variable" class="w-6 h-6 object-contain" />
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-cyan-600 dark:text-cyan-400">
                        {{ $t('Variable') }}
                    </span>
                    <h4 class="text-xs font-bold text-slate-900 dark:text-white leading-tight">
                        {{ data.title || $t('Set Variable') }}
                    </h4>
                </div>
            </div>
            <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-cyan-500/15 text-cyan-700 dark:text-cyan-300 font-bold">
                $VAR
            </span>
        </div>

        <!-- Body -->
        <div class="p-3 space-y-2">
            <div class="p-2 rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-200/60 dark:border-white/5 text-xs font-mono">
                <div class="text-[10px] text-slate-400 uppercase tracking-wider mb-0.5">{{ $t('Assignment') }}</div>
                <div class="text-[11px] font-medium text-slate-800 dark:text-slate-200 truncate">
                    <span class="text-cyan-600 dark:text-cyan-400 font-bold">{{ data.variableName || 'customer_stage' }}</span>
                    <span class="text-slate-400 mx-1">=</span>
                    <span class="text-emerald-600 dark:text-emerald-400">"{{ data.variableValue || 'VIP' }}"</span>
                </div>
            </div>
        </div>

        <!-- Source Handle (Output) -->
        <Handle
            type="source"
            :position="Position.Bottom"
            class="!w-3.5 !h-3.5 !bg-cyan-500 !border-2 !border-white dark:!border-slate-900 !rounded-full hover:!scale-125 !transition-transform"
        />
    </div>
</template>

<script setup>
import { Handle, Position } from '@vue-flow/core';
import iconVariable from '@/assets/images/icon_Variable.png';

defineProps({
    id: { type: String, required: true },
    data: { type: Object, default: () => ({}) },
    selected: { type: Boolean, default: false },
});
</script>
