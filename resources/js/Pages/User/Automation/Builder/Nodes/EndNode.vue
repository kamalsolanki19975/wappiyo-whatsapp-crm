<template>
    <div
        class="relative min-w-[200px] max-w-[240px] bg-white dark:bg-slate-900 rounded-2xl border-2 transition-all duration-200 shadow-md select-none"
        :class="selected
            ? 'border-slate-500 ring-4 ring-slate-500/20 shadow-lg'
            : 'border-slate-200 dark:border-white/10 hover:border-slate-400'"
    >
        <!-- Target Handle (Input) -->
        <Handle
            type="target"
            :position="Position.Top"
            class="!w-3.5 !h-3.5 !bg-slate-500 !border-2 !border-white dark:!border-slate-900 !rounded-full hover:!scale-125 !transition-transform"
        />

        <!-- Header / Content -->
        <div class="p-3 bg-gradient-to-r from-slate-500/10 via-slate-500/5 to-transparent rounded-2xl flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <img :src="iconEnd" alt="End" class="w-6 h-6 object-contain" />
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">
                        {{ $t('Complete') }}
                    </span>
                    <h4 class="text-xs font-bold text-slate-900 dark:text-white leading-tight">
                        {{ data.title || $t('End Workflow') }}
                    </h4>
                </div>
            </div>
            <span class="w-2 h-2 rounded-full bg-slate-400"></span>
        </div>
    </div>
</template>

<script setup>
import { Handle, Position } from '@vue-flow/core';
import iconEnd from '@/assets/images/icon_End.png';

defineProps({
    id: { type: String, required: true },
    data: { type: Object, default: () => ({}) },
    selected: { type: Boolean, default: false },
});
</script>
