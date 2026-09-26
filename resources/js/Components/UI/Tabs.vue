<script setup>
const props = defineProps({
    tabs: {
        type: Array,
        required: true,
        // Array of { id: string, label: string, icon?: string, count?: number, disabled?: boolean }
    },
    modelValue: {
        type: [String, Number],
        required: true,
    },
    variant: {
        type: String,
        default: 'underline', // 'underline', 'pills', 'segmented'
    },
    className: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['update:modelValue', 'change']);

const selectTab = (tab) => {
    if (!tab.disabled) {
        emit('update:modelValue', tab.id);
        emit('change', tab.id);
    }
};
</script>

<template>
    <div :class="['w-full', className]">
        <!-- Underline variant -->
        <div v-if="variant === 'underline'" class="border-b border-slate-200 dark:border-zinc-800">
            <nav class="-mb-px flex space-x-6 overflow-x-auto scrollbar-none" aria-label="Tabs">
                <button
                    v-for="tab in tabs"
                    :key="tab.id"
                    type="button"
                    :disabled="tab.disabled"
                    :class="[
                        'group inline-flex items-center gap-2 py-3 px-1 border-b-2 font-medium text-sm whitespace-nowrap transition-all duration-150',
                        modelValue === tab.id
                            ? 'border-[#6C5CE7] text-[#6C5CE7] dark:text-purple-400 font-semibold'
                            : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 dark:text-zinc-400 dark:hover:text-zinc-200 dark:hover:border-zinc-700',
                        tab.disabled ? 'opacity-40 cursor-not-allowed' : 'cursor-pointer'
                    ]"
                    @click="selectTab(tab)"
                >
                    <component v-if="tab.icon" :is="tab.icon" class="w-4 h-4" />
                    <span>{{ tab.label }}</span>
                    <span
                        v-if="tab.count !== undefined"
                        :class="[
                            'ml-1.5 py-0.5 px-2 rounded-full text-xs font-semibold',
                            modelValue === tab.id
                                ? 'bg-purple-100 text-[#6C5CE7] dark:bg-purple-950/60 dark:text-purple-300'
                                : 'bg-slate-100 text-slate-600 dark:bg-zinc-800 dark:text-zinc-400'
                        ]"
                    >
                        {{ tab.count }}
                    </span>
                </button>
            </nav>
        </div>

        <!-- Segmented variant -->
        <div
            v-else-if="variant === 'segmented'"
            class="inline-flex p-1 rounded-xl bg-slate-100 dark:bg-zinc-900 border border-slate-200/80 dark:border-zinc-800"
        >
            <button
                v-for="tab in tabs"
                :key="tab.id"
                type="button"
                :disabled="tab.disabled"
                :class="[
                    'inline-flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs sm:text-sm font-medium transition-all duration-150',
                    modelValue === tab.id
                        ? 'bg-white dark:bg-[#18181B] text-slate-900 dark:text-white shadow-sm font-semibold'
                        : 'text-slate-600 dark:text-zinc-400 hover:text-slate-900 dark:hover:text-zinc-100',
                    tab.disabled ? 'opacity-40 cursor-not-allowed' : 'cursor-pointer'
                ]"
                @click="selectTab(tab)"
            >
                <component v-if="tab.icon" :is="tab.icon" class="w-4 h-4" />
                <span>{{ tab.label }}</span>
                <span
                    v-if="tab.count !== undefined"
                    class="py-0.2 px-1.5 rounded-full text-[10px] font-bold bg-purple-100 dark:bg-purple-950/60 text-[#6C5CE7] dark:text-purple-300"
                >
                    {{ tab.count }}
                </span>
            </button>
        </div>

        <!-- Pills variant -->
        <div v-else class="flex gap-2 flex-wrap">
            <button
                v-for="tab in tabs"
                :key="tab.id"
                type="button"
                :disabled="tab.disabled"
                :class="[
                    'inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs sm:text-sm font-medium transition-all duration-150',
                    modelValue === tab.id
                        ? 'bg-[#6C5CE7] text-white shadow-sm shadow-purple-600/30'
                        : 'bg-slate-100 dark:bg-zinc-800 text-slate-600 dark:text-zinc-400 hover:bg-slate-200 dark:hover:bg-zinc-700 hover:text-slate-900 dark:hover:text-zinc-100',
                    tab.disabled ? 'opacity-40 cursor-not-allowed' : 'cursor-pointer'
                ]"
                @click="selectTab(tab)"
            >
                <component v-if="tab.icon" :is="tab.icon" class="w-4 h-4" />
                <span>{{ tab.label }}</span>
                <span
                    v-if="tab.count !== undefined"
                    class="py-0.2 px-1.5 rounded-full text-[10px] font-bold"
                    :class="modelValue === tab.id ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-zinc-700 text-slate-700 dark:text-zinc-300'"
                >
                    {{ tab.count }}
                </span>
            </button>
        </div>
    </div>
</template>
