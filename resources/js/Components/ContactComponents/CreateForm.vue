<script setup>
import { ref } from 'vue';
import { Link, useForm } from "@inertiajs/vue3";
import FormCheckbox from '@/Components/FormCheckbox.vue';
import FormInput from '@/Components/FormInput.vue';
import FormPhoneInput from '@/Components/FormPhoneInput.vue';
import FormSelect from '@/Components/FormSelect.vue';
import FormTextArea from '@/Components/FormTextArea.vue';
import Button from '@/Components/UI/Button.vue';
import Avatar from '@/Components/UI/Avatar.vue';
import { trans } from 'laravel-vue-i18n';

const props = defineProps({
    contactGroups: {
        type: Array,
        default: () => [],
    },
    contact: {
        type: Object,
        default: null,
    },
    fields: {
        type: Array,
        default: () => [],
    },
    locationSettings: {
        type: String,
        default: 'before',
    },
});

const fileUrl = ref(props.contact?.avatar || null);
const inputFields = props.fields || [];

const defaultFields = inputFields.reduce((acc, field) => {
    acc[field.name] = "";
    return acc;
}, {});

const getAddressDetail = (value, key) => {
    if (!value) return '';
    try {
        const address = typeof value === 'string' ? JSON.parse(value) : value;
        return address?.[key] ?? '';
    } catch (_) {
        return '';
    }
};

const parseMetadata = (value) => {
    if (!value) return defaultFields;
    try {
        return typeof value === 'string' ? JSON.parse(value) : value;
    } catch (_) {
        return defaultFields;
    }
};

const form = useForm({
    first_name: props.contact?.first_name ?? null,
    last_name: props.contact?.last_name ?? null,
    phone: props.contact?.phone ?? null,
    email: props.contact?.email ?? null,
    group: props.contact?.contact_group?.uuid ?? null,
    file: null,
    street: props.contact?.address ? getAddressDetail(props.contact?.address, 'street') : null,
    city: props.contact?.address ? getAddressDetail(props.contact?.address, 'city') : null,
    state: props.contact?.address ? getAddressDetail(props.contact?.address, 'state') : null,
    zip: props.contact?.address ? getAddressDetail(props.contact?.address, 'zip') : null,
    country: props.contact?.address ? getAddressDetail(props.contact?.address, 'country') : null,
    metadata: parseMetadata(props.contact?.metadata),
});

const contactGroupOptions = () => {
    return (props.contactGroups || []).map((option) => ({
        value: option.uuid,
        label: option.name,
    }));
};

const handleFileUpload = (event) => {
    const fileSizeLimit = 5 * 1024 * 1024; // 5MB
    const file = event.target.files?.[0];

    if (file && file.size > fileSizeLimit) {
        alert(trans('File size exceeds the limit. Max allowed size: 5MB'));
        event.target.value = null;
    } else if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            fileUrl.value = e.target.result;
        };
        form.file = file;
        reader.readAsDataURL(file);
    }
};

const submitForm = () => {
    if (!props.contact) {
        form.post('/contacts');
    } else {
        form.post('/contacts/' + props.contact.uuid);
    }
};

const transformOptions = (optionsString) => {
    if (!optionsString) return [];
    return optionsString.split(", ").map(option => ({ label: option, value: option }));
};
</script>

<template>
    <div class="h-full overflow-y-auto bg-slate-50/70 dark:bg-[#09090B] flex flex-col transition-colors">
        <!-- Sticky Top Form Header Bar -->
        <div class="sticky top-0 z-20 px-4 sm:px-6 lg:px-8 py-3.5 glass-header border-b border-slate-200/80 dark:border-zinc-800 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <Link
                    :href="contact ? '/contacts/' + contact.uuid : '/contacts'"
                    class="p-1.5 -ml-1 text-slate-500 hover:text-slate-800 dark:hover:text-zinc-200 rounded-lg hover:bg-slate-100 dark:hover:bg-zinc-800 transition-colors"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
                </Link>
                <div>
                    <h1 class="text-base font-bold text-slate-900 dark:text-white">
                        {{ !props.contact ? $t('Add New Contact') : $t('Edit Contact') }}
                    </h1>
                    <p class="text-xs text-slate-500 dark:text-zinc-400">
                        {{ !props.contact ? $t('Create customer profile for WhatsApp messaging and CRM.') : $t('Update customer details, metadata and address.') }}
                    </p>
                </div>
            </div>

            <!-- Top Actions -->
            <div class="flex items-center gap-2">
                <Link :href="contact ? '/contacts/' + contact.uuid : '/contacts'">
                    <Button variant="secondary" size="xs">
                        {{ $t('Cancel') }}
                    </Button>
                </Link>
                <Button variant="primary" size="xs" @click="submitForm" :disabled="form.processing">
                    <span v-if="!form.processing">{{ !props.contact ? $t('Create Contact') : $t('Save Changes') }}</span>
                    <span v-else>{{ $t('Saving...') }}</span>
                </Button>
            </div>
        </div>

        <!-- Form Fields Container -->
        <div class="flex-1 p-4 sm:p-6 lg:p-8 max-w-4xl mx-auto w-full">
            <form @submit.prevent="submitForm" class="space-y-6">
                
                <!-- SECTION 1: Profile Image & Basic Information -->
                <div class="rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 p-5 sm:p-6 shadow-xs space-y-6">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">
                        {{ $t('Basic Information') }}
                    </h3>

                    <!-- Avatar Uploader -->
                    <div class="flex flex-col sm:flex-row items-center gap-5 pb-4 border-b border-slate-100 dark:border-zinc-800/80">
                        <div class="relative shrink-0 group">
                            <Avatar
                                :src="fileUrl || null"
                                :name="(form.first_name || 'C') + ' ' + (form.last_name || '')"
                                size="xl"
                            />
                            <label
                                for="contact-photo-upload"
                                class="absolute inset-0 rounded-2xl bg-black/40 text-white flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer"
                                title="Upload Photo"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mb-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                                <span class="text-[10px] font-bold">{{ $t('Change') }}</span>
                            </label>
                            <input
                                type="file"
                                id="contact-photo-upload"
                                class="sr-only"
                                accept=".jpg, .png, .jpeg"
                                @change="handleFileUpload"
                            />
                        </div>

                        <div class="text-center sm:text-left space-y-1">
                            <label
                                for="contact-photo-upload"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-slate-100 dark:bg-zinc-800 hover:bg-slate-200 dark:hover:bg-zinc-700 text-slate-700 dark:text-zinc-200 cursor-pointer transition-colors"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                <span>{{ $t('Upload Profile Photo') }}</span>
                            </label>
                            <p class="text-[11px] text-slate-400 dark:text-zinc-500">
                                {{ $t('JPG or PNG, max 5MB. Clear face recommended for team recognition.') }}
                            </p>
                        </div>
                    </div>

                    <!-- Input Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <FormInput
                            v-model="form.first_name"
                            :name="$t('First Name')"
                            :error="form.errors.first_name"
                            type="text"
                            required
                        />
                        <FormInput
                            v-model="form.last_name"
                            :name="$t('Last Name')"
                            :error="form.errors.last_name"
                            type="text"
                        />
                        <FormPhoneInput
                            v-model="form.phone"
                            :name="$t('WhatsApp Phone Number')"
                            :error="form.errors.phone"
                            type="text"
                            required
                        />
                        <FormInput
                            v-model="form.email"
                            :name="$t('Email Address')"
                            :error="form.errors.email"
                            type="email"
                        />
                        <div class="sm:col-span-2">
                            <FormSelect
                                v-model="form.group"
                                :name="$t('Contact Group')"
                                :error="form.errors.group"
                                :options="contactGroupOptions()"
                            />
                        </div>
                    </div>
                </div>

                <!-- SECTION 2: Custom CRM Fields (Before Location) -->
                <div
                    v-if="locationSettings === 'before' && fields.length > 0"
                    class="rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 p-5 sm:p-6 shadow-xs space-y-4"
                >
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">
                        {{ $t('Organization Custom Fields') }}
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div v-for="(input, index) in fields" :key="index" :class="input.type !== 'input' ? 'sm:col-span-2' : ''">
                            <FormInput
                                v-if="input.type === 'input'"
                                v-model="form.metadata[input.name]"
                                :name="input.name"
                                :label="$t(input.name)"
                                :type="input.value || 'text'"
                                :required="input.required === 1"
                            />
                            <FormTextArea
                                v-if="input.type === 'textarea'"
                                v-model="form.metadata[input.name]"
                                :name="input.name"
                                :label="$t(input.name)"
                                :required="input.required === 1"
                            />
                            <FormSelect
                                v-if="input.type === 'select'"
                                v-model="form.metadata[input.name]"
                                :name="input.name"
                                :options="transformOptions(input.value)"
                                :required="input.required === 1"
                            />
                            <FormCheckbox
                                v-if="input.type === 'checkbox'"
                                v-model="form.metadata[input.name]"
                                :name="input.name"
                                :label="input.name"
                                :required="input.required === 1"
                            />
                        </div>
                    </div>
                </div>

                <!-- SECTION 3: Address & Location -->
                <div class="rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 p-5 sm:p-6 shadow-xs space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500 flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                        <span>{{ $t('Address & Geography') }}</span>
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-6 gap-4">
                        <div class="sm:col-span-6">
                            <FormInput
                                v-model="form.street"
                                :name="$t('Street Address')"
                                :error="form.errors.street"
                                type="text"
                            />
                        </div>
                        <div class="sm:col-span-3">
                            <FormInput
                                v-model="form.city"
                                :name="$t('City')"
                                :error="form.errors.city"
                                type="text"
                            />
                        </div>
                        <div class="sm:col-span-3">
                            <FormInput
                                v-model="form.state"
                                :name="$t('State / Province')"
                                :error="form.errors.state"
                                type="text"
                            />
                        </div>
                        <div class="sm:col-span-3">
                            <FormInput
                                v-model="form.zip"
                                :name="$t('Postal / Zip Code')"
                                :error="form.errors.zip"
                                type="text"
                            />
                        </div>
                        <div class="sm:col-span-3">
                            <FormInput
                                v-model="form.country"
                                :name="$t('Country')"
                                :error="form.errors.country"
                                type="text"
                            />
                        </div>
                    </div>
                </div>

                <!-- SECTION 4: Custom CRM Fields (After Location) -->
                <div
                    v-if="locationSettings === 'after' && fields.length > 0"
                    class="rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 p-5 sm:p-6 shadow-xs space-y-4"
                >
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">
                        {{ $t('Organization Custom Fields') }}
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div v-for="(input, index) in fields" :key="index" :class="input.type !== 'input' ? 'sm:col-span-2' : ''">
                            <FormInput
                                v-if="input.type === 'input'"
                                v-model="form.metadata[input.name]"
                                :name="input.name"
                                :label="$t(input.name)"
                                :type="input.value || 'text'"
                                :required="input.required === 1"
                            />
                            <FormTextArea
                                v-if="input.type === 'textarea'"
                                v-model="form.metadata[input.name]"
                                :name="input.name"
                                :label="$t(input.name)"
                                :required="input.required === 1"
                            />
                            <FormSelect
                                v-if="input.type === 'select'"
                                v-model="form.metadata[input.name]"
                                :name="input.name"
                                :options="transformOptions(input.value)"
                                :required="input.required === 1"
                            />
                            <FormCheckbox
                                v-if="input.type === 'checkbox'"
                                v-model="form.metadata[input.name]"
                                :name="input.name"
                                :label="input.name"
                                :required="input.required === 1"
                            />
                        </div>
                    </div>
                </div>

                <!-- Bottom Action Toolbar -->
                <div class="flex items-center justify-end gap-3 pt-2 pb-8">
                    <Link :href="contact ? '/contacts/' + contact.uuid : '/contacts'">
                        <Button variant="secondary" size="md">
                            {{ $t('Cancel') }}
                        </Button>
                    </Link>
                    <Button variant="primary" size="md" type="submit" :disabled="form.processing">
                        <span v-if="!form.processing">{{ !props.contact ? $t('Create Contact') : $t('Save Changes') }}</span>
                        <span v-else>{{ $t('Saving...') }}</span>
                    </Button>
                </div>
            </form>
        </div>
    </div>
</template>