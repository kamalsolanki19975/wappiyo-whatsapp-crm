<template>
    <div
        class="relative min-w-[240px] max-w-[280px] bg-white dark:bg-slate-900 rounded-2xl border-2 transition-all duration-200 shadow-md select-none"
        :class="selected
            ? 'border-fuchsia-500 ring-4 ring-fuchsia-500/20 shadow-lg shadow-fuchsia-500/10'
            : 'border-slate-200 dark:border-white/10 hover:border-fuchsia-500/50'"
    >
        <!-- Target Handle (Input) -->
        <Handle
            type="target"
            :position="Position.Top"
            class="!w-3.5 !h-3.5 !bg-fuchsia-500 !border-2 !border-white dark:!border-slate-900 !rounded-full hover:!scale-125 !transition-transform"
        />

        <!-- Header -->
        <div class="p-3 bg-gradient-to-r from-fuchsia-500/10 via-fuchsia-500/5 to-transparent rounded-t-2xl border-b border-slate-100 dark:border-white/5 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <img :src="iconLLM" alt="AI / LLM" class="w-6 h-6 object-contain" />
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-fuchsia-600 dark:text-fuchsia-400">
                        {{ $t('AI / Intelligence') }}
                    </span>
                    <h4 class="text-xs font-bold text-slate-900 dark:text-white leading-tight">
                        {{ data.title || $t('AI Assistant') }}
                    </h4>
                </div>
            </div>
            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-fuchsia-500/15 text-fuchsia-700 dark:text-fuchsia-300">
                {{ data.model || 'GPT-4o' }}
            </span>
        </div>

        <!-- Body -->
        <div class="p-3 space-y-2">
            <div class="p-2 rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-200/60 dark:border-white/5 text-xs">
                <div class="text-[10px] text-slate-400 uppercase tracking-wider mb-0.5">{{ $t('Instruction / Prompt') }}</div>
                <div class="text-[11px] text-slate-700 dark:text-slate-300 line-clamp-2 leading-relaxed italic">
                    "{{ data.prompt || $t('Generate a polite response answering customer question...') }}"
                </div>
            </div>
        </div>

        <!-- Source Handle (Output) -->
        <Handle
            type="source"
            :position="Position.Bottom"
            class="!w-3.5 !h-3.5 !bg-fuchsia-500 !border-2 !border-white dark:!border-slate-900 !rounded-full hover:!scale-125 !transition-transform"
        />
    </div>
</template>

<script setup>
import { Handle, Position } from '@vue-flow/core';
import iconLLM from '@/assets/images/icon_LLM.png';

defineProps({
    id: { type: String, required: true },
    data: { type: Object, default: () => ({}) },
    selected: { type: Boolean, default: false },
});
</script>
