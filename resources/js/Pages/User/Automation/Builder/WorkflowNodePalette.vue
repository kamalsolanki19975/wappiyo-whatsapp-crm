<template>
    <aside class="w-72 border-r border-slate-200/80 dark:border-white/10 bg-white dark:bg-slate-900 flex flex-col h-[calc(100vh-4rem)] z-20 select-none">
        <!-- Header & Search -->
        <div class="p-4 border-b border-slate-100 dark:border-white/10 space-y-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-primary animate-ping"></span>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 dark:text-slate-200">
                        {{ $t('Node Palette') }}
                    </h3>
                </div>
                <span class="text-[11px] text-slate-400">
                    {{ filteredPaletteNodes.length }} {{ $t('nodes') }}
                </span>
            </div>

            <!-- Search Filter -->
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input
                    v-model="searchQuery"
                    type="text"
                    :placeholder="$t('Search node types...')"
                    class="w-full pl-8 pr-3 py-1.5 rounded-xl border border-slate-200/80 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-slate-900 dark:text-white placeholder:text-slate-400 text-xs focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition"
                />
            </div>
        </div>

        <!-- Node List Grouped by Category -->
        <div class="flex-1 overflow-y-auto p-3 space-y-4">
            <div
                v-for="group in categorizedNodes"
                :key="group.category"
                class="space-y-1.5"
            >
                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 px-2 py-0.5">
                    {{ $t(group.category) }}
                </div>

                <div class="space-y-1">
                    <div
                        v-for="item in group.items"
                        :key="item.type + (item.subType || '')"
                        draggable="true"
                        @dragstart="onDragStart($event, item)"
                        @click="$emit('addNode', item)"
                        class="group flex items-center justify-between p-2.5 rounded-xl border border-slate-200/60 dark:border-white/5 bg-slate-50/50 dark:bg-white/5 hover:bg-white dark:hover:bg-slate-800 hover:border-primary/40 dark:hover:border-primary/50 hover:shadow-sm cursor-grab active:cursor-grabbing transition duration-150"
                    >
                        <div class="flex items-center gap-2.5">
                            <div
                                class="w-8 h-8 rounded-lg flex items-center justify-center p-1.5 border"
                                :class="item.bgClass"
                            >
                                <img
                                    v-if="item.icon"
                                    :src="item.icon"
                                    :alt="item.label"
                                    class="w-full h-full object-contain"
                                />
                                <svg
                                    v-else
                                    class="w-4 h-4"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>

                            <div>
                                <h4 class="text-xs font-semibold text-slate-800 dark:text-slate-200 group-hover:text-primary transition leading-tight">
                                    {{ $t(item.label) }}
                                </h4>
                                <p class="text-[10px] text-slate-400 dark:text-slate-500 leading-tight mt-0.5">
                                    {{ $t(item.description) }}
                                </p>
                            </div>
                        </div>

                        <!-- Add Button -->
                        <button
                            type="button"
                            :title="$t('Click to add to canvas')"
                            class="opacity-0 group-hover:opacity-100 p-1 text-slate-400 hover:text-primary rounded-md transition"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Palette Footer Tip -->
        <div class="p-3 border-t border-slate-100 dark:border-white/10 bg-slate-50/50 dark:bg-white/5 text-[11px] text-slate-500 flex items-center gap-2">
            <svg class="w-4 h-4 text-primary shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ $t('Drag nodes onto canvas or click to add directly.') }}</span>
        </div>
    </aside>
</template>

<script setup>
import { ref, computed } from 'vue';
import iconStart from '@/assets/images/icon_Start.png';
import iconEnd from '@/assets/images/icon_End.png';
import iconCondition from '@/assets/images/icon_Condition.png';
import iconVariable from '@/assets/images/icon_Variable.png';
import iconLLM from '@/assets/images/icon_LLM.png';
import iconKnowledge from '@/assets/images/icon_Knowledge.png';
import iconApi from '@/assets/images/icon_Api.png';

const emit = defineEmits(['addNode']);

const searchQuery = ref('');

const allNodes = [
    // Triggers
    {
        category: 'Triggers',
        type: 'start',
        label: 'Incoming Message',
        description: 'Triggers when a WhatsApp message is received',
        icon: iconStart,
        bgClass: 'bg-emerald-500/10 border-emerald-500/20 text-emerald-600',
        defaultData: {
            title: 'Incoming Message',
            trigger: 'hello, hi, help',
            matchCriteria: 'contains',
        },
    },

    // Actions
    {
        category: 'Actions',
        type: 'action',
        subType: 'text',
        label: 'Send Message',
        description: 'Send a formatted WhatsApp text message',
        icon: iconApi,
        bgClass: 'bg-primary/10 border-primary/20 text-primary',
        defaultData: {
            title: 'Send Message',
            responseType: 'text',
            response: 'Hello! Thanks for reaching out to us.',
        },
    },
    {
        category: 'Actions',
        type: 'action',
        subType: 'template',
        label: 'Send Template',
        description: 'Send an approved WhatsApp message template',
        icon: iconApi,
        bgClass: 'bg-violet-500/10 border-violet-500/20 text-violet-600',
        defaultData: {
            title: 'Send Template',
            responseType: 'template',
            templateName: '',
            response: '',
        },
    },

    // Logic
    {
        category: 'Logic & Branching',
        type: 'condition',
        label: 'Condition (Branch)',
        description: 'Split path by contact tag or message text',
        icon: iconCondition,
        bgClass: 'bg-amber-500/10 border-amber-500/20 text-amber-600',
        defaultData: {
            title: 'Check Condition',
            field: 'message',
            operator: 'contains',
            value: 'pricing',
        },
    },

    // Data
    {
        category: 'Data & Variables',
        type: 'variable',
        label: 'Set Variable',
        description: 'Store or update a custom contact variable',
        icon: iconVariable,
        bgClass: 'bg-cyan-500/10 border-cyan-500/20 text-cyan-600',
        defaultData: {
            title: 'Set Variable',
            variableName: 'customer_stage',
            variableValue: 'lead',
        },
    },

    // AI & Intelligence
    {
        category: 'AI & Intelligence',
        type: 'llm',
        label: 'AI / LLM Assistant',
        description: 'Generate dynamic AI response with GPT model',
        icon: iconLLM,
        bgClass: 'bg-fuchsia-500/10 border-fuchsia-500/20 text-fuchsia-600',
        defaultData: {
            title: 'AI Assistant',
            model: 'GPT-4o',
            prompt: 'Answer customer inquiry politely and offer assistance.',
        },
    },
    {
        category: 'AI & Intelligence',
        type: 'knowledge',
        label: 'Knowledge Query',
        description: 'Retrieve answers from knowledge base & FAQs',
        icon: iconKnowledge,
        bgClass: 'bg-blue-500/10 border-blue-500/20 text-blue-600',
        defaultData: {
            title: 'Search FAQ',
            query: 'Customer Query',
        },
    },

    // Flow Control
    {
        category: 'Flow Control',
        type: 'end',
        label: 'End Workflow',
        description: 'Concludes the automated conversation branch',
        icon: iconEnd,
        bgClass: 'bg-slate-500/10 border-slate-500/20 text-slate-600',
        defaultData: {
            title: 'End Workflow',
        },
    },
];

const filteredPaletteNodes = computed(() => {
    if (!searchQuery.value) return allNodes;
    const q = searchQuery.value.toLowerCase();
    return allNodes.filter(n =>
        n.label.toLowerCase().includes(q) ||
        n.description.toLowerCase().includes(q) ||
        n.category.toLowerCase().includes(q)
    );
});

const categorizedNodes = computed(() => {
    const groups = {};
    filteredPaletteNodes.value.forEach(node => {
        if (!groups[node.category]) {
            groups[node.category] = [];
        }
        groups[node.category].push(node);
    });

    return Object.keys(groups).map(category => ({
        category,
        items: groups[category],
    }));
});

const onDragStart = (event, nodeItem) => {
    if (event.dataTransfer) {
        event.dataTransfer.setData('application/vueflow', JSON.stringify(nodeItem));
        event.dataTransfer.effectAllowed = 'move';
    }
};
</script>
