<template>
    <AppLayout>
        <div class="min-h-[calc(100vh-4rem)] p-4 md:p-8 space-y-6 text-slate-900 dark:text-white">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-200/80 dark:border-white/10 pb-6">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <Link
                            href="/automation/basic"
                            class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-white/5 transition"
                        >
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                        </Link>
                        <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                            {{ $t('Update Automation') }}
                        </h1>
                    </div>
                    <p class="text-sm text-slate-500 dark:text-slate-400 ml-9">
                        {{ $t('Edit basic trigger criteria and automated response') }}
                    </p>
                </div>

                <div class="flex items-center gap-3 ml-9 sm:ml-0">
                    <Link
                        :href="`/automation/builder/${props.autoreply.uuid}`"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-r from-primary to-violet-600 hover:from-primary/90 text-white text-xs font-bold shadow-md shadow-primary/20 transition"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                        <span>{{ $t('Open in Visual Builder') }}</span>
                    </Link>

                    <Link
                        href="/automation/basic"
                        class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/5 border border-slate-200/80 dark:border-white/10 transition"
                    >
                        {{ $t('Back') }}
                    </Link>
                </div>
            </div>

            <!-- Visual Flow Builder Banner -->
            <div class="max-w-4xl mx-auto p-5 rounded-3xl bg-gradient-to-r from-primary/10 via-violet-500/10 to-transparent border border-primary/25 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-primary text-white flex items-center justify-center shadow-md shadow-primary/25 shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-900 dark:text-white">
                            {{ $t('Visual Flow Builder') }}
                        </h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            {{ $t('Build multi-step logic with conditions, variables, templates, and AI responses visually on canvas.') }}
                        </p>
                    </div>
                </div>

                <Link
                    :href="`/automation/builder/${props.autoreply.uuid}`"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white dark:bg-slate-800 text-primary dark:text-white hover:bg-primary hover:text-white text-xs font-bold shadow-sm transition border border-slate-200 dark:border-white/10 shrink-0"
                >
                    <span>{{ $t('Launch Canvas') }}</span>
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </Link>
            </div>

            <!-- Form Card -->
            <div class="max-w-4xl mx-auto bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-white/10 p-6 sm:p-8 shadow-sm">
                <form @submit.prevent="submitForm()" class="space-y-6">
                    <!-- Name -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-start pb-6 border-b border-slate-100 dark:border-white/5">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                                {{ $t('Automation Name') }} <span class="text-rose-500">*</span>
                            </label>
                            <p class="text-xs text-slate-500 mt-1">
                                {{ $t('Display name for internal reference') }}
                            </p>
                        </div>
                        <div class="md:col-span-2">
                            <FormInput
                                v-model="form.name"
                                :type="'text'"
                                :error="form.errors.name"
                                :class="'w-full'"
                                :labelClass="'mb-0'"
                            />
                        </div>
                    </div>

                    <!-- Trigger -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-start pb-6 border-b border-slate-100 dark:border-white/5">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                                {{ $t('Trigger Keyword(s)') }} <span class="text-rose-500">*</span>
                            </label>
                            <p class="text-xs text-slate-500 mt-1">
                                {{ $t('Comma-separated words that trigger this response') }}
                            </p>
                        </div>
                        <div class="md:col-span-2">
                            <FormTextArea
                                v-model="form.trigger"
                                :type="'text'"
                                :error="form.errors.trigger"
                                :textAreaRows="3"
                                :class="'w-full font-mono text-xs'"
                            />
                        </div>
                    </div>

                    <!-- Match criteria -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-start pb-6 border-b border-slate-100 dark:border-white/5">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                                {{ $t('Match Criteria') }} <span class="text-rose-500">*</span>
                            </label>
                            <p class="text-xs text-slate-500 mt-1">
                                {{ $t('Trigger matching algorithm') }}
                            </p>
                        </div>
                        <div class="md:col-span-2">
                            <FormSelect
                                v-model="form.match_criteria"
                                :options="criteriaOptions"
                                :error="form.errors.match_criteria"
                                :placeholder="$t('Select criteria')"
                            />
                        </div>
                    </div>

                    <!-- Response type -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-start pb-6 border-b border-slate-100 dark:border-white/5">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                                {{ $t('Response Type') }} <span class="text-rose-500">*</span>
                            </label>
                            <p class="text-xs text-slate-500 mt-1">
                                {{ $t('Select reply channel format') }}
                            </p>
                        </div>
                        <div class="md:col-span-2">
                            <FormSelect
                                v-model="form.response_type"
                                @update:modelValue="clearResponse"
                                :options="responseOptions"
                                :error="form.errors.response_type"
                                :placeholder="$t('Select Type')"
                            />
                        </div>
                    </div>

                    <!-- Response Content: Text -->
                    <div v-if="form.response_type === 'text'" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-start pb-6">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                                {{ $t('Text Response') }} <span class="text-rose-500">*</span>
                            </label>
                            <p class="text-xs text-slate-500 mt-1">
                                {{ $t('Message text sent to customer') }}
                            </p>
                        </div>
                        <div class="md:col-span-2 space-y-3">
                            <textarea
                                v-model="form.response"
                                rows="5"
                                ref="textareaRef"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-slate-900 dark:text-white text-xs leading-relaxed focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition"
                            ></textarea>
                            <div v-if="form.errors.response" class="text-rose-500 text-xs">{{ form.errors.response }}</div>

                            <button
                                type="button"
                                @click="isModalOpen = true"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-white/5 hover:bg-slate-200 text-xs font-semibold text-slate-700 dark:text-slate-300 transition"
                            >
                                <svg class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                <span>{{ $t('Insert Variable') }}</span>
                            </button>
                        </div>
                    </div>

                    <!-- Response Content: Template -->
                    <div v-else-if="form.response_type === 'template'" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-start pb-6">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                                {{ $t('Template Response') }} <span class="text-rose-500">*</span>
                            </label>
                            <p class="text-xs text-slate-500 mt-1">
                                {{ $t('Select an approved WhatsApp template') }}
                            </p>
                        </div>
                        <div class="md:col-span-2">
                            <FormSelectCombo
                                v-model="form.response"
                                :loadOptions="loadTemplates"
                                :error="form.errors.response"
                                :placeholder="$t('Search and select template')"
                            />
                        </div>
                    </div>

                    <!-- Response Content: Media -->
                    <div v-else-if="form.response_type === 'image' || form.response_type === 'audio'" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-start pb-6">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                                {{ form.response_type === 'image' ? $t('Image File') : $t('Audio File') }}
                            </label>
                        </div>
                        <div class="md:col-span-2">
                            <div class="p-6 border-2 border-dashed border-slate-200 dark:border-white/10 rounded-2xl text-center">
                                <input
                                    type="file"
                                    class="sr-only"
                                    :accept="form.response_type === 'image' ? '.jpg, .png' : '.mp3'"
                                    id="media-upload-edit"
                                    @change="handleFileUpload($event)"
                                />
                                <label for="media-upload-edit" class="cursor-pointer">
                                    <div class="text-xs font-semibold text-primary hover:underline">
                                        {{ form.response ? (form.response.name || form.response) : $t('Click to upload new file') }}
                                    </div>
                                    <p class="text-[11px] text-slate-400 mt-1">
                                        {{ form.response_type === 'image' ? 'PNG, JPG' : 'MP3' }}
                                    </p>
                                </label>
                            </div>
                            <div v-if="form.errors.response" class="text-rose-500 text-xs mt-1">{{ form.errors.response }}</div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="pt-6 border-t border-slate-100 dark:border-white/10 flex items-center justify-end gap-3">
                        <Link
                            href="/automation/basic"
                            class="px-5 py-2.5 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-white/5 transition"
                        >
                            {{ $t('Cancel') }}
                        </Link>

                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-gradient-to-r from-primary to-violet-600 hover:from-primary/90 text-white text-xs font-bold shadow-md shadow-primary/25 transition disabled:opacity-50"
                        >
                            <svg v-if="form.processing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>{{ $t('Update Automation') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>

    <!-- Variable Selection Modal -->
    <Modal :label="$t('Select Variable')" :isOpen="isModalOpen">
        <div class="p-4 space-y-4">
            <p class="text-xs text-slate-500">
                {{ $t('Select a placeholder to insert into your response message.') }}
            </p>

            <div class="grid grid-cols-2 gap-2 max-h-60 overflow-y-auto">
                <button
                    v-for="item in props.placeholders"
                    :key="item.value"
                    @click="addToTextArea(item.value)"
                    class="p-2.5 rounded-xl bg-slate-100 dark:bg-white/5 hover:bg-primary/10 hover:text-primary text-left text-xs font-medium text-slate-700 dark:text-slate-300 transition"
                >
                    {{ $t(item.label || item.value) }}
                </button>
            </div>

            <div class="pt-3 border-t border-slate-100 dark:border-white/10 flex justify-end">
                <button
                    type="button"
                    @click="isModalOpen = false"
                    class="px-4 py-2 text-xs font-semibold bg-slate-100 dark:bg-white/10 text-slate-600 dark:text-slate-300 rounded-xl hover:bg-slate-200 transition"
                >
                    {{ $t('Close') }}
                </button>
            </div>
        </div>
    </Modal>
</template>

<script setup>
import { ref } from 'vue';
import { Link, useForm } from "@inertiajs/vue3";
import axios from 'axios';
import { trans } from 'laravel-vue-i18n';
import AppLayout from '@/Pages/User/Layout/App.vue';
import FormInput from '@/Components/FormInput.vue';
import FormSelect from '@/Components/FormSelect.vue';
import FormSelectCombo from '@/Components/FormSelectCombo.vue';
import FormTextArea from '@/Components/FormTextArea.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps(['autoreply', 'placeholders']);

const isModalOpen = ref(false);
const textareaRef = ref(null);

const meta = (() => {
    try {
        return typeof props.autoreply.metadata === 'string'
            ? JSON.parse(props.autoreply.metadata)
            : (props.autoreply.metadata || {});
    } catch (e) {
        return {};
    }
})();

const type = meta.type || 'text';
const initialResponse = (() => {
    const data = meta.data || {};
    if (type === 'text') {
        return data.text || '';
    } else if (type === 'image' || type === 'audio') {
        return data.file || null;
    } else {
        return data.template || '';
    }
})();

const form = useForm({
    _method: 'put',
    name: props.autoreply.name,
    trigger: props.autoreply.trigger,
    match_criteria: props.autoreply.match_criteria,
    response_type: type,
    response: initialResponse,
});

const criteriaOptions = ref([
    { value: 'contains', label: trans('When text contains trigger keyword') },
    { value: 'exact match', label: trans('When text is an exact match') },
]);

const responseOptions = ref([
    { value: 'text', label: trans('Respond with text') },
    { value: 'template', label: trans('Respond with template') },
    { value: 'image', label: trans('Respond with image') },
    { value: 'audio', label: trans('Respond with audio') },
]);

const loadTemplates = async (query, setOptions) => {
    try {
        const response = await axios.get("/templates?query=" + (query || ''));
        if (response.data && response.data[0]) {
            setOptions(response.data[0]);
        }
    } catch (error) {
        console.error("Error fetching templates:", error);
    }
};

const addToTextArea = (textToAdd) => {
    form.response = (form.response || '') + ' ' + textToAdd;
    isModalOpen.value = false;
};

const handleFileUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.response = file;
    }
};

const clearResponse = () => {
    form.response = null;
};

const submitForm = () => {
    form.post('/automation/basic/' + props.autoreply.uuid);
};
</script>