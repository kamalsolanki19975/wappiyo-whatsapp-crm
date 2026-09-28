<script setup>
import axios from "axios";
import FormInput from '@/Components/FormInput.vue';
import FormSelect from '@/Components/FormSelect.vue';
import WhatsappTemplate from '@/Components/WhatsappTemplate.vue';
import Button from '@/Components/UI/Button.vue';
import Badge from '@/Components/UI/Badge.vue';
import { ref, computed, onMounted } from 'vue';
import { Link, useForm, usePage } from "@inertiajs/vue3";
import { trans } from 'laravel-vue-i18n';

const props = defineProps({
    templates: {
        type: Array,
        default: () => []
    },
    contactGroups: {
        type: Array,
        default: () => []
    },
    settings: {
        type: Object,
        default: null
    },
    contact: {
        type: String,
        default: null
    },
    displayTitle: {
        type: Boolean,
        default: false
    },
    displayCancelBtn: {
        type: Boolean,
        default: true
    },
    isCampaignFlow: {
        type: Boolean,
        default: true
    },
    sendText: {
        type: String,
        default: 'Save'
    }
});

const emit = defineEmits(['viewTemplate']);

const isLoading = ref(false);
const activeWizardStep = ref(1); // 1: Campaign & Audience, 2: Message & Variables, 3: Schedule & Review
const page = usePage();
const activeTimezoneLabel = computed(() => {
    return page.props.timezone_display || 'India Standard Time (UTC+05:30)';
});

const contactGroupOptions = ref([
    { value: 'all', label: trans('All Contacts (Full Broadcast)') },
]);
const templateOptions = ref([]);

const config = computed(() => props.settings?.metadata);
const parsedSettings = computed(() => {
    try {
        if (!config.value) return null;
        return typeof config.value === 'string' ? JSON.parse(config.value) : config.value;
    } catch (_) {
        return null;
    }
});

const variableOptions = ref([
    { value: 'static', label: trans('Static Text') },
    { value: 'dynamic', label: trans('Dynamic Customer Field') }
]);

const dynamicOptions = ref([
    { value: 'first name', label: trans('First Name (e.g. John)') },
    { value: 'last name', label: trans('Last Name (e.g. Smith)') },
    { value: 'name', label: trans('Full Name (e.g. John Smith)') },
    { value: 'phone', label: trans('Phone Number') },
    { value: 'email', label: trans('Email Address') },
]);

const form = useForm({
    name: null,
    template: null,
    contacts: null,
    time: null,
    skip_schedule: false,
    header: {
        format: null,
        text: null,
        parameters: []
    },
    body: {
        text: null,
        parameters: []
    },
    footer: {
        text: null,
    },
    buttons: [],
});

const selectedTemplateData = ref(null);

const loadTemplate = async () => {
    if (!form.template) return;
    try {
        const response = await axios.get('/templates/' + form.template);
        if (response?.data) {
            const metadata = typeof response.data.metadata === 'string'
                ? JSON.parse(response.data.metadata)
                : response.data.metadata;

            selectedTemplateData.value = response.data;
            form.header.format = extractComponent(metadata, 'HEADER', 'format');
            form.header.text = extractComponent(metadata, 'HEADER', 'text');

            const headerExamples = extractComponent(metadata, 'HEADER', 'example');
            if (headerExamples) {
                if (form.header.format === 'TEXT') {
                    form.header.parameters = headerExamples.header_text.map(item => ({
                        type: 'text',
                        selection: 'static',
                        value: item,
                    }));
                } else if (['IMAGE', 'DOCUMENT', 'VIDEO'].includes(form.header.format)) {
                    form.header.parameters = headerExamples.header_handle.map(item => ({
                        type: form.header.format,
                        selection: 'default',
                        value: null,
                        url: item,
                    }));
                }
            } else {
                form.header.parameters = [];
            }

            form.body.text = extractComponent(metadata, 'BODY', 'text');
            const bodyExamples = extractComponent(metadata, 'BODY', 'example');
            if (bodyExamples) {
                form.body.parameters = bodyExamples.body_text[0].map(item => ({
                    type: 'text',
                    selection: 'static',
                    value: item,
                }));
            } else {
                form.body.parameters = [];
            }

            form.footer.text = extractComponent(metadata, 'FOOTER', 'text');

            const buttons = extractComponent(metadata, 'BUTTONS', 'buttons');
            if (buttons) {
                form.buttons = buttons.map(item => ({
                    type: item.type,
                    text: item.text,
                    value: item[item.type.toLowerCase()] ?? null,
                    parameters: (item.type === 'QUICK_REPLY')
                        ? [{ type: 'static', value: null }]
                        : (item.example
                            ? item.example.map(param => ({ type: 'static', value: param }))
                            : []
                        ),
                }));
            } else {
                form.buttons = [];
            }
        }
    } catch (error) {
        console.error('Error fetching template details:', error);
    }
};

const handleFileUpload = (event) => {
    const fileType = form.header.parameters[0]?.type;
    const fileSizeLimit = getFileSizeLimit(fileType);
    const file = event.target.files?.[0];

    if (file && file.size > fileSizeLimit) {
        alert(trans('File size exceeds the limit. Max allowed size: ') + (fileSizeLimit / (1024 * 1024)) + 'MB');
        event.target.value = null;
    } else if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            form.header.parameters[0].url = e.target.result;
        };
        form.header.parameters[0].selection = 'upload';
        form.header.parameters[0].value = file;
        reader.readAsDataURL(file);
    }
};

const getFileAcceptAttribute = (fileType) => {
    switch (fileType) {
        case 'IMAGE':
            return '.png, .jpg, .jpeg';
        case 'DOCUMENT':
            return '.pdf, .txt, .ppt, .doc, .xls, .docx, .pptx, .xlsx';
        case 'VIDEO':
            return '.mp4';
        default:
            return '';
    }
};

const getFileSizeLimit = (fileType) => {
    switch (fileType) {
        case 'IMAGE':
            return 5 * 1024 * 1024; // 5MB
        case 'DOCUMENT':
            return 100 * 1024 * 1024; // 100MB
        case 'VIDEO':
            return 16 * 1024 * 1024; // 16MB
        default:
            return Infinity;
    }
};

const extractComponent = (data, type, customProperty) => {
    if (!data?.components || !Array.isArray(data.components)) return null;
    const component = data.components.find((c) => c.type === type);
    return component ? component[customProperty] : null;
};

const transformOptions = (options) => {
    if (!options || !Array.isArray(options)) return [];
    return options.map((option) => ({
        value: option.uuid,
        label: option.language ? `${option.name} [${option.language.toUpperCase()}]` : option.name,
    }));
};

const submitForm = () => {
    isLoading.value = true;
    form.post(props.isCampaignFlow ? '/campaigns' : '/chat/' + props.contact + '/send/template', {
        onFinish: () => {
            isLoading.value = false;
            if (!props.isCampaignFlow) {
                emit('viewTemplate', false);
            }
        },
    });
};

const selectedGroupName = computed(() => {
    if (form.contacts === 'all') return trans('All Contacts');
    const match = (props.contactGroups || []).find(g => g.uuid === form.contacts);
    return match ? match.name : form.contacts;
});

const selectedTemplateName = computed(() => {
    const match = (props.templates || []).find(t => t.uuid === form.template);
    return match ? match.name : form.template;
});

onMounted(() => {
    templateOptions.value = transformOptions(props.templates);
    contactGroupOptions.value = [...contactGroupOptions.value, ...transformOptions(props.contactGroups)];
});
</script>

<template>
    <div class="h-full flex flex-col md:flex-row overflow-hidden bg-slate-50/70 dark:bg-[#09090B] transition-colors">
        
        <!-- WARNING: WhatsApp Not Connected -->
        <div v-if="!parsedSettings?.whatsapp" class="flex-1 p-6 sm:p-10 flex items-center justify-center">
            <div class="max-w-md w-full rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 p-8 text-center shadow-subtle space-y-4">
                <div class="w-16 h-16 rounded-3xl bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 mx-auto flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">
                    {{ $t('Connect WhatsApp Account First') }}
                </h3>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-zinc-400 leading-relaxed">
                    {{ $t('You must connect your official Meta WhatsApp Cloud API credentials before creating and broadcasting campaigns.') }}
                </p>
                <div class="pt-2">
                    <Link href="/settings/whatsapp">
                        <Button variant="primary" size="md" class="w-full justify-center">
                            {{ $t('Connect WhatsApp Account') }}
                        </Button>
                    </Link>
                </div>
            </div>
        </div>

        <!-- FORM WORKSPACE -->
        <div v-else class="flex-1 flex flex-col md:flex-row h-full overflow-hidden">
            
            <!-- LEFT COLUMN: Stepper & Form Configuration -->
            <div class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 space-y-6">
                
                <!-- Chat View Title if rendered inside chat thread -->
                <div v-if="displayTitle" class="p-3.5 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#6C5CE7]"></span>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">{{ $t('Send WhatsApp Template Message') }}</h3>
                    </div>
                    <Button variant="secondary" size="xs" @click="emit('viewTemplate', false)">
                        {{ $t('Cancel') }}
                    </Button>
                </div>

                <!-- Guided Stepper Navigation Tabs (when in full Campaign flow) -->
                <div v-if="isCampaignFlow" class="flex items-center justify-between p-1 rounded-2xl bg-slate-100 dark:bg-zinc-800/80 text-xs font-semibold">
                    <button
                        type="button"
                        @click="activeWizardStep = 1"
                        :class="[
                            'flex-1 flex items-center justify-center gap-1.5 py-2 rounded-xl transition-all duration-150',
                            activeWizardStep === 1
                                ? 'bg-white dark:bg-zinc-900 text-slate-900 dark:text-white shadow-xs font-bold'
                                : 'text-slate-500 dark:text-zinc-400 hover:text-slate-700'
                        ]"
                    >
                        <span class="w-4 h-4 rounded-full bg-purple-100 dark:bg-purple-950 text-[#6C5CE7] flex items-center justify-center text-[10px] font-bold">1</span>
                        <span>{{ $t('Audience') }}</span>
                    </button>

                    <button
                        type="button"
                        @click="activeWizardStep = 2"
                        :class="[
                            'flex-1 flex items-center justify-center gap-1.5 py-2 rounded-xl transition-all duration-150',
                            activeWizardStep === 2
                                ? 'bg-white dark:bg-zinc-900 text-slate-900 dark:text-white shadow-xs font-bold'
                                : 'text-slate-500 dark:text-zinc-400 hover:text-slate-700'
                        ]"
                    >
                        <span class="w-4 h-4 rounded-full bg-purple-100 dark:bg-purple-950 text-[#6C5CE7] flex items-center justify-center text-[10px] font-bold">2</span>
                        <span>{{ $t('Message') }}</span>
                    </button>

                    <button
                        type="button"
                        @click="activeWizardStep = 3"
                        :class="[
                            'flex-1 flex items-center justify-center gap-1.5 py-2 rounded-xl transition-all duration-150',
                            activeWizardStep === 3
                                ? 'bg-white dark:bg-zinc-900 text-slate-900 dark:text-white shadow-xs font-bold'
                                : 'text-slate-500 dark:text-zinc-400 hover:text-slate-700'
                        ]"
                    >
                        <span class="w-4 h-4 rounded-full bg-purple-100 dark:bg-purple-950 text-[#6C5CE7] flex items-center justify-center text-[10px] font-bold">3</span>
                        <span>{{ $t('Schedule & Launch') }}</span>
                    </button>
                </div>

                <form @submit.prevent="submitForm" class="space-y-6">
                    
                    <!-- STEP 1: CAMPAIGN DETAILS & AUDIENCE -->
                    <div v-show="!isCampaignFlow || activeWizardStep === 1" class="rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 p-5 sm:p-6 shadow-xs space-y-4">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">
                            {{ $t('Campaign Setup & Target Audience') }}
                        </h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <FormInput
                                v-if="isCampaignFlow"
                                v-model="form.name"
                                :name="$t('Campaign Name')"
                                :type="'text'"
                                :error="form.errors.name"
                                :required="true"
                                class="sm:col-span-2"
                                :placeholder="$t('e.g. Summer Flash Sale Announcement')"
                            />

                            <FormSelect
                                v-if="isCampaignFlow"
                                v-model="form.contacts"
                                :options="contactGroupOptions"
                                :name="$t('Target Audience / Group')"
                                :required="true"
                                class="sm:col-span-2"
                                :placeholder="$t('Select recipient audience')"
                                :error="form.errors.contacts"
                            />
                        </div>

                        <div v-if="isCampaignFlow" class="flex justify-end pt-2">
                            <Button
                                type="button"
                                variant="primary"
                                size="sm"
                                @click="activeWizardStep = 2"
                                :disabled="!form.name || !form.contacts"
                            >
                                <span>{{ $t('Continue to Message') }}</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                            </Button>
                        </div>
                    </div>

                    <!-- STEP 2: TEMPLATE & VARIABLES -->
                    <div v-show="!isCampaignFlow || activeWizardStep === 2" class="space-y-4">
                        <!-- Template Selector Card -->
                        <div class="rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 p-5 sm:p-6 shadow-xs space-y-4">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">
                                {{ $t('WhatsApp Template') }}
                            </h3>

                            <FormSelect
                                v-model="form.template"
                                @update:modelValue="loadTemplate"
                                :options="templateOptions"
                                :required="true"
                                :error="form.errors.template"
                                :name="$t('Choose Approved Template')"
                                :placeholder="$t('Select a WhatsApp template...')"
                            />
                        </div>

                        <!-- Header Variables & Media Upload -->
                        <div
                            v-if="form.header.parameters.length > 0"
                            class="rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 p-5 sm:p-6 shadow-xs space-y-4"
                        >
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500 flex items-center gap-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#6C5CE7]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242"/><path d="M12 12v9"/><path d="m16 16-4-4-4 4"/></svg>
                                <span>{{ $t('Header Media & Parameters') }}</span>
                            </h3>

                            <div v-for="(item, index) in form.header.parameters" :key="index" class="space-y-3">
                                <!-- Text Header Parameter -->
                                <div v-if="form.header.parameters[index].type === 'text'" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <FormSelect
                                        v-model="form.header.parameters[index].selection"
                                        :name="$t('Content Type')"
                                        :options="variableOptions"
                                    />
                                    <FormInput
                                        v-if="form.header.parameters[index].selection === 'static'"
                                        :name="$t('Static Value')"
                                        :required="true"
                                        :error="form.errors['header.parameters.0.value']"
                                        v-model="form.header.parameters[index].value"
                                        type="text"
                                    />
                                    <FormSelect
                                        v-if="form.header.parameters[index].selection === 'dynamic'"
                                        :name="$t('Customer Field')"
                                        :required="true"
                                        :error="form.errors['header.parameters.0.value']"
                                        v-model="form.header.parameters[index].value"
                                        :options="dynamicOptions"
                                    />
                                </div>

                                <!-- Media Upload Header -->
                                <div v-if="['IMAGE', 'DOCUMENT', 'VIDEO'].includes(form.header.parameters[index].type)" class="space-y-2">
                                    <div class="border-2 border-dashed border-slate-200 dark:border-zinc-800 rounded-2xl p-5 text-center hover:border-[#6C5CE7] transition-colors">
                                        <input
                                            type="file"
                                            class="sr-only"
                                            :accept="getFileAcceptAttribute(form.header.parameters[index].type)"
                                            :id="'header-media-file-' + index"
                                            @change="handleFileUpload"
                                        />
                                        <label :for="'header-media-file-' + index" class="cursor-pointer flex flex-col items-center">
                                            <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-950/50 text-[#6C5CE7] flex items-center justify-center mb-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                            </div>
                                            <span class="text-xs font-bold text-slate-800 dark:text-zinc-200">
                                                {{ form.header.parameters[index].value ? (form.header.parameters[index].value.name || form.header.parameters[index].value) : $t('Upload Header File') }}
                                            </span>
                                            <span class="text-[11px] text-slate-400 dark:text-zinc-500 mt-1">
                                                {{ form.header.parameters[index].type === 'IMAGE' ? 'PNG, JPG max 5MB' : form.header.parameters[index].type === 'VIDEO' ? 'MP4 max 16MB' : 'PDF, DOCX max 100MB' }}
                                            </span>
                                        </label>
                                    </div>
                                    <div v-if="form.errors['header.parameters.0.value']" class="text-xs text-rose-500">
                                        {{ form.errors['header.parameters.0.value'] }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Body Dynamic Variables -->
                        <div
                            v-if="form.body.parameters.length > 0"
                            class="rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 p-5 sm:p-6 shadow-xs space-y-4"
                        >
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">
                                {{ $t('Body Personalization Variables') }}
                            </h3>

                            <div v-for="(item, index) in form.body.parameters" :key="index" class="p-3.5 rounded-xl bg-slate-50 dark:bg-zinc-900/40 border border-slate-100 dark:border-zinc-800/80 space-y-2">
                                <div class="flex items-center gap-2 text-xs font-bold text-[#6C5CE7]">
                                    <span v-text="'{{' + (index + 1) + '}}'" class="px-2 py-0.5 rounded-md bg-purple-100 dark:bg-purple-950/60 font-mono"></span>
                                    <span class="text-slate-500 dark:text-zinc-400 font-normal">{{ $t('Variable mapping') }}</span>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <FormSelect
                                        v-model="form.body.parameters[index].selection"
                                        :options="variableOptions"
                                        :name="$t('Mapping Type')"
                                    />
                                    <FormInput
                                        v-if="form.body.parameters[index].selection === 'static'"
                                        v-model="form.body.parameters[index].value"
                                        :required="true"
                                        :error="form.errors['body.parameters.' + index + '.value']"
                                        type="text"
                                        :name="$t('Static Value')"
                                    />
                                    <FormSelect
                                        v-if="form.body.parameters[index].selection === 'dynamic'"
                                        v-model="form.body.parameters[index].value"
                                        :required="true"
                                        :error="form.errors['body.parameters.' + index + '.value']"
                                        :options="dynamicOptions"
                                        :name="$t('Customer Field')"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Button Variables -->
                        <div
                            v-if="form.buttons.length > 0"
                            class="rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 p-5 sm:p-6 shadow-xs space-y-4"
                        >
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">
                                {{ $t('Interactive Buttons Configuration') }}
                            </h3>

                            <div v-for="(item, index) in form.buttons" :key="index" class="space-y-2">
                                <div v-if="item.parameters?.length > 0" class="p-3.5 rounded-xl bg-slate-50 dark:bg-zinc-900/40 border border-slate-100 dark:border-zinc-800/80">
                                    <p class="text-xs font-bold text-slate-700 dark:text-zinc-300 mb-2">
                                        {{ $t('Button Label') }}: {{ item.text }}
                                    </p>
                                    <div v-for="(value, key) in item.parameters" :key="key" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <FormSelect
                                            v-model="value.type"
                                            :name="$t('Button Type')"
                                            :options="variableOptions"
                                        />
                                        <FormInput
                                            v-if="value.type === 'static'"
                                            v-model="value.value"
                                            :name="$t('Value')"
                                            :required="true"
                                            :error="form.errors['buttons.' + index + '.parameters.0.value']"
                                            type="text"
                                        />
                                        <FormSelect
                                            v-if="value.type === 'dynamic'"
                                            v-model="value.value"
                                            :name="$t('Value')"
                                            :required="true"
                                            :error="form.errors['buttons.' + index + '.parameters.0.value']"
                                            :options="dynamicOptions"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Stepper navigation for Step 2 -->
                        <div v-if="isCampaignFlow" class="flex items-center justify-between pt-2">
                            <Button type="button" variant="secondary" size="sm" @click="activeWizardStep = 1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
                                <span>{{ $t('Back to Audience') }}</span>
                            </Button>
                            <Button
                                type="button"
                                variant="primary"
                                size="sm"
                                @click="activeWizardStep = 3"
                                :disabled="!form.template"
                            >
                                <span>{{ $t('Continue to Schedule') }}</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                            </Button>
                        </div>
                    </div>

                    <!-- STEP 3: SCHEDULE & LAUNCH REVIEW -->
                    <div v-show="!isCampaignFlow || activeWizardStep === 3" class="space-y-4">
                        <!-- Scheduling Card -->
                        <div v-if="isCampaignFlow" class="rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 p-5 sm:p-6 shadow-xs space-y-4">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">
                                {{ $t('Campaign Timing & Scheduling') }}
                            </h3>

                            <div class="space-y-4">
                                <div class="flex items-center gap-3 p-3.5 rounded-xl bg-purple-50/50 dark:bg-purple-950/20 border border-purple-100 dark:border-purple-900/30">
                                    <input
                                        v-model="form.skip_schedule"
                                        id="skip-schedule"
                                        type="checkbox"
                                        class="h-4 w-4 rounded border-slate-300 dark:border-zinc-700 text-[#6C5CE7] focus:ring-[#6C5CE7] dark:bg-zinc-900 cursor-pointer"
                                    />
                                    <label for="skip-schedule" class="text-xs sm:text-sm font-semibold text-slate-800 dark:text-zinc-200 cursor-pointer">
                                        {{ $t('Send immediately (Skip scheduling)') }}
                                    </label>
                                </div>

                                <div v-if="!form.skip_schedule" class="space-y-1.5">
                                    <FormInput
                                        v-model="form.time"
                                        :name="$t('Scheduled Broadcast Time')"
                                        type="datetime-local"
                                        :error="form.errors.time"
                                        :required="true"
                                    />
                                    <p class="text-[11px] text-slate-500 dark:text-zinc-400 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>{{ $t('Active Timezone:') }} <strong class="text-slate-700 dark:text-zinc-200">{{ activeTimezoneLabel }}</strong></span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Final Review Summary Card (Campaign Flow) -->
                        <div v-if="isCampaignFlow" class="rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 p-5 sm:p-6 shadow-xs space-y-3">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">
                                {{ $t('Review & Confirmation') }}
                            </h3>

                            <dl class="divide-y divide-slate-100 dark:divide-zinc-800/60 text-xs sm:text-sm">
                                <div class="py-2 flex justify-between">
                                    <dt class="text-slate-500 dark:text-zinc-400">{{ $t('Campaign Name') }}</dt>
                                    <dd class="font-bold text-slate-900 dark:text-white">{{ form.name || '—' }}</dd>
                                </div>
                                <div class="py-2 flex justify-between">
                                    <dt class="text-slate-500 dark:text-zinc-400">{{ $t('Target Audience') }}</dt>
                                    <dd class="font-semibold text-purple-600 dark:text-purple-400">{{ selectedGroupName }}</dd>
                                </div>
                                <div class="py-2 flex justify-between">
                                    <dt class="text-slate-500 dark:text-zinc-400">{{ $t('Template') }}</dt>
                                    <dd class="font-mono text-slate-800 dark:text-zinc-200">{{ selectedTemplateName || '—' }}</dd>
                                </div>
                                <div class="py-2 flex justify-between">
                                    <dt class="text-slate-500 dark:text-zinc-400">{{ $t('Delivery Timing') }}</dt>
                                    <dd class="font-semibold text-emerald-600 dark:text-emerald-400 text-right">
                                        <div>{{ form.skip_schedule ? $t('Immediate Broadcast') : (form.time || $t('Scheduled for selected time')) }}</div>
                                        <div v-if="!form.skip_schedule && form.time" class="text-[10px] text-slate-400 font-normal">
                                            {{ activeTimezoneLabel }}
                                        </div>
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Action Bar -->
                        <div class="flex items-center justify-between pt-2">
                            <div v-if="isCampaignFlow">
                                <Button type="button" variant="secondary" size="sm" @click="activeWizardStep = 2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
                                    <span>{{ $t('Back to Message') }}</span>
                                </Button>
                            </div>
                            <div v-else-if="displayCancelBtn">
                                <Button type="button" variant="secondary" size="sm" @click="emit('viewTemplate', false)">
                                    {{ $t('Cancel') }}
                                </Button>
                            </div>

                            <Button
                                type="submit"
                                variant="primary"
                                size="md"
                                :disabled="isLoading || (!form.template) || (isCampaignFlow && (!form.name || !form.contacts))"
                                class="shadow-sm"
                            >
                                <span v-if="!isLoading">
                                    {{ isCampaignFlow ? (form.skip_schedule ? $t('🚀 Launch Campaign Now') : $t('📅 Schedule Campaign')) : (sendText || $t('Send WhatsApp Template')) }}
                                </span>
                                <span v-else>{{ $t('Processing...') }}</span>
                            </Button>
                        </div>
                    </div>

                </form>
            </div>

            <!-- RIGHT COLUMN: Interactive Live WhatsApp Preview Frame -->
            <div class="md:w-96 lg:w-[420px] shrink-0 border-t md:border-t-0 md:border-l border-slate-200/80 dark:border-zinc-800 p-6 flex flex-col items-center justify-center chat-bg transition-colors">
                <div class="w-full max-w-sm space-y-3">
                    <div class="flex items-center justify-between text-xs font-semibold text-slate-500 dark:text-zinc-400 px-1">
                        <span class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span>{{ $t('Live WhatsApp Preview') }}</span>
                        </span>
                        <span class="text-[11px] font-mono opacity-75">{{ $t('Customer View') }}</span>
                    </div>

                    <!-- Phone Frame -->
                    <div class="relative rounded-3xl bg-white dark:bg-[#111113] p-4 shadow-xl border border-slate-200/80 dark:border-zinc-800">
                        <div v-if="form.template" class="w-full">
                            <WhatsappTemplate
                                :parameters="form"
                                :placeholder="true"
                                :visible="true"
                            />
                        </div>
                        <div v-else class="py-16 text-center text-slate-400 dark:text-zinc-500 text-xs space-y-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 mx-auto text-slate-300 dark:text-zinc-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="m3 21 1.9-5.7a8.5 8.5 0 1 1 3.8 3.8z"/></svg>
                            <p>{{ $t('Select a template to preview the message') }}</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>