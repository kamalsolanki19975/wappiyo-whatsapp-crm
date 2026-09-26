<template>
    <SettingLayout :modules="props.modules">
        <div class="max-w-4xl mx-auto space-y-6 pb-20">
            <!-- Header -->
            <div class="border-b border-slate-200/80 dark:border-zinc-800 pb-5">
                <h2 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white flex items-center gap-2">
                    <span>{{ $t('Ticketing & Assignment Workflow') }}</span>
                </h2>
                <p class="text-sm text-slate-500 dark:text-zinc-400 mt-1">
                    {{ $t('Control ticket lifecycles, team member auto-assignment rules, and chat visibility permissions.') }}
                </p>
            </div>

            <!-- Master Toggle: Enable Ticketing -->
            <div class="bg-white dark:bg-zinc-900 border border-slate-200/80 dark:border-zinc-800 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <div class="max-w-xl">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-violet-50 dark:bg-violet-950/50 flex items-center justify-center text-[#6C5CE7] dark:text-violet-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"></path>
                                    <path d="M13 5v2"></path><path d="M13 17v2"></path><path d="M13 11v2"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-semibold text-slate-900 dark:text-white">{{ $t('Enable Ticketing Workflow') }}</h3>
                                <p class="text-xs text-slate-500 dark:text-zinc-400 mt-0.5">
                                    {{ $t('Organize WhatsApp conversations into status-driven support tickets with assignment, stages, and resolution states.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <button
                        type="button"
                        role="switch"
                        :aria-checked="form.active"
                        class="w-12 h-6 flex items-center rounded-full p-1 transition-colors duration-200 focus:outline-none shrink-0"
                        :class="form.active ? 'bg-[#6C5CE7]' : 'bg-slate-300 dark:bg-zinc-700'"
                        @click="toggleState1()"
                    >
                        <div 
                            class="bg-white w-4 h-4 rounded-full shadow-md transform duration-200 ease-in-out" 
                            :class="{ 'translate-x-6': form.active }"
                        ></div>
                    </button>
                </div>
            </div>

            <!-- Workflow Rules Subsections (only visible when ticketing is active) -->
            <div v-if="form.active" class="space-y-6">
                <!-- Section 1: Auto Assignment Mode -->
                <div class="bg-white dark:bg-zinc-900 border border-slate-200/80 dark:border-zinc-800 rounded-2xl p-6 shadow-sm space-y-4">
                    <div>
                        <h3 class="text-base font-semibold text-slate-900 dark:text-white">{{ $t('Incoming Chat Distribution') }}</h3>
                        <p class="text-xs text-slate-500 dark:text-zinc-400 mt-0.5">
                            {{ $t('Select how new incoming WhatsApp messages are routed across your support agents.') }}
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                        <!-- Option 1: Manual Pick (Off) -->
                        <div 
                            @click="toggleAutoAssignment(false)"
                            class="relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition-all"
                            :class="form.auto_assignment === false 
                                ? 'border-[#6C5CE7] bg-indigo-50/40 dark:bg-indigo-950/20' 
                                : 'border-slate-200 dark:border-zinc-800 hover:border-slate-300 dark:hover:border-zinc-700 bg-white dark:bg-zinc-900'"
                        >
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-zinc-800 flex items-center justify-center text-slate-600 dark:text-zinc-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <line x1="15" y1="9" x2="9" y2="15"></line>
                                            <line x1="9" y1="9" x2="15" y2="15"></line>
                                        </svg>
                                    </div>
                                    <span class="font-semibold text-sm text-slate-900 dark:text-white">{{ $t('Manual Pick (Off)') }}</span>
                                </div>
                                <div 
                                    class="w-5 h-5 rounded-full border flex items-center justify-center transition-colors"
                                    :class="form.auto_assignment === false 
                                        ? 'border-[#6C5CE7] bg-[#6C5CE7] text-white' 
                                        : 'border-slate-300 dark:border-zinc-700'"
                                >
                                    <svg v-if="form.auto_assignment === false" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-zinc-400 leading-relaxed">
                                {{ $t('Team members pick conversations manually from the Unassigned folder as they become available.') }}
                            </p>
                        </div>

                        <!-- Option 2: Automatic Distribution -->
                        <div 
                            @click="toggleAutoAssignment(true)"
                            class="relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition-all"
                            :class="form.auto_assignment === true 
                                ? 'border-[#6C5CE7] bg-indigo-50/40 dark:bg-indigo-950/20' 
                                : 'border-slate-200 dark:border-zinc-800 hover:border-slate-300 dark:hover:border-zinc-700 bg-white dark:bg-zinc-900'"
                        >
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-950/50 flex items-center justify-center text-[#6C5CE7] dark:text-indigo-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="9" cy="7" r="4"></circle>
                                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                        </svg>
                                    </div>
                                    <span class="font-semibold text-sm text-slate-900 dark:text-white">{{ $t('Auto Distribution') }}</span>
                                </div>
                                <div 
                                    class="w-5 h-5 rounded-full border flex items-center justify-center transition-colors"
                                    :class="form.auto_assignment === true 
                                        ? 'border-[#6C5CE7] bg-[#6C5CE7] text-white' 
                                        : 'border-slate-300 dark:border-zinc-700'"
                                >
                                    <svg v-if="form.auto_assignment === true" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-zinc-400 leading-relaxed">
                                {{ $t('Evenly distribute conversations among all available online team members in round-robin sequence.') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Reopened Chats Behavior -->
                <div class="bg-white dark:bg-zinc-900 border border-slate-200/80 dark:border-zinc-800 rounded-2xl p-6 shadow-sm">
                    <div class="flex items-center justify-between gap-4">
                        <div class="max-w-xl">
                            <h3 class="text-base font-semibold text-slate-900 dark:text-white">{{ $t('Reassign Reopened Conversations') }}</h3>
                            <p class="text-xs text-slate-500 dark:text-zinc-400 mt-1 leading-relaxed">
                                {{ $t('When a contact sends a new message in a previously closed conversation, trigger auto-assignment to find an available agent. If disabled, the chat will route back to the original handler or remain unassigned.') }}
                            </p>
                        </div>
                        <button
                            type="button"
                            role="switch"
                            :aria-checked="form.reassign_reopened_chats"
                            class="w-12 h-6 flex items-center rounded-full p-1 transition-colors duration-200 focus:outline-none shrink-0"
                            :class="form.reassign_reopened_chats ? 'bg-[#6C5CE7]' : 'bg-slate-300 dark:bg-zinc-700'"
                            @click="toggleState2()"
                        >
                            <div 
                                class="bg-white w-4 h-4 rounded-full shadow-md transform duration-200 ease-in-out" 
                                :class="{ 'translate-x-6': form.reassign_reopened_chats }"
                            ></div>
                        </button>
                    </div>
                </div>

                <!-- Section 3: Live Agent Visibility Permissions -->
                <div class="bg-white dark:bg-zinc-900 border border-slate-200/80 dark:border-zinc-800 rounded-2xl p-6 shadow-sm">
                    <div class="flex items-center justify-between gap-4">
                        <div class="max-w-xl">
                            <h3 class="text-base font-semibold text-slate-900 dark:text-white">{{ $t('Global Conversation Visibility') }}</h3>
                            <p class="text-xs text-slate-500 dark:text-zinc-400 mt-1 leading-relaxed">
                                {{ $t('Allow live support agents to view all inbox threads even if not assigned to them. Disable this if agents should strictly see only new unassigned conversations and chats directly assigned to their account.') }}
                            </p>
                        </div>
                        <button
                            type="button"
                            role="switch"
                            :aria-checked="form.allow_agents_to_view_all_chats"
                            class="w-12 h-6 flex items-center rounded-full p-1 transition-colors duration-200 focus:outline-none shrink-0"
                            :class="form.allow_agents_to_view_all_chats ? 'bg-[#6C5CE7]' : 'bg-slate-300 dark:bg-zinc-700'"
                            @click="toggleState3()"
                        >
                            <div 
                                class="bg-white w-4 h-4 rounded-full shadow-md transform duration-200 ease-in-out" 
                                :class="{ 'translate-x-6': form.allow_agents_to_view_all_chats }"
                            ></div>
                        </button>
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

    const props = defineProps(['rows', 'filters', 'settings', 'modules']);
    const config = ref(props.settings?.metadata);
    const settings = ref(config.value ? JSON.parse(config.value) : null);

    const form = useForm({
        active: settings.value?.tickets?.active ?? false,
        auto_assignment: settings.value?.tickets?.auto_assignment ?? false,
        reassign_reopened_chats: settings.value?.tickets?.reassign_reopened_chats ?? false,
        allow_agents_to_view_all_chats: settings.value?.tickets?.allow_agents_to_view_all_chats ?? false,
    });

    const toggleState1 = () => {
        form.active = !form.active;
        submitForm();
    };

    const toggleState2 = () => {
        form.reassign_reopened_chats = !form.reassign_reopened_chats;
        submitForm();
    };

    const toggleState3 = () => {
        form.allow_agents_to_view_all_chats = !form.allow_agents_to_view_all_chats;
        submitForm();
    };

    const toggleAutoAssignment = (el) => {
        form.auto_assignment = el;
        submitForm();
    };

    const submitForm = async () => {
        form.post('/settings/tickets', {
            preserveScroll: true,
        });
    };
</script>