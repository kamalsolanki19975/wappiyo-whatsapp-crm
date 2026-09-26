<script setup>
    import { ref, computed } from 'vue';
    import AlertModal from '@/Components/AlertModal.vue';
    import { useForm } from "@inertiajs/vue3";
    import { useAlertModal } from '@/Composables/useAlertModal';
    import Dropdown from '@/Components/Dropdown.vue';
    import DropdownItemGroup from '@/Components/DropdownItemGroup.vue';
    import DropdownItem from '@/Components/DropdownItem.vue';
    import Pagination from '@/Components/Pagination.vue';
    import draggable from 'vuedraggable';

    const props = defineProps({
        rows: {
            type: Object,
            required: true,
        },
    });

    const { isOpenAlert, openAlert, confirmAlert } = useAlertModal();
    const emit = defineEmits(['edit', 'delete']);

    const form = useForm({'test': null});
    
    function edit(id) {
        emit('edit', id);
    }

    const deleteAction = (key) => {
        form.delete('/contact-fields/' + key);
    };

    const capitalizeFirstLetter = (string) => {
        if (!string) return '';
        return string.charAt(0).toUpperCase() + string.slice(1);
    };
</script>

<template>
    <div class="space-y-4">
        <div class="overflow-x-auto rounded-xl border border-slate-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-2xs">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-slate-200/80 dark:border-zinc-800 bg-slate-50/75 dark:bg-zinc-800/40 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-zinc-400">
                        <th scope="col" class="py-3.5 pl-4 pr-3 sm:pl-6">{{ $t('Field Name') }}</th>
                        <th scope="col" class="px-3 py-3.5">{{ $t('Type') }}</th>
                        <th scope="col" class="px-3 py-3.5">{{ $t('Required') }}</th>
                        <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6 text-right"><span class="sr-only">{{ $t('Actions') }}</span></th>
                    </tr>
                </thead>
                <draggable 
                    tag="tbody" 
                    :list="rows.data" 
                    handle=".handle" 
                    :clone="false" 
                    item-key="id"
                    class="divide-y divide-slate-100 dark:divide-zinc-800/60"
                >
                    <template #item="{ element }">
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-zinc-800/30 transition-colors group">
                            <!-- Field Name & Drag Handle -->
                            <td class="py-3.5 pl-4 pr-3 sm:pl-6">
                                <div class="flex items-center gap-3">
                                    <div class="handle cursor-grab active:cursor-grabbing text-slate-300 dark:text-zinc-600 hover:text-slate-600 dark:hover:text-zinc-300 transition-colors p-1 rounded">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                            <circle cx="9" cy="6" r="1.5"/><circle cx="15" cy="6" r="1.5"/>
                                            <circle cx="9" cy="12" r="1.5"/><circle cx="15" cy="12" r="1.5"/>
                                            <circle cx="9" cy="18" r="1.5"/><circle cx="15" cy="18" r="1.5"/>
                                        </svg>
                                    </div>
                                    <span class="font-medium text-slate-900 dark:text-zinc-100">{{ element.name }}</span>
                                </div>
                            </td>

                            <!-- Input Type -->
                            <td class="px-3 py-3.5 whitespace-nowrap">
                                <div class="flex items-center gap-1.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-slate-100 dark:bg-zinc-800 text-slate-700 dark:text-zinc-300 border border-slate-200/60 dark:border-zinc-700/60 capitalize">
                                        {{ $t(capitalizeFirstLetter(element.type)) }}
                                    </span>
                                    <span v-if="element.type === 'input' && element.value" class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium bg-indigo-50 dark:bg-indigo-950/50 text-[#6C5CE7] dark:text-indigo-400 border border-indigo-200/50 dark:border-indigo-900/50 capitalize">
                                        {{ $t(capitalizeFirstLetter(element.value)) }}
                                    </span>
                                </div>
                            </td>

                            <!-- Is Required -->
                            <td class="px-3 py-3.5 whitespace-nowrap">
                                <span 
                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium"
                                    :class="element.required !== 0 
                                        ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200/60 dark:border-emerald-800/60' 
                                        : 'bg-slate-100 dark:bg-zinc-800 text-slate-500 dark:text-zinc-400'"
                                >
                                    <span class="w-1.5 h-1.5 rounded-full" :class="element.required !== 0 ? 'bg-emerald-500' : 'bg-slate-400'"></span>
                                    {{ element.required === 0 ? $t('Optional') : $t('Required') }}
                                </span>
                            </td>

                            <!-- Actions -->
                            <td class="py-3.5 pl-3 pr-4 sm:pr-6 text-right whitespace-nowrap">
                                <Dropdown :align="'right'">
                                    <button
                                        type="button"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-zinc-200 hover:bg-slate-100 dark:hover:bg-zinc-800 transition-colors"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                            <circle cx="12" cy="6" r="1.5"/>
                                            <circle cx="12" cy="12" r="1.5"/>
                                            <circle cx="12" cy="18" r="1.5"/>
                                        </svg>
                                    </button>
                                    <template #items>
                                        <DropdownItemGroup>
                                            <DropdownItem as="button" @click="edit(element.uuid)" class="flex items-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>
                                                <span>{{ $t('Edit') }}</span>
                                            </DropdownItem>
                                            <DropdownItem as="button" @click="openAlert(element.uuid)" class="flex items-center gap-2 text-rose-600 dark:text-rose-400">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                                <span>{{ $t('Delete') }}</span>
                                            </DropdownItem>
                                        </DropdownItemGroup>
                                    </template>
                                </Dropdown>
                            </td>
                        </tr>
                    </template>
                </draggable>
            </table>

            <!-- Empty state if no custom fields -->
            <div v-if="!rows.data || rows.data.length === 0" class="py-12 text-center">
                <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-zinc-800 text-slate-400 dark:text-zinc-500 mx-auto flex items-center justify-center mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <line x1="8" y1="6" x2="21" y2="6"></line>
                        <line x1="8" y1="12" x2="21" y2="12"></line>
                        <line x1="8" y1="18" x2="21" y2="18"></line>
                        <line x1="3" y1="6" x2="3.01" y2="6"></line>
                        <line x1="3" y1="12" x2="3.01" y2="12"></line>
                        <line x1="3" y1="18" x2="3.01" y2="18"></line>
                    </svg>
                </div>
                <h4 class="text-sm font-semibold text-slate-800 dark:text-zinc-200">{{ $t('No custom fields defined') }}</h4>
                <p class="text-xs text-slate-500 dark:text-zinc-400 mt-1">{{ $t('Add your first custom field above to enrich customer contact records.') }}</p>
            </div>
        </div>

        <Pagination v-if="rows.meta && rows.meta.total > rows.meta.per_page" class="mt-4" :pagination="rows.meta"/>
    </div>

    <!-- Alert Modal Component-->
    <AlertModal 
        v-model="isOpenAlert" 
        @confirm="() => confirmAlert(deleteAction)"
        :label="$t('Delete Custom Field')" 
        :description="$t('Are you sure you want to delete this custom field? Existing values stored under this field on contacts will be permanently removed.')"
    />
</template>