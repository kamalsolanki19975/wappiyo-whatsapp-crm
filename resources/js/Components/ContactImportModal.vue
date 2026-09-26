<script setup>
import { computed, ref, watch } from 'vue';
import { usePage, router } from "@inertiajs/vue3";
import Modal from '@/Components/Modal.vue';
import Button from '@/Components/UI/Button.vue';
import Badge from '@/Components/UI/Badge.vue';
import { trans } from 'laravel-vue-i18n';

const props = defineProps({
    type: {
        type: String,
        default: 'contact',
    },
    modelValue: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['update:modelValue']);
const flashResponse = computed(() => usePage().props.flash?.status || {});

const modalLabel = computed(() =>
    props.type === 'contact' ? trans('Import Contacts') : trans('Import Contact Groups')
);

const isOpenModal = ref(props.modelValue);
const fileName = ref(null);
const progressStatus = ref(null);
const isDragging = ref(false);

watch(() => props.modelValue, (newValue) => {
    isOpenModal.value = newValue;
});

const handleFileUpload = (event) => {
    event.preventDefault();
    const file = event.target.files[0];
    if (file) uploadFile(file);
};

const handleDrop = (event) => {
    event.preventDefault();
    isDragging.value = false;
    const file = event.dataTransfer?.files[0];
    if (file) uploadFile(file);
};

function uploadFile(file) {
    const validTypes = [
        'text/csv',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ];

    if (!validTypes.includes(file.type) && !file.name.endsWith('.csv') && !file.name.endsWith('.xlsx')) {
        alert(trans('Please select a valid CSV or XLSX file'));
        return;
    }

    fileName.value = file.name;
    const formData = new FormData();
    formData.append('file', file);

    router.post(props.type === 'contact' ? '/contacts/import' : '/contact-groups/import', formData, {
        forceFormData: true,
        onProgress: () => {
            progressStatus.value = 'pending';
        },
        onSuccess: () => {
            progressStatus.value = 'complete';
        },
        onError: () => {
            progressStatus.value = null;
        }
    });
}

function closeModal() {
    isOpenModal.value = false;
    emit('update:modelValue', false);
    setTimeout(() => {
        progressStatus.value = null;
        fileName.value = null;
    }, 400);
}
</script>

<template>
    <Modal :label="modalLabel" :isOpen="isOpenModal" @close="closeModal">
        <div class="space-y-4">
            <!-- Instruction text -->
            <p class="text-xs sm:text-sm text-slate-500 dark:text-zinc-400 leading-relaxed">
                {{ type === 'contact'
                    ? $t('Upload a CSV or XLSX file to batch import contacts. For WhatsApp numbers, ensure country code is included (e.g. +91XXXXXXXXXX).')
                    : $t('Upload a CSV or XLSX spreadsheet to batch import contact groups into your organization.')
                }}
            </p>

            <!-- Sample Template Download -->
            <div class="flex items-center justify-between p-3 rounded-xl bg-purple-50/60 dark:bg-purple-950/20 border border-purple-100 dark:border-purple-900/30 text-xs">
                <div class="flex items-center gap-2 text-purple-900 dark:text-purple-300 font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#6C5CE7]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
                    <span>{{ $t('Need the format template?') }}</span>
                </div>
                <a
                    :href="type === 'contact' ? '/contacts.xlsx' : '/contact-groups.xlsx'"
                    class="font-bold text-[#6C5CE7] hover:text-[#5B4BC4] dark:text-purple-400 hover:underline flex items-center gap-1"
                    download
                >
                    <span>{{ $t('Download sample') }}</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                </a>
            </div>

            <!-- Upload Area -->
            <div
                v-if="progressStatus == null || progressStatus === 'complete'"
                @dragover.prevent="isDragging = true"
                @dragleave.prevent="isDragging = false"
                @drop="handleDrop"
                :class="[
                    'relative border-2 border-dashed rounded-2xl p-6 sm:p-8 text-center transition-all duration-200 cursor-pointer',
                    isDragging
                        ? 'border-[#6C5CE7] bg-purple-50/50 dark:bg-purple-950/30'
                        : 'border-slate-200 dark:border-zinc-800 hover:border-slate-300 dark:hover:border-zinc-700 bg-slate-50/50 dark:bg-zinc-900/40'
                ]"
            >
                <input
                    type="file"
                    class="sr-only"
                    accept=".csv,.xlsx"
                    id="file-upload-input"
                    @change="handleFileUpload"
                />

                <label for="file-upload-input" class="cursor-pointer flex flex-col items-center">
                    <div class="w-12 h-12 rounded-2xl bg-purple-100 dark:bg-purple-950/60 text-[#6C5CE7] flex items-center justify-center mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    </div>

                    <p class="text-sm font-semibold text-slate-800 dark:text-zinc-200">
                        {{ $t('Click to upload or drag & drop') }}
                    </p>
                    <p class="text-xs text-slate-400 dark:text-zinc-500 mt-1">
                        {{ $t('Supported formats: CSV, Excel (.xlsx)') }}
                    </p>
                </label>
            </div>

            <!-- Upload In Progress -->
            <div
                v-else
                class="border-2 border-dashed border-purple-200 dark:border-purple-800/40 rounded-2xl p-8 text-center bg-purple-50/30 dark:bg-purple-950/20"
            >
                <div class="flex flex-col items-center">
                    <svg class="animate-spin h-8 w-8 text-[#6C5CE7] mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $t('Importing Data...') }}</p>
                    <p v-if="fileName" class="text-xs text-slate-500 dark:text-zinc-400 mt-1 font-mono">{{ fileName }}</p>
                </div>
            </div>

            <!-- Import Results Summary Cards -->
            <div v-if="progressStatus === 'complete' && flashResponse" class="space-y-2 pt-2">
                <div v-if="flashResponse.successfulImports" class="flex items-center justify-between p-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200/80 dark:border-emerald-800/40 text-xs text-emerald-800 dark:text-emerald-300">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-600 dark:text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                        <span>{{ flashResponse.successfulImports }} / {{ flashResponse.totalImports }} {{ $t('contacts imported successfully') }}</span>
                    </div>
                    <Badge variant="success" size="sm">{{ $t('Done') }}</Badge>
                </div>

                <div v-if="flashResponse.failedDuplicates" class="flex items-center justify-between p-3 rounded-xl bg-amber-50 dark:bg-amber-950/30 border border-amber-200/80 dark:border-amber-800/40 text-xs text-amber-800 dark:text-amber-300">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-amber-600 dark:text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <span>{{ flashResponse.failedDuplicates }} {{ $t('duplicate phone numbers skipped') }}</span>
                    </div>
                    <Badge variant="warning" size="sm">{{ $t('Skipped') }}</Badge>
                </div>

                <div v-if="flashResponse.failedNames" class="flex items-center justify-between p-3 rounded-xl bg-rose-50 dark:bg-rose-950/30 border border-rose-200/80 dark:border-rose-800/40 text-xs text-rose-800 dark:text-rose-300">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-rose-600 dark:text-rose-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                        <span>{{ flashResponse.failedNames }} {{ $t('rows without first name') }}</span>
                    </div>
                    <Badge variant="danger" size="sm">{{ $t('Error') }}</Badge>
                </div>

                <div v-if="flashResponse.failedFormats" class="flex items-center justify-between p-3 rounded-xl bg-rose-50 dark:bg-rose-950/30 border border-rose-200/80 dark:border-rose-800/40 text-xs text-rose-800 dark:text-rose-300">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-rose-600 dark:text-rose-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                        <span>{{ flashResponse.failedFormats }} {{ $t('rows with phone formatting issues') }}</span>
                    </div>
                    <Badge variant="danger" size="sm">{{ $t('Invalid') }}</Badge>
                </div>
            </div>

            <!-- Footer Action -->
            <div class="pt-2 flex justify-end">
                <Button variant="secondary" size="sm" @click="closeModal">
                    {{ $t('Close') }}
                </Button>
            </div>
        </div>
    </Modal>
</template>