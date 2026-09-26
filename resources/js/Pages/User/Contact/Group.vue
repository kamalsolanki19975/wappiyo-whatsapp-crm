<template>
    <AppLayout>
        <div class="flex h-full w-full overflow-hidden bg-slate-50 dark:bg-[#09090B] transition-colors">
            
            <!-- LEFT PANEL: Groups List Sidebar -->
            <aside
                :class="[
                    'h-full flex-col bg-white dark:bg-[#111113] border-r border-slate-200/80 dark:border-zinc-800 shrink-0 z-10 transition-all duration-200',
                    group ? 'hidden md:flex md:w-80 lg:w-88 xl:w-96' : 'flex w-full md:w-80 lg:w-88 xl:w-96'
                ]"
            >
                <!-- Groups Header with Count and Create Button -->
                <div class="p-3.5 sm:p-4 border-b border-slate-100 dark:border-zinc-800/80 flex items-center justify-between shrink-0">
                    <div class="flex items-center gap-2">
                        <h2 class="text-base font-extrabold tracking-tight text-slate-900 dark:text-white">
                            {{ $t('Contact Groups') }}
                        </h2>
                        <span class="inline-flex items-center rounded-full bg-slate-100 dark:bg-zinc-800 px-2 py-0.5 text-xs font-semibold text-slate-600 dark:text-zinc-400">
                            {{ props.rowCount ?? 0 }}
                        </span>
                    </div>

                    <div class="flex items-center gap-1.5">
                        <button
                            type="button"
                            @click="openModal"
                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl text-xs font-semibold bg-[#6C5CE7] hover:bg-[#5B4BC4] text-white shadow-xs transition-colors"
                            :title="$t('Add Group')"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            <span class="hidden sm:inline">{{ $t('New') }}</span>
                        </button>
                    </div>
                </div>

                <!-- ContactTable Component in Group Mode -->
                <ContactTable
                    :rows="props.rows"
                    :filters="props.filters"
                    :type="'group'"
                    :activeUuid="group?.uuid"
                    @callback="handleGroup"
                />
            </aside>

            <!-- RIGHT PANEL: Group Profile or Directory Overview -->
            <main
                :class="[
                    'flex-1 min-w-0 h-full flex flex-col relative overflow-hidden transition-colors',
                    group ? 'flex' : 'hidden md:flex'
                ]"
            >
                <div v-if="group" class="h-full">
                    <ContactGroupInfo :group="group" />
                </div>

                <!-- ZERO SELECTION: GROUPS OVERVIEW -->
                <div v-else class="flex-1 overflow-y-auto p-6 sm:p-10 flex flex-col items-center justify-center text-center select-none">
                    <div class="max-w-xl mx-auto space-y-6">
                        <!-- Hero Icon -->
                        <div class="relative inline-flex items-center justify-center">
                            <div class="absolute -inset-4 bg-gradient-to-r from-purple-500/20 via-blue-500/15 to-emerald-500/20 rounded-full blur-xl animate-pulse"></div>
                            <div class="relative w-20 h-20 rounded-3xl bg-white dark:bg-[#111113] shadow-lg border border-slate-200/80 dark:border-zinc-800 flex items-center justify-center text-[#6C5CE7]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>
                            </div>
                        </div>

                        <!-- Header -->
                        <div class="space-y-2">
                            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                                {{ $t('Contact Segments & Groups') }}
                            </h1>
                            <p class="text-sm text-slate-500 dark:text-zinc-400 leading-relaxed max-w-md mx-auto">
                                {{ $t('Organize contacts into high-converting audience segments for WhatsApp broadcast campaigns, marketing sequences, and team tagging.') }}
                            </p>
                        </div>

                        <!-- Action Cards -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 text-left">
                            <button
                                type="button"
                                @click="openModal"
                                class="p-4 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 hover:border-[#6C5CE7] hover:shadow-subtle transition-all duration-150 group text-left"
                            >
                                <div class="w-8 h-8 rounded-xl bg-purple-50 dark:bg-purple-950/50 text-[#6C5CE7] flex items-center justify-center mb-2.5 group-hover:scale-105 transition-transform">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                </div>
                                <h4 class="text-xs font-bold text-slate-900 dark:text-white group-hover:text-[#6C5CE7] transition-colors">
                                    {{ $t('Create New Group') }}
                                </h4>
                                <p class="text-[11px] text-slate-400 dark:text-zinc-500 mt-0.5">
                                    {{ $t('Name and initialize new segment') }}
                                </p>
                            </button>

                            <button
                                type="button"
                                @click="isOpenModal = true"
                                class="p-4 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 hover:border-[#6C5CE7] hover:shadow-subtle transition-all duration-150 group text-left"
                            >
                                <div class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mb-2.5 group-hover:scale-105 transition-transform">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                </div>
                                <h4 class="text-xs font-bold text-slate-900 dark:text-white group-hover:text-[#6C5CE7] transition-colors">
                                    {{ $t('Excel Import Groups') }}
                                </h4>
                                <p class="text-[11px] text-slate-400 dark:text-zinc-500 mt-0.5">
                                    {{ $t('Batch import groups from CSV/XLSX') }}
                                </p>
                            </button>
                        </div>
                    </div>
                </div>
            </main>
        </div>

        <!-- Create Group Form Modal -->
        <FormModal 
            v-model="isOpenFormModal" 
            :label="$t('Create Contact Group')" 
            :url="'/contact-groups'" 
            :form="form" 
            :formInputs="formInputs"
            @callback="handleCallback"
        />

        <!-- Import Modal -->
        <ContactImportModal :type="'group'" v-model:modelValue="isOpenModal"/>
    </AppLayout>
</template>

<script setup>
import AppLayout from "./../Layout/App.vue";
import { ref } from 'vue';
import ContactGroupInfo from '@/Components/ContactGroupInfo.vue';
import ContactImportModal from '@/Components/ContactImportModal.vue';
import ContactTable from '@/Components/Tables/ContactTable.vue';
import FormModal from '@/Components/FormModal.vue';
import { router } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';

const props = defineProps({
    rows: Object,
    filters: Object,
    rowCount: Number,
    group: Object
});

const isOpenModal = ref(false);
const isOpenFormModal = ref(false);

const form = ref({
    name: '',
});

const formInputs = [
    {
        inputType: 'FormInput',
        name: 'name',
        label: trans('Group Name'),
        type: 'text',
        className: 'sm:col-span-6',
        required: true,
    },
];

const openModal = () => {
    form.value.name = '';
    isOpenFormModal.value = true;
};

const handleCallback = () => {
    isOpenFormModal.value = false;
    form.value.name = '';
};

const handleGroup = (value) => {
    router.visit('/contact-groups', {
        method: 'get',
        data: value,
        preserveState: true,
    });
};
</script>