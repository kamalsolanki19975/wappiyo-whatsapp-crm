<template>
    <div
        class="relative min-w-[240px] max-w-[280px] bg-white dark:bg-slate-900 rounded-2xl border-2 transition-all duration-200 shadow-md select-none"
        :class="selected
            ? 'border-emerald-500 ring-4 ring-emerald-500/20 shadow-lg shadow-emerald-500/10'
            : 'border-slate-200 dark:border-white/10 hover:border-emerald-500/50'"
    >
        <!-- Header -->
        <div class="p-3 bg-gradient-to-r from-emerald-500/10 via-emerald-500/5 to-transparent rounded-t-2xl border-b border-slate-100 dark:border-white/5 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <img :src="iconStart" alt="Start" class="w-6 h-6 object-contain" />
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">
                        {{ $t('Trigger') }}
                    </span>
                    <h4 class="text-xs font-bold text-slate-900 dark:text-white leading-tight">
                        {{ data.title || $t('Incoming Message') }}
                    </h4>
                </div>
            </div>
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
        </div>

        <!-- Body -->
        <div class="p-3 space-y-2">
            <!-- Trigger Keywords -->
            <div>
                <div class="text-[10px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">
                    {{ $t('Keyword(s)') }}
                </div>
                <div class="flex flex-wrap gap-1 max-h-16 overflow-y-auto">
                    <span
                        v-for="(word, idx) in triggerWords"
                        :key="idx"
                        class="px-2 py-0.5 rounded-md bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 font-mono text-[11px] font-medium border border-emerald-500/20 truncate max-w-[180px]"
                    >
                        {{ word }}
                    </span>
                    <span v-if="triggerWords.length === 0" class="text-[11px] text-slate-400 italic">
                        {{ $t('Any incoming message') }}
                    </span>
                </div>
            </div>

            <!-- Match Criteria -->
            <div class="flex items-center justify-between pt-1 border-t border-slate-100 dark:border-white/5 text-[10px] text-slate-500">
                <span>{{ $t('Match') }}:</span>
                <span class="font-semibold text-slate-700 dark:text-slate-300 capitalize">
                    {{ data.matchCriteria === 'exact match' ? $t('Exact match') : $t('Contains') }}
                </span>
            </div>
        </div>

        <!-- Source Handle (Output) -->
        <Handle
            type="source"
            :position="Position.Bottom"
            class="!w-3.5 !h-3.5 !bg-emerald-500 !border-2 !border-white dark:!border-slate-900 !rounded-full hover:!scale-125 !transition-transform"
        />
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { Handle, Position } from '@vue-flow/core';
import iconStart from '@/assets/images/icon_Start.png';

const props = defineProps({
    id: { type: String, required: true },
    data: { type: Object, default: () => ({}) },
    selected: { type: Boolean, default: false },
});

const triggerWords = computed(() => {
    const raw = props.data?.trigger || '';
    if (!raw) return [];
    return raw.split(',').map(s => s.trim()).filter(Boolean);
});
</script>
