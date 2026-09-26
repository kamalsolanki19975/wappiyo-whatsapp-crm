<template>
    <aside
        v-if="selectedNode"
        class="w-80 sm:w-96 border-l border-slate-200/80 dark:border-white/10 bg-white dark:bg-slate-900 flex flex-col h-[calc(100vh-4rem)] z-20 shadow-xl shadow-slate-900/10 select-none overflow-y-auto"
    >
        <!-- Header -->
        <div class="p-4 border-b border-slate-100 dark:border-white/10 flex items-center justify-between sticky top-0 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md z-10">
            <div class="flex items-center gap-2.5">
                <span class="w-2.5 h-2.5 rounded-full" :class="nodeBadgeColor"></span>
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 dark:text-slate-200">
                        {{ $t('Node Inspector') }}
                    </h3>
                    <p class="text-[11px] text-slate-400 capitalize">
                        {{ selectedNode.type }} {{ $t('Configuration') }}
                    </p>
                </div>
            </div>

            <button
                @click="$emit('close')"
                class="p-1.5 text-slate-400 hover:text-slate-600 dark:hover:text-white rounded-lg hover:bg-slate-100 dark:hover:bg-white/5 transition"
            >
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Form Fields Body -->
        <div class="p-5 space-y-5 flex-1">
            <!-- Node Title -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                    {{ $t('Node Label') }}
                </label>
                <input
                    v-model="nodeData.title"
                    type="text"
                    class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-slate-900 dark:text-white text-xs focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition"
                />
            </div>

            <!-- START / TRIGGER NODE CONFIG -->
            <div v-if="selectedNode.type === 'start'" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                        {{ $t('Trigger Keywords / Phrases') }}
                    </label>
                    <textarea
                        v-model="nodeData.trigger"
                        rows="3"
                        :placeholder="$t('e.g. hi, hello, start, support (comma-separated)')"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-slate-900 dark:text-white text-xs font-mono focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500 transition"
                    ></textarea>
                    <p class="text-[11px] text-slate-400 mt-1">
                        {{ $t('Separate multiple keywords with commas. Example: info, pricing, help') }}
                    </p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                        {{ $t('Match Criteria') }}
                    </label>
                    <select
                        v-model="nodeData.matchCriteria"
                        class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-slate-900 dark:text-white text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500 transition"
                    >
                        <option value="contains">{{ $t('Contains keyword') }}</option>
                        <option value="exact match">{{ $t('Exact match only') }}</option>
                    </select>
                </div>
            </div>

            <!-- ACTION NODE CONFIG -->
            <div v-else-if="selectedNode.type === 'action'" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                        {{ $t('Response Type') }}
                    </label>
                    <select
                        v-model="nodeData.responseType"
                        class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-slate-900 dark:text-white text-xs focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition"
                    >
                        <option value="text">{{ $t('Text Message') }}</option>
                        <option value="template">{{ $t('WhatsApp Template') }}</option>
                    </select>
                </div>

                <!-- Template Selector -->
                <div v-if="nodeData.responseType === 'template'">
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                        {{ $t('Select Template') }}
                    </label>
                    <select
                        v-model="nodeData.response"
                        @change="onTemplateChange"
                        class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-slate-900 dark:text-white text-xs focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition"
                    >
                        <option value="">{{ $t('-- Choose Template --') }}</option>
                        <option
                            v-for="tpl in templates"
                            :key="tpl.uuid || tpl.id"
                            :value="tpl.name"
                        >
                            {{ tpl.name }} ({{ tpl.category || 'MARKETING' }})
                        </option>
                    </select>
                </div>

                <!-- Text Message & Variable Inserter -->
                <div v-else>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                            {{ $t('Message Content') }}
                        </label>
                    </div>

                    <textarea
                        ref="messageTextarea"
                        v-model="nodeData.response"
                        rows="5"
                        :placeholder="$t('Type your automated reply... Use {first_name} for customer name.')"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-slate-900 dark:text-white text-xs leading-relaxed focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition font-sans"
                    ></textarea>

                    <!-- Variable Chips -->
                    <div class="mt-2">
                        <div class="text-[10px] text-slate-400 uppercase tracking-wider mb-1.5 font-semibold">
                            {{ $t('Insert Variables') }}
                        </div>
                        <div class="flex flex-wrap gap-1.5 max-h-24 overflow-y-auto">
                            <button
                                v-for="placeholder in placeholders"
                                :key="placeholder.value"
                                type="button"
                                @click="insertVariable(placeholder.value)"
                                class="px-2 py-1 rounded-lg bg-slate-100 dark:bg-white/5 hover:bg-primary/10 hover:text-primary dark:hover:text-primary text-[11px] font-mono text-slate-600 dark:text-slate-400 border border-slate-200/60 dark:border-white/5 transition"
                            >
                                {{ placeholder.label || placeholder.value }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CONDITION NODE CONFIG -->
            <div v-else-if="selectedNode.type === 'condition'" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                        {{ $t('Evaluate Field') }}
                    </label>
                    <select
                        v-model="nodeData.field"
                        class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-slate-900 dark:text-white text-xs focus:outline-none focus:ring-2 focus:ring-amber-500/40 focus:border-amber-500 transition"
                    >
                        <option value="message">{{ $t('Incoming Message Text') }}</option>
                        <option value="contact_tag">{{ $t('Contact Tag') }}</option>
                        <option value="customer_stage">{{ $t('Customer Stage') }}</option>
                        <option value="phone">{{ $t('Phone Number') }}</option>
                        <option value="email">{{ $t('Email Address') }}</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                        {{ $t('Comparison Operator') }}
                    </label>
                    <select
                        v-model="nodeData.operator"
                        class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-slate-900 dark:text-white text-xs focus:outline-none focus:ring-2 focus:ring-amber-500/40 focus:border-amber-500 transition"
                    >
                        <option value="contains">{{ $t('Contains') }}</option>
                        <option value="equals">{{ $t('Equals Exactly') }}</option>
                        <option value="starts_with">{{ $t('Starts With') }}</option>
                        <option value="not_empty">{{ $t('Is Not Empty') }}</option>
                    </select>
                </div>

                <div v-if="nodeData.operator !== 'not_empty'">
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                        {{ $t('Comparison Value') }}
                    </label>
                    <input
                        v-model="nodeData.value"
                        type="text"
                        :placeholder="$t('e.g. VIP, pricing, urgent')"
                        class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-slate-900 dark:text-white text-xs font-mono focus:outline-none focus:ring-2 focus:ring-amber-500/40 focus:border-amber-500 transition"
                    />
                </div>
            </div>

            <!-- VARIABLE NODE CONFIG -->
            <div v-else-if="selectedNode.type === 'variable'" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                        {{ $t('Variable Name') }}
                    </label>
                    <input
                        v-model="nodeData.variableName"
                        type="text"
                        :placeholder="$t('e.g. customer_interest')"
                        class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-slate-900 dark:text-white text-xs font-mono focus:outline-none focus:ring-2 focus:ring-cyan-500/40 focus:border-cyan-500 transition"
                    />
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                        {{ $t('Variable Value') }}
                    </label>
                    <input
                        v-model="nodeData.variableValue"
                        type="text"
                        :placeholder="$t('e.g. enterprise, hot_lead')"
                        class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-slate-900 dark:text-white text-xs focus:outline-none focus:ring-2 focus:ring-cyan-500/40 focus:border-cyan-500 transition"
                    />
                </div>
            </div>

            <!-- LLM / AI NODE CONFIG -->
            <div v-else-if="selectedNode.type === 'llm'" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                        {{ $t('AI Model') }}
                    </label>
                    <select
                        v-model="nodeData.model"
                        class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-slate-900 dark:text-white text-xs focus:outline-none focus:ring-2 focus:ring-fuchsia-500/40 focus:border-fuchsia-500 transition"
                    >
                        <option value="GPT-4o">GPT-4o (Most Intelligent)</option>
                        <option value="GPT-3.5-turbo">GPT-3.5-turbo (Fast)</option>
                        <option value="Claude-3.5-Sonnet">Claude-3.5 Sonnet</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                        {{ $t('System Instructions / Prompt') }}
                    </label>
                    <textarea
                        v-model="nodeData.prompt"
                        rows="4"
                        :placeholder="$t('Act as a friendly customer representative. Answer FAQs and guide the lead.')"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-slate-900 dark:text-white text-xs leading-relaxed focus:outline-none focus:ring-2 focus:ring-fuchsia-500/40 focus:border-fuchsia-500 transition"
                    ></textarea>
                </div>
            </div>

            <!-- KNOWLEDGE NODE CONFIG -->
            <div v-else-if="selectedNode.type === 'knowledge'" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                        {{ $t('Knowledge Search Target') }}
                    </label>
                    <input
                        v-model="nodeData.query"
                        type="text"
                        :placeholder="$t('e.g. Inbound Question or Keyword')"
                        class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-slate-900 dark:text-white text-xs focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500 transition"
                    />
                </div>
            </div>

            <!-- END NODE -->
            <div v-else-if="selectedNode.type === 'end'" class="p-3 rounded-xl bg-slate-50 dark:bg-white/5 text-xs text-slate-500">
                {{ $t('This node completes the execution branch of the workflow. No further configuration is required.') }}
            </div>
        </div>

        <!-- Footer / Delete Node Button -->
        <div class="p-4 border-t border-slate-100 dark:border-white/10 sticky bottom-0 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md flex items-center justify-between">
            <button
                type="button"
                @click="$emit('deleteNode', selectedNode.id)"
                class="inline-flex items-center gap-1.5 text-xs font-semibold text-rose-600 hover:text-rose-700 hover:bg-rose-50 dark:hover:bg-rose-950/20 px-3 py-2 rounded-xl transition"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                <span>{{ $t('Delete Node') }}</span>
            </button>

            <button
                type="button"
                @click="$emit('close')"
                class="px-4 py-2 text-xs font-semibold bg-primary text-white rounded-xl hover:bg-primary/90 transition shadow-sm"
            >
                {{ $t('Done') }}
            </button>
        </div>
    </aside>
</template>

<script setup>
import { computed, ref } from 'vue';

const props = defineProps({
    selectedNode: { type: Object, default: null },
    templates: { type: Array, default: () => [] },
    placeholders: { type: Array, default: () => [] },
});

const emit = defineEmits(['close', 'deleteNode', 'updateNodeData']);

const messageTextarea = ref(null);

const nodeData = computed({
    get: () => props.selectedNode?.data || {},
    set: (val) => {
        emit('updateNodeData', { id: props.selectedNode.id, data: val });
    },
});

const nodeBadgeColor = computed(() => {
    switch (props.selectedNode?.type) {
        case 'start': return 'bg-emerald-500';
        case 'action': return 'bg-primary';
        case 'condition': return 'bg-amber-500';
        case 'variable': return 'bg-cyan-500';
        case 'llm': return 'bg-fuchsia-500';
        case 'knowledge': return 'bg-blue-500';
        case 'end': return 'bg-slate-400';
        default: return 'bg-primary';
    }
});

const onTemplateChange = (e) => {
    const name = e.target.value;
    nodeData.value.templateName = name;
};

const insertVariable = (variableValue) => {
    const current = nodeData.value.response || '';
    nodeData.value.response = current + ' ' + variableValue;
    if (messageTextarea.value) {
        messageTextarea.value.focus();
    }
};
</script>
