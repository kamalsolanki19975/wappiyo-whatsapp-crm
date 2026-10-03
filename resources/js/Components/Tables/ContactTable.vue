<script setup>
import { computed, ref, watchEffect, onMounted, onUpdated } from 'vue';
import debounce from 'lodash/debounce';
import { Link, router, usePage } from "@inertiajs/vue3";
import ContactImportModal from '@/Components/ContactImportModal.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownItemGroup from '@/Components/DropdownItemGroup.vue';
import DropdownItem from '@/Components/DropdownItem.vue';
import Pagination from '@/Components/Pagination.vue';
import Avatar from '@/Components/UI/Avatar.vue';
import Badge from '@/Components/UI/Badge.vue';
import EmptyState from '@/Components/UI/EmptyState.vue';
import { trans } from 'laravel-vue-i18n';

const props = defineProps({
    rows: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
    type: {
        type: String,
        default: 'contact',
    },
    activeUuid: {
        type: String,
        default: null,
    },
});

const params = ref({
    id: props.filters?.id,
    search: props.filters?.search,
    page: props.filters?.page
});

const isOpenModal = ref(false);
const isSearching = ref(false);
const emit = defineEmits(['callback']);
const bulkCheckbox = ref(false);
const selectedCount = ref(0);
const checkedContacts = ref([]);
const checkedGroups = ref([]);

function getRow(value) {
    params.value.id = value;
    const filteredParams = Object.fromEntries(
        Object.entries(params.value).filter(([_, val]) => val !== null && val !== undefined)
    );
    emit('callback', filteredParams);
}

const clearSearch = () => {
    params.value.search = null;
    runSearch();
};

const search = debounce(() => {
    params.value.page = null;
    isSearching.value = true;
    runSearch();
}, 600);

const runSearch = () => {
    const filteredParams = Object.fromEntries(
        Object.entries(params.value).filter(([_, val]) => val !== null && val !== undefined)
    );

    router.visit(props.type === 'contact' ? '/contacts' : '/contact-groups', {
        method: 'get',
        data: filteredParams,
        preserveState: true,
        onFinish: () => {
            isSearching.value = false;
        }
    });
};

function saveCheckedItems() {
    if (props.type === 'contact') {
        localStorage.setItem('checkedContacts', JSON.stringify(checkedContacts.value));
    } else {
        localStorage.setItem('checkedGroups', JSON.stringify(checkedGroups.value));
    }
}

function loadCheckedItems() {
    if (props.type === 'contact') {
        const saved = localStorage.getItem('checkedContacts');
        checkedContacts.value = saved ? JSON.parse(saved) : [];
    } else {
        const saved = localStorage.getItem('checkedGroups');
        checkedGroups.value = saved ? JSON.parse(saved) : [];
    }
}

function updateCheckedItems(uuid, isChecked) {
    if (props.type === 'contact') {
        const index = checkedContacts.value.indexOf(uuid);
        if (isChecked && index === -1) {
            checkedContacts.value.push(uuid);
        } else if (!isChecked && index !== -1) {
            checkedContacts.value.splice(index, 1);
        }
    } else {
        const index = checkedGroups.value.indexOf(uuid);
        if (isChecked && index === -1) {
            checkedGroups.value.push(uuid);
        } else if (!isChecked && index !== -1) {
            checkedGroups.value.splice(index, 1);
        }
    }
    saveCheckedItems();
}

function toggleCheckbox(uuid) {
    const item = props.rows?.data?.find(r => r.uuid === uuid);
    if (!item) return;
    item.isChecked = !item.isChecked;
    updateCheckedItems(uuid, item.isChecked);
    updateBulkCheckboxState();
    updateSelectedCount();
}

function toggleAllCheckboxes() {
    bulkCheckbox.value = !bulkCheckbox.value;
    if (props.rows?.data) {
        props.rows.data.forEach(row => {
            row.isChecked = bulkCheckbox.value;
            updateCheckedItems(row.uuid, bulkCheckbox.value);
        });
    }
    updateSelectedCount();
}

function applyCheckedState() {
    if (!props.rows?.data) return;
    props.rows.data.forEach(row => {
        row.isChecked = props.type === 'contact'
            ? checkedContacts.value.includes(row.uuid)
            : checkedGroups.value.includes(row.uuid);
    });
    updateBulkCheckboxState();
    updateSelectedCount();
}

function updateBulkCheckboxState() {
    bulkCheckbox.value = !!props.rows?.data?.length && props.rows.data.every(row => row.isChecked);
}

function updateSelectedCount() {
    selectedCount.value = props.type === 'contact' ? checkedContacts.value.length : checkedGroups.value.length;
}

function deleteItems(value) {
    const itemsToDelete = props.type === 'contact' ? checkedContacts.value : checkedGroups.value;
    const isAll = value === 'all';

    if (!isAll && itemsToDelete.length === 0) return;

    if (!confirm(isAll ? trans('Are you sure you want to delete ALL records?') : trans('Are you sure you want to delete selected records?'))) {
        return;
    }

    router.visit(props.type === 'contact' ? '/contacts' : '/contact-groups', {
        method: 'delete',
        data: { 'uuids': isAll ? [] : itemsToDelete, 'all': isAll },
        preserveState: true,
        onSuccess: () => {
            localStorage.removeItem(props.type === 'contact' ? 'checkedContacts' : 'checkedGroups');
            if (props.type === 'contact') {
                checkedContacts.value = [];
            } else {
                checkedGroups.value = [];
            }
            updateSelectedCount();
        }
    });
}

onMounted(() => {
    loadCheckedItems();
    applyCheckedState();
});

onUpdated(() => {
    applyCheckedState();
});

watchEffect(() => {
    params.value.page = props.filters?.page;
    applyCheckedState();
});
</script>

<template>
    <div class="flex flex-col h-full bg-white dark:bg-[#111113] transition-colors">
        <!-- Search and Filter Bar -->
        <div class="p-3.5 sm:p-4 border-b border-slate-100 dark:border-zinc-800/80 space-y-3 shrink-0">
            <!-- Search Input -->
            <div class="relative flex items-center">
                <div class="pointer-events-none absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 dark:text-zinc-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                    </svg>
                </div>

                <input
                    v-model="params.search"
                    @input="search"
                    type="text"
                    :placeholder="type === 'contact' ? $t('Search name, phone, email...') : $t('Search groups...')"
                    class="w-full pl-9 pr-8 py-2 text-xs sm:text-sm bg-slate-50 dark:bg-zinc-900/80 text-slate-800 dark:text-zinc-200 border border-slate-200 dark:border-zinc-800 rounded-xl focus:outline-none focus:border-[#6C5CE7] focus:ring-1 focus:ring-[#6C5CE7] placeholder-slate-400 dark:placeholder-zinc-500 transition-colors"
                />

                <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center">
                    <button
                        v-if="params.search && !isSearching"
                        type="button"
                        @click="clearSearch"
                        class="text-slate-400 hover:text-slate-600 dark:hover:text-zinc-200 transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                    <svg v-if="isSearching" class="animate-spin h-3.5 w-3.5 text-[#6C5CE7]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                </div>
            </div>

            <!-- Segmented Switcher: All Contacts vs Groups -->
            <div class="flex items-center p-0.5 rounded-xl bg-slate-100 dark:bg-zinc-800/80 text-xs font-semibold">
                <Link
                    href="/contacts"
                    :class="[
                        'flex-1 text-center py-1.5 rounded-lg transition-all duration-150',
                        $page.url.startsWith('/contacts')
                            ? 'bg-white dark:bg-zinc-900 text-slate-900 dark:text-white shadow-xs font-bold'
                            : 'text-slate-500 dark:text-zinc-400 hover:text-slate-700 dark:hover:text-zinc-200'
                    ]"
                >
                    {{ $t('All Contacts') }}
                </Link>
                <Link
                    href="/contact-groups"
                    :class="[
                        'flex-1 text-center py-1.5 rounded-lg transition-all duration-150',
                        $page.url.startsWith('/contact-groups')
                            ? 'bg-white dark:bg-zinc-900 text-slate-900 dark:text-white shadow-xs font-bold'
                            : 'text-slate-500 dark:text-zinc-400 hover:text-slate-700 dark:hover:text-zinc-200'
                    ]"
                >
                    {{ $t('Groups') }}
                </Link>
            </div>
        </div>

        <!-- Selection Toolbar -->
        <div class="flex items-center justify-between px-3.5 sm:px-4 py-2 border-b border-slate-100 dark:border-zinc-800/80 bg-slate-50/50 dark:bg-zinc-900/30 text-xs shrink-0">
            <div class="flex items-center gap-2">
                <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                    <input
                        type="checkbox"
                        :checked="bulkCheckbox"
                        @change="toggleAllCheckboxes"
                        class="rounded border-slate-300 dark:border-zinc-700 text-[#6C5CE7] focus:ring-[#6C5CE7] dark:bg-zinc-900 cursor-pointer h-3.5 w-3.5"
                    />
                    <span class="text-slate-600 dark:text-zinc-400 font-medium">
                        <template v-if="selectedCount === 0">{{ $t('Select all') }}</template>
                        <template v-else><strong class="text-purple-600 dark:text-purple-400 font-bold">{{ selectedCount }}</strong> {{ $t('selected') }}</template>
                    </span>
                </label>
            </div>

            <!-- Actions Dropdown -->
            <Dropdown align="right" width="w-48">
                <button
                    type="button"
                    class="p-1 rounded-lg text-slate-500 dark:text-zinc-400 hover:text-slate-800 dark:hover:text-zinc-200 hover:bg-slate-100 dark:hover:bg-zinc-800 transition-colors"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="1"/><circle cx="12" cy="5" r="1"/><circle cx="12" cy="19" r="1"/></svg>
                </button>
                <template #items>
                    <DropdownItemGroup>
                        <DropdownItem as="button" @click="isOpenModal = true">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                            <span>{{ $t('Import from Excel') }}</span>
                        </DropdownItem>
                        <DropdownItem as="a" :href="type === 'contact' ? '/contacts/export' : '/contact-groups/export'">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            <span>{{ $t('Export to Excel') }}</span>
                        </DropdownItem>
                        <DropdownItem v-if="selectedCount > 0" as="button" @click="deleteItems()" class="text-rose-600 dark:text-rose-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-rose-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                            <span>{{ $t('Delete selected') }} ({{ selectedCount }})</span>
                        </DropdownItem>
                        <DropdownItem as="button" @click="deleteItems('all')" class="text-rose-600 dark:text-rose-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-rose-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                            <span>{{ $t('Delete all') }}</span>
                        </DropdownItem>
                    </DropdownItemGroup>
                </template>
            </Dropdown>
        </div>

        <!-- List Items Container -->
        <div class="flex-1 overflow-y-auto divide-y divide-slate-100/80 dark:divide-zinc-800/60" ref="scrollContainer">
            <!-- Empty State -->
            <div v-if="!rows.data || rows.data.length === 0" class="p-6">
                <EmptyState
                    :title="type === 'contact' ? $t('No Contacts Found') : $t('No Groups Found')"
                    :description="type === 'contact' ? $t('No contacts match your query.') : $t('No groups have been created yet.')"
                />
            </div>

            <!-- Contact Rows -->
            <template v-if="type === 'contact'">
                <div
                    v-for="(contact, index) in rows.data"
                    :key="index"
                    @click="getRow(contact.uuid)"
                    :class="[
                        'flex items-center gap-3 px-3.5 py-3 transition-all duration-150 cursor-pointer group',
                        (activeUuid === contact.uuid || params.id === contact.uuid)
                            ? 'bg-purple-50/70 dark:bg-purple-950/30 border-l-4 border-[#6C5CE7]'
                            : contact.isChecked
                                ? 'bg-slate-50 dark:bg-zinc-800/60'
                                : 'hover:bg-slate-50/80 dark:hover:bg-zinc-800/40'
                    ]"
                >
                    <!-- Checkbox -->
                    <div class="shrink-0" @click.stop="toggleCheckbox(contact.uuid)">
                        <input
                            type="checkbox"
                            :checked="contact.isChecked"
                            class="rounded border-slate-300 dark:border-zinc-700 text-[#6C5CE7] focus:ring-[#6C5CE7] dark:bg-zinc-900 cursor-pointer h-3.5 w-3.5"
                        />
                    </div>

                    <!-- Avatar -->
                    <div class="relative shrink-0">
                        <Avatar
                            :src="contact.avatar || null"
                            :name="contact.full_name || 'Contact'"
                            size="md"
                        />
                        <span
                            v-if="contact.unread_messages > 0"
                            class="absolute -top-0.5 -right-0.5 h-3 w-3 rounded-full bg-emerald-500 ring-2 ring-white dark:ring-[#111113]"
                        />
                    </div>

                    <!-- Name and Phone -->
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center justify-between gap-1 mb-0.5">
                            <h3 class="text-xs sm:text-sm font-semibold text-slate-800 dark:text-zinc-200 truncate group-hover:text-[#6C5CE7] transition-colors">
                                {{ contact.full_name }}
                            </h3>
                            <!-- Favorite Star Indicator -->
                            <span v-if="contact.is_favorite" class="text-amber-400 shrink-0" title="Starred Favorite">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                            </span>
                        </div>
                        <div class="flex items-center gap-1.5 text-[11px] text-slate-500 dark:text-zinc-400 truncate">
                            <span>{{ contact.formatted_phone_number }}</span>
                            <template v-if="contact.contact_group?.name">
                                <span>•</span>
                                <span class="text-purple-600 dark:text-purple-400 font-medium truncate max-w-[120px]">
                                    {{ contact.contact_group.name }}
                                </span>
                            </template>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Group Rows -->
            <template v-else-if="type === 'group'">
                <div
                    v-for="(row, key) in rows.data"
                    :key="key"
                    @click="getRow(row.uuid)"
                    :class="[
                        'flex items-center gap-3 px-3.5 py-3 transition-all duration-150 cursor-pointer group',
                        (activeUuid === row.uuid || params.id === row.uuid)
                            ? 'bg-purple-50/70 dark:bg-purple-950/30 border-l-4 border-[#6C5CE7]'
                            : row.isChecked
                                ? 'bg-slate-50 dark:bg-zinc-800/60'
                                : 'hover:bg-slate-50/80 dark:hover:bg-zinc-800/40'
                    ]"
                >
                    <!-- Checkbox -->
                    <div class="shrink-0" @click.stop="toggleCheckbox(row.uuid)">
                        <input
                            type="checkbox"
                            :checked="row.isChecked"
                            class="rounded border-slate-300 dark:border-zinc-700 text-[#6C5CE7] focus:ring-[#6C5CE7] dark:bg-zinc-900 cursor-pointer h-3.5 w-3.5"
                        />
                    </div>

                    <!-- Group Icon Avatar -->
                    <div class="w-10 h-10 rounded-xl bg-purple-100 dark:bg-purple-950/60 text-[#6C5CE7] flex items-center justify-center font-bold text-sm shrink-0">
                        {{ row.name ? row.name.substring(0, 2).toUpperCase() : 'GP' }}
                    </div>

                    <!-- Group Details -->
                    <div class="min-w-0 flex-1">
                        <h3 class="text-xs sm:text-sm font-semibold text-slate-800 dark:text-zinc-200 truncate group-hover:text-[#6C5CE7] transition-colors">
                            {{ row.name }}
                        </h3>
                        <p class="text-[11px] text-slate-400 dark:text-zinc-500 mt-0.5">
                            {{ row.contact_count ?? 0 }} {{ $t('contacts') }}
                        </p>
                    </div>
                </div>
            </template>
        </div>

        <!-- Pagination -->
        <div class="p-3 border-t border-slate-100 dark:border-zinc-800/80 shrink-0">
            <Pagination :pagination="rows.meta || rows"/>
        </div>

        <!-- Import Modal -->
        <ContactImportModal :type="type" v-model:modelValue="isOpenModal" />
    </div>
</template>