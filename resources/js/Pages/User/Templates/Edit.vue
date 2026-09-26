<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import axios from 'axios';
import AppLayout from './../Layout/App.vue';
import FormInput from '@/Components/FormInput.vue';
import FormSelect from '@/Components/FormSelect.vue';
import FormTextArea from '@/Components/FormTextArea.vue';
import BodyTextArea from '@/Components/Template/BodyTextArea.vue';
import HeaderTextArea from '@/Components/Template/HeaderTextArea.vue';
import WhatsAppTemplatePreview from '@/Components/Template/WhatsAppTemplatePreview.vue';
import TemplateStatusBadge from '@/Components/Template/TemplateStatusBadge.vue';
import Button from '@/Components/UI/Button.vue';
import Modal from '@/Components/Modal.vue';
import { Link, router } from '@inertiajs/vue3';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';

const props = defineProps({
    languages: {
        type: Array,
        default: () => [],
    },
    template: {
        type: Object,
        required: true,
    },
    title: {
        type: String,
        default: 'Edit Template',
    },
});

const headerCharacterLimit = ref(60);
const headerCharacterCount = ref(0);
const bodyCharacterLimit = ref(1098);
const bodyCharacterCount = ref(0);
const footerCharacterLimit = ref(60);
const footerCharacterCount = ref(0);
const isLoading = ref(false);
const imageUrl = ref(null);
const isModalOpen = ref(false);
const error = ref(null);

const extractComponent = (type, customProperty) => {
    try {
        const metadata = typeof props.template.metadata === 'string'
            ? JSON.parse(props.template.metadata)
            : props.template.metadata;
        const component = metadata?.components?.find(c => c.type === type);
        return component ? component[customProperty] : '';
    } catch (e) {
        return '';
    }
};

const extractButtons = () => {
    try {
        const metadata = typeof props.template.metadata === 'string'
            ? JSON.parse(props.template.metadata)
            : props.template.metadata;
        const btnsComp = metadata?.components?.find(c => c.type === 'BUTTONS');
        if (btnsComp && Array.isArray(btnsComp.buttons)) {
            return btnsComp.buttons.map(b => ({
                name: b.text || 'Button',
                type: b.type,
                text: b.text || '',
                url: b.url || '',
                country: b.country || '',
                phone_number: b.phone_number || '',
                example: b.example || '',
            }));
        }
        return [];
    } catch (e) {
        return [];
    }
};

const form = ref({
    name: props.template.name,
    category: props.template.category || 'UTILITY',
    language: props.template.language || 'en_US',
    header: {
        format: extractComponent('HEADER', 'format') || 'NONE',
        text: extractComponent('HEADER', 'text') || '',
        example: extractComponent('HEADER', 'example') || '',
    },
    body: {
        text: extractComponent('BODY', 'text') || '',
        variables: null,
        example: [],
    },
    footer: {
        text: extractComponent('FOOTER', 'text') || '',
    },
    buttons: extractButtons(),
});

const langOptions = computed(() => {
    return props.languages || [];
});

const categoryOptions = [
    { value: 'UTILITY', label: 'Utility' },
    { value: 'MARKETING', label: 'Marketing' },
    { value: 'AUTHENTICATION', label: 'Authentication' },
];

const changeHeaderType = (format) => {
    form.value.header.format = format;
    form.value.header.example = '';
    if (format === 'NONE') {
        form.value.header.text = null;
    }
};

const handleNameInput = (event) => {
    const value = event.target.value.toLowerCase();
    form.value.name = value.replace(/[^a-zA-Z0-9_]/g, '');
};

const addUnderscore = (event) => {
    event.preventDefault();
    if (!form.value.name) {
        form.value.name = '_';
    } else {
        form.value.name += '_';
    }
};

const handleFileUpload = (event) => {
    const file = event.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = (e) => {
        imageUrl.value = e.target.result;
        form.value.header.example = file;
    };
    reader.readAsDataURL(file);
};

const removeFile = () => {
    form.value.header.example = '';
    imageUrl.value = null;
};

const characterCount = (type) => {
    if (type === 'footer') {
        const text = form.value.footer.text || '';
        footerCharacterCount.value = text.length;
        if (text.length > footerCharacterLimit.value) {
            form.value.footer.text = text.slice(0, footerCharacterLimit.value);
            footerCharacterCount.value = footerCharacterLimit.value;
        }
    }
};

const addButton = (type) => {
    if (type === 'call') {
        const count = form.value.buttons.filter(b => b.type === 'PHONE_NUMBER').length;
        if (count < 1) {
            form.value.buttons.push({
                name: 'Call Phone Number',
                type: 'PHONE_NUMBER',
                country: '+1',
                text: 'Call Us',
                phone_number: '',
            });
        } else {
            toast.info('Only 1 phone call button allowed per WhatsApp template');
        }
    } else if (type === 'website') {
        const count = form.value.buttons.filter(b => b.type === 'URL').length;
        if (count < 2) {
            form.value.buttons.push({
                name: 'Website URL',
                type: 'URL',
                text: 'Visit Website',
                url: 'https://',
            });
        } else {
            toast.info('Maximum 2 URL buttons allowed per WhatsApp template');
        }
    } else if (type === 'custom') {
        const count = form.value.buttons.filter(b => b.type === 'QUICK_REPLY').length;
        if (count < 6) {
            form.value.buttons.push({
                name: 'Custom Button',
                type: 'QUICK_REPLY',
                text: 'Quick Reply',
            });
        } else {
            toast.info('Maximum 6 Quick Reply buttons allowed per WhatsApp template');
        }
    } else if (type === 'offer') {
        const count = form.value.buttons.filter(b => b.type === 'COPY_CODE').length;
        if (count < 1) {
            form.value.buttons.push({
                name: 'Offer Code',
                type: 'COPY_CODE',
                example: 'OFFER20',
            });
        } else {
            toast.info('Only 1 Copy Code button allowed per WhatsApp template');
        }
    }
};

const removeButton = (index) => {
    if (index >= 0 && index < form.value.buttons.length) {
        form.value.buttons.splice(index, 1);
    }
};

const isFormValid = computed(() => {
    if (
        !form.value.name ||
        form.value.name.trim() === '' ||
        !form.value.language ||
        form.value.language.trim() === '' ||
        !form.value.category ||
        form.value.category.trim() === ''
    ) {
        return false;
    }

    if (!form.value.body.text || form.value.body.text.trim() === '') {
        return false;
    }

    if (form.value.buttons.length > 0) {
        const invalidBtn = form.value.buttons.some(button => {
            if (button.type === 'PHONE_NUMBER') {
                return !button.text || !button.phone_number;
            }
            if (button.type === 'URL') {
                return !button.text || !button.url;
            }
            if (button.type === 'QUICK_REPLY') {
                return !button.text;
            }
            if (button.type === 'COPY_CODE') {
                return !button.example;
            }
            return false;
        });
        if (invalidBtn) return false;
    }

    return true;
});

const updateBodyExamples = (value) => {
    form.value.body.example = value;
};

const updateHeaderExamples = (value) => {
    form.value.header.example = value;
};

const duplicateThis = () => {
    router.visit(`/templates/create?duplicate=${props.template.uuid}`);
};

const submitForm = () => {
    isLoading.value = true;
    isModalOpen.value = true;
    error.value = null;

    axios.post('/templates/' + props.template.uuid, form.value, {
        headers: {
            'Content-Type': 'multipart/form-data',
        },
    })
    .then(response => {
        if (response.data.success === false) {
            isLoading.value = false;
            error.value = response.data.message || 'Meta API rejected this template update.';
        } else {
            toast.success('Template updated successfully!', {
                autoClose: 3000,
            });
            setTimeout(() => {
                router.visit('/templates', { method: 'get' });
            }, 600);
        }
    })
    .catch(err => {
        isLoading.value = false;
        error.value = err.response?.data?.message || err.message || 'An error occurred while updating the template.';
    });
};

const closeModal = () => {
    isModalOpen.value = false;
    setTimeout(() => {
        error.value = null;
    }, 400);
};
</script>

<template>
    <AppLayout>
        <div class="p-4 sm:p-6 lg:p-8 max-w-7xl mx-auto space-y-6">
            <!-- STUDIO TOP BAR -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-200/80 dark:border-zinc-800/80">
                <div class="flex items-center gap-3">
                    <Link href="/templates">
                        <Button variant="secondary" size="sm">
                            <template #icon>
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                            </template>
                            <span>{{ $t('Templates') }}</span>
                        </Button>
                    </Link>

                    <div>
                        <div class="flex items-center gap-2.5">
                            <h1 class="text-xl sm:text-2xl font-black font-mono tracking-tight text-slate-900 dark:text-white">
                                {{ template.name }}
                            </h1>
                            <TemplateStatusBadge :status="template.status" size="xs" />
                        </div>
                        <p class="text-xs text-slate-500 dark:text-zinc-400">
                            {{ $t('Meta ID') }}: <span class="font-mono">{{ template.meta_id || '—' }}</span>
                            <span v-if="template.updated_at">• {{ $t('Updated') }} {{ template.updated_at }}</span>
                        </p>
                    </div>
                </div>

                <!-- Actions: Duplicate & Save Changes -->
                <div class="flex items-center gap-3">
                    <Button
                        type="button"
                        variant="secondary"
                        size="md"
                        @click="duplicateThis"
                    >
                        <template #icon>
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
                        </template>
                        <span>{{ $t('Duplicate') }}</span>
                    </Button>

                    <Button
                        type="button"
                        variant="primary"
                        size="md"
                        @click="submitForm"
                        :disabled="!isFormValid || isLoading"
                        :loading="isLoading"
                    >
                        <template #icon>
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
                        </template>
                        <span>{{ $t('Update Template') }}</span>
                    </Button>
                </div>
            </div>

            <!-- 2-COLUMN STUDIO WORKSPACE -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <!-- LEFT COLUMN: BUILDER FORM (7 COLS) -->
                <div class="lg:col-span-7 space-y-6">
                    
                    <!-- 1. Basic Information Card -->
                    <div class="bg-white dark:bg-[#111113] rounded-2xl border border-slate-200/80 dark:border-zinc-800/80 p-5 sm:p-6 shadow-sm space-y-4">
                        <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-zinc-800/80">
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-6 rounded-lg bg-[#6C5CE7]/10 text-[#6C5CE7] font-bold text-xs flex items-center justify-center">1</span>
                                <h3 class="font-bold text-sm text-slate-900 dark:text-white">{{ $t('Basic Information') }}</h3>
                            </div>
                            <span class="text-[11px] text-slate-400">{{ $t('Required') }}</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Template Name -->
                            <div class="sm:col-span-2 space-y-1">
                                <FormInput 
                                    v-model="form.name" 
                                    :name="$t('Template Name')" 
                                    :type="'text'" 
                                    @input="handleNameInput" 
                                    @keydown.space.prevent="addUnderscore"
                                    :placeholder="$t('e.g. order_shipped_notification')"
                                />
                                <p class="text-[11px] text-slate-400 dark:text-zinc-500">
                                    {{ $t('Only lowercase letters, numbers, and underscores are allowed by Meta.') }}
                                </p>
                            </div>

                            <!-- Category -->
                            <div class="space-y-1">
                                <FormSelect 
                                    v-model="form.category" 
                                    :options="categoryOptions" 
                                    :name="$t('Category')" 
                                    :placeholder="$t('Select Category')"
                                />
                            </div>

                            <!-- Language -->
                            <div class="space-y-1">
                                <FormSelect 
                                    v-model="form.language" 
                                    :options="langOptions" 
                                    :name="$t('Language')" 
                                    :placeholder="$t('Select Language')"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- 2. Header Card (Optional) -->
                    <div class="bg-white dark:bg-[#111113] rounded-2xl border border-slate-200/80 dark:border-zinc-800/80 p-5 sm:p-6 shadow-sm space-y-4">
                        <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-zinc-800/80">
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-6 rounded-lg bg-[#6C5CE7]/10 text-[#6C5CE7] font-bold text-xs flex items-center justify-center">2</span>
                                <h3 class="font-bold text-sm text-slate-900 dark:text-white">{{ $t('Header') }}</h3>
                            </div>
                            <span class="text-[11px] text-slate-400">{{ $t('Optional') }}</span>
                        </div>

                        <!-- Header Format Selector Tabs -->
                        <div class="grid grid-cols-5 p-1 rounded-xl bg-slate-100 dark:bg-zinc-800/80 border border-slate-200/60 dark:border-zinc-700/60 text-xs font-semibold gap-1">
                            <button
                                type="button"
                                @click="changeHeaderType('NONE')"
                                class="py-1.5 rounded-lg transition-all"
                                :class="form.header.format === 'NONE' ? 'bg-white dark:bg-zinc-700 text-[#6C5CE7] dark:text-purple-400 shadow-sm' : 'text-slate-600 dark:text-zinc-400 hover:text-slate-900'"
                            >
                                {{ $t('None') }}
                            </button>
                            <button
                                type="button"
                                @click="changeHeaderType('TEXT')"
                                class="py-1.5 rounded-lg transition-all"
                                :class="form.header.format === 'TEXT' ? 'bg-white dark:bg-zinc-700 text-[#6C5CE7] dark:text-purple-400 shadow-sm' : 'text-slate-600 dark:text-zinc-400 hover:text-slate-900'"
                            >
                                {{ $t('Text') }}
                            </button>
                            <button
                                type="button"
                                @click="changeHeaderType('IMAGE')"
                                class="py-1.5 rounded-lg transition-all"
                                :class="form.header.format === 'IMAGE' ? 'bg-white dark:bg-zinc-700 text-[#6C5CE7] dark:text-purple-400 shadow-sm' : 'text-slate-600 dark:text-zinc-400 hover:text-slate-900'"
                            >
                                {{ $t('Image') }}
                            </button>
                            <button
                                type="button"
                                @click="changeHeaderType('VIDEO')"
                                class="py-1.5 rounded-lg transition-all"
                                :class="form.header.format === 'VIDEO' ? 'bg-white dark:bg-zinc-700 text-[#6C5CE7] dark:text-purple-400 shadow-sm' : 'text-slate-600 dark:text-zinc-400 hover:text-slate-900'"
                            >
                                {{ $t('Video') }}
                            </button>
                            <button
                                type="button"
                                @click="changeHeaderType('DOCUMENT')"
                                class="py-1.5 rounded-lg transition-all"
                                :class="form.header.format === 'DOCUMENT' ? 'bg-white dark:bg-zinc-700 text-[#6C5CE7] dark:text-purple-400 shadow-sm' : 'text-slate-600 dark:text-zinc-400 hover:text-slate-900'"
                            >
                                {{ $t('Document') }}
                            </button>
                        </div>

                        <!-- Header Form Content -->
                        <div class="pt-2">
                            <!-- Text Header -->
                            <div v-if="form.header.format === 'TEXT'">
                                <HeaderTextArea v-model="form.header.text" @updateExamples="updateHeaderExamples" />
                            </div>

                            <!-- Image Header Upload Zone -->
                            <div v-else-if="form.header.format === 'IMAGE'" class="space-y-3">
                                <div class="flex justify-center px-6 pt-5 pb-6 border-2 border-dashed border-slate-300 dark:border-zinc-700 rounded-2xl bg-slate-50/50 dark:bg-zinc-800/30 hover:bg-slate-50 dark:hover:bg-zinc-800/50 transition-colors">
                                    <input
                                        type="file"
                                        class="sr-only"
                                        accept=".jpg, .jpeg, .png"
                                        id="edit-header-image-upload"
                                        @change="handleFileUpload($event)"
                                    />
                                    <div class="text-center">
                                        <div v-if="form.header.example" class="flex items-center gap-2 p-2 bg-purple-50 dark:bg-purple-950/40 rounded-xl border border-purple-200 dark:border-purple-800">
                                            <span class="text-xs font-medium text-purple-700 dark:text-purple-300">
                                                {{ form.header.example.name || $t('Image ready') }}
                                            </span>
                                            <button type="button" @click="removeFile" class="text-purple-500 hover:text-rose-500">
                                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
                                            </button>
                                        </div>
                                        <label v-else for="edit-header-image-upload" class="cursor-pointer space-y-2">
                                            <div class="w-10 h-10 rounded-xl bg-purple-500/10 text-[#6C5CE7] mx-auto flex items-center justify-center">
                                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="3" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                                            </div>
                                            <p class="text-xs font-semibold text-[#6C5CE7]">{{ $t('Upload replacement image') }}</p>
                                            <p class="text-[11px] text-slate-400">{{ $t('JPG or PNG, max 5MB') }}</p>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Video Header Upload Zone -->
                            <div v-else-if="form.header.format === 'VIDEO'" class="space-y-3">
                                <div class="flex justify-center px-6 pt-5 pb-6 border-2 border-dashed border-slate-300 dark:border-zinc-700 rounded-2xl bg-slate-50/50 dark:bg-zinc-800/30 hover:bg-slate-50 dark:hover:bg-zinc-800/50 transition-colors">
                                    <input
                                        type="file"
                                        class="sr-only"
                                        accept=".mp4"
                                        id="edit-header-video-upload"
                                        @change="handleFileUpload($event)"
                                    />
                                    <div class="text-center">
                                        <div v-if="form.header.example" class="flex items-center gap-2 p-2 bg-purple-50 dark:bg-purple-950/40 rounded-xl border border-purple-200 dark:border-purple-800">
                                            <span class="text-xs font-medium text-purple-700 dark:text-purple-300">
                                                {{ form.header.example.name || $t('Video ready') }}
                                            </span>
                                            <button type="button" @click="removeFile" class="text-purple-500 hover:text-rose-500">
                                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
                                            </button>
                                        </div>
                                        <label v-else for="edit-header-video-upload" class="cursor-pointer space-y-2">
                                            <div class="w-10 h-10 rounded-xl bg-purple-500/10 text-[#6C5CE7] mx-auto flex items-center justify-center">
                                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="23 7 16 12 23 17 23 7"/><rect width="15" height="14" x="1" y="5" rx="2" ry="2"/></svg>
                                            </div>
                                            <p class="text-xs font-semibold text-[#6C5CE7]">{{ $t('Upload replacement video') }}</p>
                                            <p class="text-[11px] text-slate-400">{{ $t('MP4 format, max 16MB') }}</p>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Document Header Upload Zone -->
                            <div v-else-if="form.header.format === 'DOCUMENT'" class="space-y-3">
                                <div class="flex justify-center px-6 pt-5 pb-6 border-2 border-dashed border-slate-300 dark:border-zinc-700 rounded-2xl bg-slate-50/50 dark:bg-zinc-800/30 hover:bg-slate-50 dark:hover:bg-zinc-800/50 transition-colors">
                                    <input
                                        type="file"
                                        class="sr-only"
                                        accept=".pdf"
                                        id="edit-header-doc-upload"
                                        @change="handleFileUpload($event)"
                                    />
                                    <div class="text-center">
                                        <div v-if="form.header.example" class="flex items-center gap-2 p-2 bg-purple-50 dark:bg-purple-950/40 rounded-xl border border-purple-200 dark:border-purple-800">
                                            <span class="text-xs font-medium text-purple-700 dark:text-purple-300">
                                                {{ form.header.example.name || $t('PDF ready') }}
                                            </span>
                                            <button type="button" @click="removeFile" class="text-purple-500 hover:text-rose-500">
                                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
                                            </button>
                                        </div>
                                        <label v-else for="edit-header-doc-upload" class="cursor-pointer space-y-2">
                                            <div class="w-10 h-10 rounded-xl bg-purple-500/10 text-[#6C5CE7] mx-auto flex items-center justify-center">
                                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                            </div>
                                            <p class="text-xs font-semibold text-[#6C5CE7]">{{ $t('Upload replacement PDF document') }}</p>
                                            <p class="text-[11px] text-slate-400">{{ $t('PDF format only, max 10MB') }}</p>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Message Body Card (Required) -->
                    <div class="bg-white dark:bg-[#111113] rounded-2xl border border-slate-200/80 dark:border-zinc-800/80 p-5 sm:p-6 shadow-sm space-y-4">
                        <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-zinc-800/80">
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-6 rounded-lg bg-[#6C5CE7]/10 text-[#6C5CE7] font-bold text-xs flex items-center justify-center">3</span>
                                <h3 class="font-bold text-sm text-slate-900 dark:text-white">{{ $t('Message Body') }}</h3>
                            </div>
                            <span class="text-[11px] font-semibold text-rose-500">{{ $t('Required') }}</span>
                        </div>

                        <div>
                            <BodyTextArea v-model="form.body.text" @updateExamples="updateBodyExamples" />
                        </div>
                    </div>

                    <!-- 4. Footer Description (Optional) -->
                    <div class="bg-white dark:bg-[#111113] rounded-2xl border border-slate-200/80 dark:border-zinc-800/80 p-5 sm:p-6 shadow-sm space-y-4">
                        <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-zinc-800/80">
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-6 rounded-lg bg-[#6C5CE7]/10 text-[#6C5CE7] font-bold text-xs flex items-center justify-center">4</span>
                                <h3 class="font-bold text-sm text-slate-900 dark:text-white">{{ $t('Footer Description') }}</h3>
                            </div>
                            <span class="text-[11px] text-slate-400">{{ $t('Optional') }}</span>
                        </div>

                        <div class="space-y-1">
                            <FormTextArea 
                                v-model="form.footer.text" 
                                @input="characterCount('footer')" 
                                :name="$t('Footer text')" 
                                :showLabel="false" 
                                :type="'text'" 
                                :textAreaRows="2" 
                                :placeholder="$t('Add a short disclaimer or opt-out text at the bottom...')"
                            />
                            <div class="flex justify-end">
                                <span class="text-xs font-mono text-slate-400">
                                    {{ footerCharacterCount }} / {{ footerCharacterLimit }} {{ $t('chars') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- 5. Interactive Buttons (Optional) -->
                    <div class="bg-white dark:bg-[#111113] rounded-2xl border border-slate-200/80 dark:border-zinc-800/80 p-5 sm:p-6 shadow-sm space-y-4">
                        <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-zinc-800/80">
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-6 rounded-lg bg-[#6C5CE7]/10 text-[#6C5CE7] font-bold text-xs flex items-center justify-center">5</span>
                                <h3 class="font-bold text-sm text-slate-900 dark:text-white">{{ $t('Buttons & Call to Action') }}</h3>
                            </div>
                            <span class="text-[11px] text-slate-400">{{ $t('Optional') }}</span>
                        </div>

                        <p class="text-xs text-slate-500 dark:text-zinc-400">
                            {{ $t('Add quick response buttons, website links, or phone call buttons to improve customer engagement.') }}
                        </p>

                        <!-- Add Button Grid -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                            <button 
                                type="button"
                                @click="addButton('custom')" 
                                class="p-2.5 rounded-xl border border-slate-200 dark:border-zinc-700/80 bg-slate-50 dark:bg-zinc-800 hover:bg-slate-100 dark:hover:bg-zinc-700/80 transition-colors flex flex-col items-center gap-1.5 text-center"
                            >
                                <svg class="w-4 h-4 text-[#6C5CE7]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 17 4 12 9 7"/><path d="M20 18v-2a4 4 0 0 0-4-4H4"/></svg>
                                <span class="text-xs font-semibold text-slate-800 dark:text-zinc-200">{{ $t('Quick Reply') }}</span>
                                <span class="text-[10px] text-slate-400">{{ $t('Max 6') }}</span>
                            </button>

                            <button 
                                type="button"
                                @click="addButton('website')" 
                                class="p-2.5 rounded-xl border border-slate-200 dark:border-zinc-700/80 bg-slate-50 dark:bg-zinc-800 hover:bg-slate-100 dark:hover:bg-zinc-700/80 transition-colors flex flex-col items-center gap-1.5 text-center"
                            >
                                <svg class="w-4 h-4 text-[#06B6D4]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" x2="21" y1="14" y2="3"/></svg>
                                <span class="text-xs font-semibold text-slate-800 dark:text-zinc-200">{{ $t('Visit URL') }}</span>
                                <span class="text-[10px] text-slate-400">{{ $t('Max 2') }}</span>
                            </button>

                            <button 
                                type="button"
                                @click="addButton('call')" 
                                class="p-2.5 rounded-xl border border-slate-200 dark:border-zinc-700/80 bg-slate-50 dark:bg-zinc-800 hover:bg-slate-100 dark:hover:bg-zinc-700/80 transition-colors flex flex-col items-center gap-1.5 text-center"
                            >
                                <svg class="w-4 h-4 text-emerald-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                <span class="text-xs font-semibold text-slate-800 dark:text-zinc-200">{{ $t('Call Phone') }}</span>
                                <span class="text-[10px] text-slate-400">{{ $t('Max 1') }}</span>
                            </button>

                            <button 
                                type="button"
                                @click="addButton('offer')" 
                                class="p-2.5 rounded-xl border border-slate-200 dark:border-zinc-700/80 bg-slate-50 dark:bg-zinc-800 hover:bg-slate-100 dark:hover:bg-zinc-700/80 transition-colors flex flex-col items-center gap-1.5 text-center"
                            >
                                <svg class="w-4 h-4 text-amber-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
                                <span class="text-xs font-semibold text-slate-800 dark:text-zinc-200">{{ $t('Offer Code') }}</span>
                                <span class="text-[10px] text-slate-400">{{ $t('Max 1') }}</span>
                            </button>
                        </div>

                        <!-- Active Buttons List -->
                        <div v-if="form.buttons.length > 0" class="space-y-3 pt-2">
                            <div 
                                v-for="(btn, index) in form.buttons" 
                                :key="index" 
                                class="p-4 rounded-xl bg-slate-50 dark:bg-zinc-800/60 border border-slate-200/80 dark:border-zinc-700/80 space-y-3"
                            >
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-zinc-300">
                                        {{ btn.name }}
                                    </span>
                                    <button 
                                        type="button" 
                                        @click="removeButton(index)" 
                                        class="p-1 rounded-lg text-slate-400 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-colors"
                                    >
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <!-- Button Label/Text -->
                                    <div :class="btn.type === 'QUICK_REPLY' ? 'sm:col-span-2' : ''">
                                        <FormInput 
                                            v-model="btn.text" 
                                            :name="$t('Button Text')" 
                                            :type="'text'" 
                                            :placeholder="$t('e.g. Yes, confirm / Track Order')"
                                        />
                                    </div>

                                    <!-- URL Input -->
                                    <div v-if="btn.type === 'URL'" class="sm:col-span-2">
                                        <FormInput 
                                            v-model="btn.url" 
                                            :name="$t('Website URL')" 
                                            :type="'url'" 
                                            :placeholder="'https://example.com/order/status'"
                                        />
                                    </div>

                                    <!-- Phone Inputs -->
                                    <template v-if="btn.type === 'PHONE_NUMBER'">
                                        <div>
                                            <FormInput 
                                                v-model="btn.country" 
                                                :name="$t('Country Code')" 
                                                :type="'text'" 
                                                :placeholder="'+1'"
                                            />
                                        </div>
                                        <div>
                                            <FormInput 
                                                v-model="btn.phone_number" 
                                                :name="$t('Phone Number')" 
                                                :type="'text'" 
                                                :placeholder="'5551234567'"
                                            />
                                        </div>
                                    </template>

                                    <!-- Copy Code Input -->
                                    <div v-if="btn.type === 'COPY_CODE'" class="sm:col-span-2">
                                        <FormInput 
                                            v-model="btn.example" 
                                            :name="$t('Sample Coupon Code')" 
                                            :type="'text'" 
                                            :placeholder="'WAPPIYO25'"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN: STICKY LIVE WHATSAPP PREVIEW (5 COLS) -->
                <div class="lg:col-span-5 lg:sticky lg:top-20 space-y-4">
                    <div class="bg-white dark:bg-[#111113] rounded-2xl border border-slate-200/80 dark:border-zinc-800/80 p-5 shadow-sm space-y-4">
                        <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-zinc-800/80">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                <h3 class="font-bold text-sm text-slate-900 dark:text-white">{{ $t('WhatsApp Live Render') }}</h3>
                            </div>
                            <span class="text-[11px] font-mono font-medium text-slate-400">{{ form.name || 'template_preview' }}</span>
                        </div>

                        <!-- Live Render Phone Frame -->
                        <div class="flex justify-center pt-2">
                            <WhatsAppTemplatePreview
                                :name="form.name || 'Template Preview'"
                                :category="form.category"
                                :language="form.language"
                                :header="form.header"
                                :body="form.body"
                                :footer="form.footer"
                                :buttons="form.buttons"
                                :interactive="true"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- UPDATE STATUS MODAL -->
        <Modal :isOpen="isModalOpen" maxWidth="md">
            <div class="p-6 text-center space-y-4">
                <!-- Error State -->
                <div v-if="error" class="space-y-4">
                    <div class="w-12 h-12 rounded-2xl bg-rose-500/10 text-rose-600 dark:text-rose-400 mx-auto flex items-center justify-center">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                    </div>
                    <div class="space-y-1">
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">{{ $t('Template Update Failed') }}</h3>
                        <p class="text-xs text-rose-600 dark:text-rose-400 leading-relaxed font-mono bg-rose-50 dark:bg-rose-950/40 p-3 rounded-xl border border-rose-200 dark:border-rose-900/60">
                            {{ error }}
                        </p>
                    </div>
                    <div>
                        <Button variant="secondary" size="sm" @click="closeModal">
                            {{ $t('Review and Edit Template') }}
                        </Button>
                    </div>
                </div>

                <!-- Updating State -->
                <div v-else class="space-y-4 py-4">
                    <div class="w-12 h-12 rounded-2xl bg-purple-500/10 text-[#6C5CE7] mx-auto flex items-center justify-center animate-spin">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/></svg>
                    </div>
                    <div class="space-y-1">
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">{{ $t('Saving Template Updates...') }}</h3>
                        <p class="text-xs text-slate-500 dark:text-zinc-400">
                            {{ $t('Updating message parameters and sync state.') }}
                        </p>
                    </div>
                </div>
            </div>
        </Modal>
    </AppLayout>
</template>