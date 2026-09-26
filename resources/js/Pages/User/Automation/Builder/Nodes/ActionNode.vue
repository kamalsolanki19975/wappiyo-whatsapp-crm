<template>
    <div
        class="relative min-w-[240px] max-w-[280px] bg-white dark:bg-slate-900 rounded-2xl border-2 transition-all duration-200 shadow-md select-none"
        :class="selected
            ? 'border-primary ring-4 ring-primary/20 shadow-lg shadow-primary/10'
            : 'border-slate-200 dark:border-white/10 hover:border-primary/50'"
    >
        <!-- Target Handle (Input) -->
        <Handle
            type="target"
            :position="Position.Top"
            class="!w-3.5 !h-3.5 !bg-primary !border-2 !border-white dark:!border-slate-900 !rounded-full hover:!scale-125 !transition-transform"
        />

        <!-- Header -->
        <div class="p-3 bg-gradient-to-r from-primary/10 via-primary/5 to-transparent rounded-t-2xl border-b border-slate-100 dark:border-white/5 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-6 h-6 rounded-lg bg-primary/20 text-primary flex items-center justify-center shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-primary">
                        {{ $t('Action') }}
                    </span>
                    <h4 class="text-xs font-bold text-slate-900 dark:text-white leading-tight">
                        {{ data.title || $t('Send WhatsApp Message') }}
                    </h4>
                </div>
            </div>

            <span class="text-[10px] px-2 py-0.5 rounded-full bg-primary/10 text-primary font-semibold uppercase tracking-wider">
                {{ data.responseType || 'Text' }}
            </span>
        </div>

        <!-- Body -->
        <div class="p-3 space-y-2">
            <!-- Message / Template Preview -->
            <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-200/60 dark:border-white/5 text-xs text-slate-700 dark:text-slate-300">
                <div v-if="data.responseType === 'template'" class="flex items-center gap-1.5 text-primary font-medium">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="truncate">{{ data.templateName || data.response || $t('Select template') }}</span>
                </div>
                <div v-else class="line-clamp-3 text-[11px] leading-relaxed break-words font-sans">
                    {{ data.response || $t('Enter message text in inspector...') }}
                </div>
            </div>

            <!-- Attachment badge if applicable -->
            <div v-if="data.responseType === 'image' || data.responseType === 'audio'" class="flex items-center gap-1.5 text-[10px] text-slate-500">
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                </svg>
                <span>{{ $t('Media Attachment') }}</span>
            </div>
        </div>

        <!-- Source Handle (Output) -->
        <Handle
            type="source"
            :position="Position.Bottom"
            class="!w-3.5 !h-3.5 !bg-primary !border-2 !border-white dark:!border-slate-900 !rounded-full hover:!scale-125 !transition-transform"
        />
    </div>
</template>

<script setup>
import { Handle, Position } from '@vue-flow/core';

defineProps({
    id: { type: String, required: true },
    data: { type: Object, default: () => ({}) },
    selected: { type: Boolean, default: false },
});
</script>
