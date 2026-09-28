<script setup>
    import { ref } from 'vue';
    import debounce from 'lodash/debounce';
    import { router } from '@inertiajs/vue3';
    import { useForm } from "@inertiajs/vue3";
    import AlertModal from '@/Components/AlertModal.vue';
    import { useAlertModal } from '@/Composables/useAlertModal';
    import 'vue3-toastify/dist/index.css';
    import Table from '@/Components/Table.vue';
    import TableHeader from '@/Components/TableHeader.vue';
    import TableHeaderRow from '@/Components/TableHeaderRow.vue';
    import TableHeaderRowItem from '@/Components/TableHeaderRowItem.vue';
    import TableBody from '@/Components/TableBody.vue';
    import TableBodyRow from '@/Components/TableBodyRow.vue';
    import TableBodyRowItem from '@/Components/TableBodyRowItem.vue';
    import Dropdown from '@/Components/Dropdown.vue';
    import DropdownItemGroup from '@/Components/DropdownItemGroup.vue';
    import DropdownItem from '@/Components/DropdownItem.vue';

    const props = defineProps({
        rows: {
            type: Object,
            required: true,
        },
        filters: {
            type: Object
        },
        type: {
            type: String
        },
        showDeleteBtn: {
            type: Boolean,
            default: true,
        },
        showRole: {
            type: Boolean,
            default: false,
        }
    });

    const { isOpenAlert, openAlert, confirmAlert } = useAlertModal();

    const form = useForm({'test': null});

    const deleteAction = (key) => {
        form.delete(props.type === 'admin' ? '/admin/team/users/' + key : '/admin/users/' + key);
    }
    
    const params = ref({
        search: props.filters.search,
    });

    const isSearching = ref(false);
    const emit = defineEmits(['delete']);

    function deleteItem(id) {
        emit('delete', id);
    }

    const clearSearch = () => {
        params.value.search = null;
        runSearch();
    }

    const isLastRow = (index) => {
      return index === props.rows.data.length - 1;
    }

    const search = debounce(() => {
        isSearching.value = true;
        runSearch();
    }, 1000);

    const statusLabel = (status) => {
        if (status === 1) {
            return 'Active';
        } else if (status === 0) {
            return 'Inactive';
        } else {
            return status;
        }
    }

    const runSearch = () => {
        const url = window.location.pathname;

        router.visit(url, {
            method: 'get',
            data: params.value,
        })
    }
</script>
<template>
    <div class="bg-white dark:bg-[#111113] flex items-center border border-slate-200/80 dark:border-zinc-800 shadow-xs h-10 w-full sm:w-80 rounded-xl mb-6 text-sm transition-colors">
        <span class="pl-3 text-slate-400 dark:text-zinc-500">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 15l6 6m-11-4a7 7 0 1 1 0-14a7 7 0 0 1 0 14Z"/></svg>
        </span>
        <input @input="search" v-model="params.search" type="text" class="outline-none px-3 w-full bg-transparent text-slate-900 dark:text-zinc-100 placeholder:text-slate-400 dark:placeholder:text-zinc-500 text-xs sm:text-sm" :placeholder="$t('Search users')">
        <button v-if="isSearching === false && params.search" @click="clearSearch" type="button" class="pr-2 text-slate-400 hover:text-slate-600 dark:hover:text-zinc-300">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="currentColor" d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10s10-4.5 10-10S17.5 2 12 2zm3.7 12.3c.4.4.4 1 0 1.4c-.4.4-1 .4-1.4 0L12 13.4l-2.3 2.3c-.4.4-1 .4-1.4 0c-.4-.4-.4-1 0-1.4l2.3-2.3l-2.3-2.3c-.4-.4-.4-1 0-1.4c.4-.4 1-.4 1.4 0l2.3 2.3l2.3-2.3c.4-.4 1-.4 1.4 0c.4.4.4 1 0 1.4L13.4 12l2.3 2.3z"/></svg>
        </button>
        <span v-if="isSearching" class="pr-2 text-primary">
            <svg class="animate-spin" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
        </span>
    </div>
    <Table :rows="rows">
        <TableHeader>
            <TableHeaderRow>
                <TableHeaderRowItem :position="'first'">{{ $t('Name') }}</TableHeaderRowItem>
                <TableHeaderRowItem class="hidden sm:table-cell">{{ $t('Email') }}</TableHeaderRowItem>
                 <TableHeaderRowItem class="hidden sm:table-cell">{{ $t('Mobile') }}</TableHeaderRowItem>
                <TableHeaderRowItem v-if="type === 'admin' || showRole === true" class="hidden sm:table-cell">{{ $t('Role') }}</TableHeaderRowItem>
                <TableHeaderRowItem>
                    <span class="">{{ $t('Status') }}</span>
                </TableHeaderRowItem>
                <TableHeaderRowItem class="hidden sm:table-cell">
                    <span class="float-right">{{ $t('Last updated') }}</span>
                </TableHeaderRowItem>
                <TableHeaderRowItem :position="'last'"></TableHeaderRowItem>
            </TableHeaderRow>
        </TableHeader>
        <TableBody>
            <TableBodyRow v-for="(item, index) in rows.data" :key="index" :class="!isLastRow(index) ? 'border-b' : ''">
                <TableBodyRowItem :position="'first'" class="capitalize">{{ item.full_name }}</TableBodyRowItem>
                <TableBodyRowItem class="hidden sm:table-cell">{{ item.email }}</TableBodyRowItem>
                <TableBodyRowItem class="hidden sm:table-cell">{{ item.phone }}</TableBodyRowItem>
                <TableBodyRowItem v-if="type === 'admin' || showRole === true" class="hidden sm:table-cell capitalize">{{ item.role }}</TableBodyRowItem>
                <TableBodyRowItem class="capitalize">
                    <span class="py-1 rounded-full text-[11px] font-semibold px-2.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">{{ statusLabel(item.status) }}</span>
                </TableBodyRowItem>
                <TableBodyRowItem class="hidden sm:table-cell text-xs text-slate-400">
                    <span class="float-right">{{ item.updated_at }}</span>
                </TableBodyRowItem>
                <TableBodyRowItem :position="'last'">
                    <Dropdown v-if="item.role != 'admin'" :align="'right'" class="mt-2">
                        <button class="inline-flex w-full justify-center rounded-lg text-sm font-medium text-slate-500 dark:text-zinc-400 hover:text-slate-800 dark:hover:text-zinc-200 focus:outline-none">
                            <span class="hover:bg-slate-100 dark:hover:bg-zinc-800 rounded-lg p-1.5 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                                    <path fill="currentColor" d="M12 16a2 2 0 0 1 2 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2a2 2 0 0 1 2-2m0-6a2 2 0 0 1 2 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2a2 2 0 0 1 2-2m0-6a2 2 0 0 1 2 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2a2 2 0 0 1 2-2Z"/>
                                </svg>
                            </span>
                        </button>
                        <template #items>
                            <DropdownItemGroup>
                                <DropdownItem :href="type === 'admin' ? '/admin/team/users/' + item.id : '/admin/users/' + item.id">{{ $t('View/edit') }}</DropdownItem>
                                <DropdownItem v-if="showDeleteBtn" as="button" @click="openAlert(item.id)">{{ $t('Delete') }}</DropdownItem>
                            </DropdownItemGroup>
                        </template>
                    </Dropdown>
                </TableBodyRowItem>
            </TableBodyRow>
        </TableBody>
    </Table>

    <!-- Alert Modal Component-->
    <AlertModal 
        v-model="isOpenAlert" 
        @confirm="() => confirmAlert(deleteAction)"
        :label = "$t('Delete row')" 
        :description = "$t('Are you sure you want to delete this row? This action can not be undone')"
    />
</template>
  