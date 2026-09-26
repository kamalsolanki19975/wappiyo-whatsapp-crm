<template>
    <header class="h-16 px-4 border-b border-slate-200/80 dark:border-white/10 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md flex items-center justify-between gap-4 z-30 select-none">
        <!-- Left: Back button & Workflow Title -->
        <div class="flex items-center gap-3">
            <button
                @click="$emit('back')"
                :title="$t('Back to Automations')"
                class="p-2 rounded-xl text-slate-500 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-white/5 transition"
            >
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </button>

            <!-- Toggle Palette button (useful for small screens or collapse) -->
            <button
                @click="$emit('togglePalette')"
                :title="$t('Toggle Node Palette')"
                class="p-2 rounded-xl text-slate-500 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-white/5 transition hidden sm:inline-flex"
            >
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16" />
                </svg>
            </button>

            <div class="h-6 w-px bg-slate-200 dark:bg-white/10 mx-1 hidden sm:block"></div>

            <!-- Workflow Title & Status -->
            <div class="flex items-center gap-2.5">
                <input
                    :value="automationName"
                    @input="$emit('update:automationName', $event.target.value)"
                    type="text"
                    class="font-bold text-sm sm:text-base text-slate-900 dark:text-white bg-transparent border border-transparent hover:border-slate-200 dark:hover:border-white/10 focus:border-primary px-2 py-1 rounded-lg focus:outline-none transition max-w-[200px] sm:max-w-xs truncate"
                    :placeholder="$t('Automation Name')"
                />

                <!-- Status Pill Toggle -->
                <button
                    @click="toggleStatus"
                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border transition"
                    :class="statusClasses"
                    :title="$t('Click to toggle status')"
                >
                    <span class="w-1.5 h-1.5 rounded-full" :class="statusDotClasses" />
                    <span>{{ statusLabel }}</span>
                </button>
            </div>
        </div>

        <!-- Center: Canvas Controls & Validation Indicator -->
        <div class="hidden md:flex items-center gap-1.5 bg-slate-100 dark:bg-white/5 p-1 rounded-xl border border-slate-200/80 dark:border-white/10 text-xs text-slate-600 dark:text-slate-300">
            <button
                @click="$emit('zoomIn')"
                :title="$t('Zoom In')"
                class="p-1.5 rounded-lg hover:bg-white dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white transition"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
            </button>

            <button
                @click="$emit('zoomOut')"
                :title="$t('Zoom Out')"
                class="p-1.5 rounded-lg hover:bg-white dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white transition"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                </svg>
            </button>

            <button
                @click="$emit('zoomReset')"
                :title="$t('Reset Zoom (100%)')"
                class="px-2 py-1 rounded-lg hover:bg-white dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white font-mono text-[11px] transition"
            >
                {{ Math.round((zoomLevel || 1) * 100) }}%
            </button>

            <button
                @click="$emit('zoomFit')"
                :title="$t('Fit to View')"
                class="p-1.5 rounded-lg hover:bg-white dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white transition"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                </svg>
            </button>

            <div class="h-4 w-px bg-slate-200 dark:bg-white/10 mx-0.5"></div>

            <!-- Minimap Toggle -->
            <button
                @click="$emit('toggleMinimap')"
                :title="$t('Toggle Minimap')"
                class="p-1.5 rounded-lg transition"
                :class="showMinimap ? 'bg-white dark:bg-slate-800 text-primary dark:text-white shadow-xs' : 'hover:bg-white dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white'"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                </svg>
            </button>

            <!-- Validation Status -->
            <button
                @click="$emit('validate')"
                class="inline-flex items-center gap-1.5 px-2 py-1 rounded-lg transition ml-1"
                :class="validationErrors.length === 0
                    ? 'text-emerald-600 dark:text-emerald-400 hover:bg-emerald-500/10'
                    : 'text-amber-600 dark:text-amber-400 hover:bg-amber-500/10 font-bold'"
                :title="$t('Workflow Validation')"
            >
                <svg v-if="validationErrors.length === 0" class="w-3.5 h-3.5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                </svg>
                <svg v-else class="w-3.5 h-3.5 text-amber-500 animate-bounce" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span class="text-[11px]">{{ validationErrors.length === 0 ? $t('Valid') : `${validationErrors.length} ${$t('issue(s)')}` }}</span>
            </button>
        </div>

        <!-- Right: Test & Save Buttons -->
        <div class="flex items-center gap-2.5">
            <!-- Unsaved changes state indicator -->
            <span class="text-xs text-slate-400 dark:text-slate-500 hidden lg:inline-block">
                {{ isSaving ? $t('Saving...') : (hasUnsavedChanges ? $t('Unsaved changes') : $t('Saved just now')) }}
            </span>

            <!-- Test Workflow button -->
            <button
                @click="$emit('test')"
                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl border border-slate-200/80 dark:border-white/10 hover:bg-slate-100 dark:hover:bg-white/5 text-slate-700 dark:text-slate-200 text-xs font-semibold transition"
            >
                <svg class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="hidden sm:inline">{{ $t('Test Workflow') }}</span>
            </button>

            <!-- Save Workflow primary button -->
            <button
                @click="$emit('save')"
                :disabled="isSaving"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-r from-primary to-violet-600 hover:from-primary/90 hover:to-violet-700 text-white text-xs font-bold shadow-md shadow-primary/25 transition-all duration-200 disabled:opacity-50"
            >
                <svg v-if="isSaving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                </svg>
                <span>{{ $t('Save Workflow') }}</span>
            </button>
        </div>
    </header>
</template>

<script setup>
import { computed } from 'vue';
import { trans } from 'laravel-vue-i18n';

const props = defineProps({
    automationName: { type: String, default: '' },
    status: { type: String, default: 'active' },
    isSaving: { type: Boolean, default: false },
    hasUnsavedChanges: { type: Boolean, default: false },
    validationErrors: { type: Array, default: () => [] },
    showMinimap: { type: Boolean, default: true },
    zoomLevel: { type: Number, default: 1 },
});

const emit = defineEmits([
    'update:automationName',
    'update:status',
    'save',
    'test',
    'zoomIn',
    'zoomOut',
    'zoomFit',
    'zoomReset',
    'toggleMinimap',
    'togglePalette',
    'validate',
    'back',
]);

const statusLabel = computed(() => {
    if (props.status === 'draft') return trans('Draft');
    if (props.status === 'inactive') return trans('Inactive');
    return trans('Active');
});

const statusClasses = computed(() => {
    if (props.status === 'draft') {
        return 'bg-amber-500/10 text-amber-700 dark:text-amber-400 border-amber-500/20 hover:bg-amber-500/20';
    }
    if (props.status === 'inactive') {
        return 'bg-slate-500/10 text-slate-700 dark:text-slate-400 border-slate-500/20 hover:bg-slate-500/20';
    }
    return 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-500/20 hover:bg-emerald-500/20';
});

const statusDotClasses = computed(() => {
    if (props.status === 'draft') return 'bg-amber-500';
    if (props.status === 'inactive') return 'bg-slate-400';
    return 'bg-emerald-500 animate-pulse';
});

const toggleStatus = () => {
    const next = props.status === 'active' ? 'draft' : 'active';
    emit('update:status', next);
};
</script>
