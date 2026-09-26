<script setup>
import { ref, watchEffect, computed } from 'vue';
import { Link, router, useForm, usePage } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownItemGroup from '@/Components/DropdownItemGroup.vue';
import DropdownItem from '@/Components/DropdownItem.vue';
import FormTextArea from '@/Components/FormTextArea.vue';
import Modal from '@/Components/Modal.vue';
import Avatar from '@/Components/UI/Avatar.vue';
import Badge from '@/Components/UI/Badge.vue';
import Button from '@/Components/UI/Button.vue';
import { trans } from 'laravel-vue-i18n';

const props = defineProps({
    contact: {
        type: Object,
        required: true,
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

const emit = defineEmits(['close']);

const contact = ref(props.contact);
const isOpenModal = ref(false);
const isLoading = ref(false);

watchEffect(() => {
    contact.value = props.contact;
});

const metadata = computed(() => {
    try {
        if (!props.contact?.metadata) return {};
        return typeof props.contact.metadata === 'string'
            ? JSON.parse(props.contact.metadata)
            : props.contact.metadata;
    } catch (_) {
        return {};
    }
});

const favorite = async () => {
    contact.value.is_favorite = !contact.value.is_favorite;
    router.put('/contacts/favorite/' + contact.value.uuid, {
        favorite: contact.value.is_favorite
    }, {
        preserveState: true,
    });
};

const form = useForm({
    notes: null,
    contact: null,
});

const deleteRow = () => {
    if (confirm(trans('Are you sure you want to delete this contact?'))) {
        form.delete('/contacts/' + contact.value.uuid);
    }
};

const getAddressDetail = (value, key) => {
    if (!value) return null;
    try {
        const address = typeof value === 'string' ? JSON.parse(value) : value;
        return address?.[key] && address?.[key] !== 'Not Set' ? address[key] : null;
    } catch (_) {
        return null;
    }
};

const submitForm = () => {
    form.contact = contact.value.uuid;
    isLoading.value = true;

    form.post('/notes', {
        onSuccess: () => {
            form.reset();
            isOpenModal.value = false;
            contact.value = usePage().props.flash?.status?.contact || contact.value;
        },
        onFinish: () => {
            isLoading.value = false;
        }
    });
};
</script>

<template>
    <div class="h-full overflow-y-auto bg-white dark:bg-[#111113] border-l border-slate-200/80 dark:border-zinc-800 flex flex-col transition-colors">
        <!-- CRM Header Panel -->
        <div class="p-4 sm:p-5 border-b border-slate-100 dark:border-zinc-800/80 shrink-0">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">
                    {{ $t('Contact CRM') }}
                </span>
                <button
                    type="button"
                    @click="emit('close')"
                    class="p-1 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-zinc-200 hover:bg-slate-100 dark:hover:bg-zinc-800 transition-colors"
                    title="Close CRM Panel"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            <!-- Profile Info Card -->
            <div class="flex flex-col items-center text-center">
                <div class="relative mb-3">
                    <Avatar
                        :src="contact.avatar || null"
                        :name="contact.full_name || 'Contact'"
                        size="xl"
                    />
                    <span class="absolute bottom-0 right-0 h-4 w-4 rounded-full bg-emerald-500 ring-2 ring-white dark:ring-[#111113]" />
                </div>

                <h3 class="text-base font-bold text-slate-900 dark:text-white">
                    {{ contact.first_name }} {{ contact.last_name }}
                </h3>

                <p class="text-xs text-slate-500 dark:text-zinc-400 mt-0.5 font-mono">
                    {{ contact.formatted_phone_number }}
                </p>

                <!-- Group / Tag Pill -->
                <div class="mt-2 flex flex-wrap justify-center gap-1.5">
                    <Badge v-if="contact.contact_group?.name" variant="primary" size="sm">
                        {{ contact.contact_group.name }}
                    </Badge>
                    <Badge v-else variant="neutral" size="sm">
                        {{ $t('General Lead') }}
                    </Badge>
                </div>

                <!-- Action Toolbar -->
                <div class="flex items-center gap-2 mt-4 w-full">
                    <Link
                        :href="'/contacts/' + contact.uuid + '?edit=true'"
                        class="flex-1"
                    >
                        <Button variant="secondary" size="xs" class="w-full justify-center">
                            <template #icon>
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </template>
                            <span>{{ $t('Edit') }}</span>
                        </Button>
                    </Link>

                    <button
                        type="button"
                        @click="favorite"
                        :class="[
                            'p-2 rounded-xl border transition-colors',
                            contact.is_favorite
                                ? 'bg-amber-50 dark:bg-amber-950/40 border-amber-300 dark:border-amber-700 text-amber-500'
                                : 'bg-slate-50 dark:bg-zinc-800 border-slate-200 dark:border-zinc-700 text-slate-400 hover:text-slate-600'
                        ]"
                        :title="contact.is_favorite ? 'Favorited' : 'Add to favorites'"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" :fill="contact.is_favorite ? 'currentColor' : 'none'" stroke="currentColor" stroke-width="2">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                        </svg>
                    </button>

                    <Dropdown align="right" width="w-40">
                        <button
                            type="button"
                            class="p-2 rounded-xl border border-slate-200 dark:border-zinc-700 bg-slate-50 dark:bg-zinc-800 text-slate-500 hover:text-slate-800 dark:hover:text-zinc-200 transition-colors"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="1"/><circle cx="12" cy="5" r="1"/><circle cx="12" cy="19" r="1"/></svg>
                        </button>
                        <template #items>
                            <DropdownItemGroup>
                                <DropdownItem as="button" @click="isOpenModal = true">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    <span>{{ $t('Add Note') }}</span>
                                </DropdownItem>
                                <DropdownItem as="button" @click="deleteRow" class="text-rose-600 dark:text-rose-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-rose-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                    <span>{{ $t('Delete Contact') }}</span>
                                </DropdownItem>
                            </DropdownItemGroup>
                        </template>
                    </Dropdown>
                </div>
            </div>
        </div>

        <!-- CRM Body Details -->
        <div class="flex-1 p-4 sm:p-5 space-y-6">
            <!-- 1. Contact Information -->
            <div class="space-y-3">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">
                    {{ $t('Contact Information') }}
                </h4>

                <div class="rounded-xl border border-slate-100 dark:border-zinc-800 bg-slate-50/60 dark:bg-zinc-900/40 p-3 space-y-2.5 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 dark:text-zinc-400">{{ $t('Email') }}</span>
                        <span class="font-medium text-slate-800 dark:text-zinc-200 truncate max-w-[160px]">
                            {{ contact.email || '—' }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 dark:text-zinc-400">{{ $t('Phone') }}</span>
                        <span class="font-medium text-slate-800 dark:text-zinc-200 font-mono">
                            {{ contact.formatted_phone_number }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 dark:text-zinc-400">{{ $t('Group') }}</span>
                        <span class="font-medium text-slate-800 dark:text-zinc-200">
                            {{ contact.contact_group?.name || 'Default' }}
                        </span>
                    </div>

                    <!-- Custom Fields (Location before) -->
                    <template v-if="fields && fields.length > 0">
                        <div
                            v-for="(field, idx) in fields"
                            :key="idx"
                            class="flex items-center justify-between border-t border-slate-100 dark:border-zinc-800/80 pt-2"
                        >
                            <span class="text-slate-500 dark:text-zinc-400 capitalize">{{ field.name }}</span>
                            <span class="font-medium text-slate-800 dark:text-zinc-200 truncate max-w-[160px]">
                                {{ metadata[field.name] || '—' }}
                            </span>
                        </div>
                    </template>
                </div>
            </div>

            <!-- 2. Address Details if set -->
            <div v-if="contact.address" class="space-y-3">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">
                    {{ $t('Address') }}
                </h4>

                <div class="rounded-xl border border-slate-100 dark:border-zinc-800 bg-slate-50/60 dark:bg-zinc-900/40 p-3 space-y-2 text-xs">
                    <div v-if="getAddressDetail(contact.address, 'street')" class="flex justify-between">
                        <span class="text-slate-500">{{ $t('Street') }}</span>
                        <span class="font-medium text-slate-800 dark:text-zinc-200">{{ getAddressDetail(contact.address, 'street') }}</span>
                    </div>
                    <div v-if="getAddressDetail(contact.address, 'city')" class="flex justify-between">
                        <span class="text-slate-500">{{ $t('City') }}</span>
                        <span class="font-medium text-slate-800 dark:text-zinc-200">{{ getAddressDetail(contact.address, 'city') }}</span>
                    </div>
                    <div v-if="getAddressDetail(contact.address, 'country')" class="flex justify-between">
                        <span class="text-slate-500">{{ $t('Country') }}</span>
                        <span class="font-medium text-slate-800 dark:text-zinc-200">{{ getAddressDetail(contact.address, 'country') }}</span>
                    </div>
                </div>
            </div>

            <!-- 3. Team Notes -->
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">
                        {{ $t('Internal Notes') }}
                    </h4>
                    <button
                        type="button"
                        @click="isOpenModal = true"
                        class="inline-flex items-center gap-1 text-[11px] font-semibold text-[#6C5CE7] dark:text-purple-400 hover:underline"
                    >
                        <span>+ {{ $t('Add Note') }}</span>
                    </button>
                </div>

                <div v-if="!contact.notes || contact.notes.length === 0" class="p-3 text-center rounded-xl bg-slate-50/50 dark:bg-zinc-900/40 border border-slate-100 dark:border-zinc-800 text-slate-400 dark:text-zinc-500 text-xs">
                    {{ $t('No internal notes recorded.') }}
                </div>

                <div v-else class="space-y-2 max-h-60 overflow-y-auto">
                    <div
                        v-for="(note, nIdx) in contact.notes"
                        :key="nIdx"
                        class="p-2.5 rounded-xl border border-amber-200/60 dark:border-amber-900/40 bg-amber-50/50 dark:bg-amber-950/20 text-xs space-y-1"
                    >
                        <p class="text-slate-800 dark:text-zinc-200 whitespace-pre-wrap leading-relaxed">
                            {{ note.content }}
                        </p>
                        <span class="text-[10px] text-slate-400 dark:text-zinc-500 block">
                            {{ note.created_at }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Note Modal -->
    <Modal :label="$t('Add Internal Note')" :isOpen="isOpenModal" @close="isOpenModal = false">
        <form @submit.prevent="submitForm" class="space-y-4 mt-3">
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-zinc-300 mb-1.5">
                    {{ $t('Note Content') }}
                </label>
                <FormTextArea
                    v-model="form.notes"
                    :error="form.errors?.note"
                    :placeholder="$t('Add notes for your team regarding this contact...')"
                    class="w-full"
                />
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <Button type="button" variant="secondary" size="sm" @click="isOpenModal = false">
                    {{ $t('Cancel') }}
                </Button>
                <Button type="submit" variant="primary" size="sm" :loading="isLoading">
                    {{ $t('Save Note') }}
                </Button>
            </div>
        </form>
    </Modal>
</template>