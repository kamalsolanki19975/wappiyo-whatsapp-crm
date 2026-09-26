<template>
    <div
        class="relative min-w-[240px] max-w-[280px] bg-white dark:bg-slate-900 rounded-2xl border-2 transition-all duration-200 shadow-md select-none"
        :class="selected
            ? 'border-blue-500 ring-4 ring-blue-500/20 shadow-lg shadow-blue-500/10'
            : 'border-slate-200 dark:border-white/10 hover:border-blue-500/50'"
    >
        <!-- Target Handle (Input) -->
        <Handle
            type="target"
            :position="Position.Top"
            class="!w-3.5 !h-3.5 !bg-blue-500 !border-2 !border-white dark:!border-slate-900 !rounded-full hover:!scale-125 !transition-transform"
        />

        <!-- Header -->
        <div class="p-3 bg-gradient-to-r from-blue-500/10 via-blue-500/5 to-transparent rounded-t-2xl border-b border-slate-100 dark:border-white/5 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <img :src="iconKnowledge" alt="Knowledge" class="w-6 h-6 object-contain" />
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-blue-600 dark:text-blue-400">
                        {{ $t('Knowledge') }}
                    </span>
                    <h4 class="text-xs font-bold text-slate-900 dark:text-white leading-tight">
                        {{ data.title || $t('Search FAQ / Docs') }}
                    </h4>
                </div>
            </div>
            <span class="text-[10px] font-semibold px-2 py-0.5 rounded bg-blue-500/15 text-blue-700 dark:text-blue-300">
                FAQ
            </span>
        </div>

        <!-- Body -->
        <div class="p-3 space-y-2">
            <div class="p-2 rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-200/60 dark:border-white/5 text-xs">
                <div class="text-[10px] text-slate-400 uppercase tracking-wider mb-0.5">{{ $t('Query Target') }}</div>
                <div class="text-[11px] font-medium text-slate-800 dark:text-slate-200 truncate">
                    {{ data.query || $t('Inbound Customer Question') }}
                </div>
            </div>
        </div>

        <!-- Source Handle (Output) -->
        <Handle
            type="source"
            :position="Position.Bottom"
            class="!w-3.5 !h-3.5 !bg-blue-500 !border-2 !border-white dark:!border-slate-900 !rounded-full hover:!scale-125 !transition-transform"
        />
    </div>
</template>

<script setup>
import { Handle, Position } from '@vue-flow/core';
import iconKnowledge from '@/assets/images/icon_Knowledge.png';

defineProps({
    id: { type: String, required: true },
    data: { type: Object, default: () => ({}) },
    selected: { type: Boolean, default: false },
});
</script>
