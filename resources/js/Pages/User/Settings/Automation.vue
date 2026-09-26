<template>
    <SettingLayout :modules="props.modules">
        <div class="max-w-4xl mx-auto space-y-6 pb-20">
            <!-- Header -->
            <div class="border-b border-slate-200/80 dark:border-zinc-800 pb-5">
                <h2 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white flex items-center gap-2">
                    <span>{{ $t('Automation & Reply Priority') }}</span>
                </h2>
                <p class="text-sm text-slate-500 dark:text-zinc-400 mt-1">
                    {{ $t('Set the resolution sequence when inbound customer messages are evaluated for automated responses.') }}
                </p>
            </div>

            <!-- Priority Control Card -->
            <div class="bg-white dark:bg-zinc-900 border border-slate-200/80 dark:border-zinc-800 rounded-2xl p-6 shadow-sm space-y-5">
                <div>
                    <h3 class="text-base font-semibold text-slate-900 dark:text-white">{{ $t('Response Sequence Pipeline') }}</h3>
                    <p class="text-xs text-slate-500 dark:text-zinc-400 mt-1 leading-relaxed">
                        {{ $t('When a message arrives, Wappiyo inspects engines from top to bottom. The first engine that matches will execute the reply. Drag items to reorder your priority hierarchy.') }}
                    </p>
                </div>

                <!-- Info Alert if Flow Builder is active -->
                <div v-if="moduleActive('Flow builder')" class="flex items-start gap-3 p-3.5 rounded-xl bg-amber-50 dark:bg-amber-950/30 border border-amber-200/70 dark:border-amber-900/50 text-amber-800 dark:text-amber-300 text-xs leading-relaxed">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0 mt-0.5 text-amber-600 dark:text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <span>{{ $t('Active Contact Flows: If a customer is currently inside an active interactive flow, ongoing flow steps take precedence over this sequence.') }}</span>
                </div>

                <!-- Draggable Sequence List -->
                <div class="space-y-3 pt-2">
                    <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-zinc-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <polyline points="19 12 12 19 5 12"></polyline>
                        </svg>
                        <span>{{ $t('Execution Hierarchy (Highest to Lowest)') }}</span>
                    </div>

                    <draggable 
                        :list="form.response_sequence" 
                        class="space-y-2.5 max-w-lg" 
                        handle=".drag-handle"
                        @end="submitForm()"
                    >
                        <template #item="{ index, element }">
                            <div class="flex items-center justify-between p-3.5 rounded-xl border border-slate-200/90 dark:border-zinc-800 bg-white dark:bg-zinc-800/60 shadow-2xs hover:border-[#6C5CE7]/60 dark:hover:border-[#6C5CE7]/60 transition-all group">
                                <div class="flex items-center gap-3">
                                    <div class="drag-handle cursor-grab active:cursor-grabbing text-slate-300 dark:text-zinc-600 hover:text-slate-600 dark:hover:text-zinc-300 transition-colors p-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                            <circle cx="9" cy="6" r="1.5"/><circle cx="15" cy="6" r="1.5"/>
                                            <circle cx="9" cy="12" r="1.5"/><circle cx="15" cy="12" r="1.5"/>
                                            <circle cx="9" cy="18" r="1.5"/><circle cx="15" cy="18" r="1.5"/>
                                        </svg>
                                    </div>

                                    <div class="flex items-center gap-2.5">
                                        <!-- Step Number Badge -->
                                        <div class="w-6 h-6 rounded-md bg-slate-100 dark:bg-zinc-700/60 text-slate-700 dark:text-zinc-300 font-bold text-xs flex items-center justify-center">
                                            {{ index + 1 }}
                                        </div>

                                        <!-- Icon based on module name -->
                                        <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                                            :class="{
                                                'bg-purple-100 dark:bg-purple-950/50 text-[#6C5CE7] dark:text-purple-400': element.includes('AI'),
                                                'bg-cyan-100 dark:bg-cyan-950/50 text-cyan-600 dark:text-cyan-400': element.includes('Flow'),
                                                'bg-emerald-100 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400': element.includes('Basic'),
                                            }"
                                        >
                                            <svg v-if="element.includes('AI')" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a10 10 0 1 0 10 10H12V2Z"/><path d="M12 12 2.1 12a10.1 10.1 0 0 0 1.9 4.3L12 12Z"/></svg>
                                            <svg v-else-if="element.includes('Flow')" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="6" height="6" rx="1"/><rect x="15" y="15" width="6" height="6" rx="1"/><path d="M6 9v3a3 3 0 0 0 3 3h6"/></svg>
                                            <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                                        </div>

                                        <div>
                                            <span class="font-semibold text-sm text-slate-900 dark:text-zinc-100 block">{{ element }}</span>
                                            <span class="text-[11px] text-slate-400 dark:text-zinc-500">
                                                {{ element.includes('AI') ? $t('LLM generative reasoning & FAQs') : (element.includes('Flow') ? $t('Multi-step visual interactive paths') : $t('Exact keyword & phrase triggers')) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-xs text-slate-400 dark:text-zinc-500 font-medium pr-2">
                                    {{ index === 0 ? $t('Primary') : (index === 1 ? $t('Secondary') : $t('Fallback')) }}
                                </div>
                            </div>
                        </template>
                    </draggable>

                    <div v-if="isSaving" class="flex items-center gap-2 text-xs text-emerald-600 dark:text-emerald-400 pt-1">
                        <svg class="animate-spin w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>{{ $t('Saving sequence...') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </SettingLayout>
</template>

<script setup>
    import SettingLayout from "./Layout.vue";
    import { ref } from 'vue';
    import { useForm } from "@inertiajs/vue3";
    import draggable from "vuedraggable";

    const props = defineProps(['rows', 'filters', 'settings', 'modules']);
    const config = ref(props.settings?.metadata);
    const settings = ref(config.value ? JSON.parse(config.value) : null);
    const isSaving = ref(false);

    const moduleActive = (moduleName) => {
        if (!props.modules) return false;
        const module = props.modules.find((mod) => mod.name === moduleName);
        return module && module.status === 1;
    };

    const draggableList = ref([
        "Basic Replies",
        moduleActive('Flow builder') ? "Automated Flows" : null,
        moduleActive('AI Assistant') ? "AI Reply Assistant" : null,
    ].filter(item => item !== null));

    const form = useForm({
        response_sequence: settings.value?.automation?.response_sequence ?? draggableList.value,
    });

    const moduleNames = {
        'Automated Flows': 'Flow builder',
        'AI Reply Assistant': 'AI Assistant'
    };

    const removeInactiveModulesFromSequence = () => {
        if (settings.value?.automation?.response_sequence) {
            const updatedSequence = settings.value.automation.response_sequence.filter(module => {
                if (module === 'Basic Replies') {
                    return true;
                }
                return moduleActive(moduleNames[module] || module);
            });

            Object.keys(moduleNames).forEach(moduleKey => {
                const module = moduleNames[moduleKey];
                if (moduleActive(module) && !updatedSequence.includes(moduleKey)) {
                    updatedSequence.push(moduleKey);
                }
            });

            form.response_sequence = updatedSequence;
        }
    };

    removeInactiveModulesFromSequence();

    const submitForm = async () => {
        isSaving.value = true;
        form.post('/settings/automation', {
            preserveScroll: true,
            onFinish: () => {
                isSaving.value = false;
            }
        });
    };
</script>