<template>
    <div class="rounded-3xl border border-slate-200/80 dark:border-white/10 bg-white dark:bg-slate-900 shadow-sm overflow-hidden">
        <!-- Desktop Table View -->
        <div class="overflow-x-auto">
            <table v-if="rows?.data?.length > 0" class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-white/5 bg-slate-50/60 dark:bg-white/[0.02] text-slate-400 font-bold uppercase tracking-wider text-[10px]">
                        <th class="py-3 px-4">{{ $t('Member') }}</th>
                        <th class="py-3 px-4">{{ $t('Email') }}</th>
                        <th class="py-3 px-4">{{ $t('Role') }}</th>
                        <th class="py-3 px-4">{{ $t('Status') }}</th>
                        <th class="py-3 px-4 text-right">{{ $t('Last Active') }}</th>
                        <th v-if="isOwner" class="py-3 px-4 text-right">{{ $t('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                    <tr
                        v-for="item in rows.data"
                        :key="item.uuid"
                        class="hover:bg-slate-50/80 dark:hover:bg-white/5 transition group"
                    >
                        <!-- Member Name & Avatar -->
                        <td class="py-3.5 px-4">
                            <div class="flex items-center gap-3">
                                <div class="relative w-8 h-8 rounded-xl bg-gradient-to-tr from-primary to-violet-500 text-white font-bold text-xs flex items-center justify-center shrink-0 shadow-xs">
                                    {{ getInitials(item.user?.first_name, item.user?.last_name) }}
                                    <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 rounded-full bg-emerald-500 border-2 border-white dark:border-slate-900"></span>
                                </div>
                                <div class="min-w-0">
                                    <div class="font-bold text-slate-900 dark:text-white capitalize truncate group-hover:text-primary transition-colors">
                                        {{ item.user ? (item.user.first_name + ' ' + item.user.last_name) : 'User' }}
                                    </div>
                                    <div class="text-[10px] text-slate-400 sm:hidden truncate">
                                        {{ item.user?.email }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Email -->
                        <td class="py-3.5 px-4 text-slate-600 dark:text-slate-300 font-medium whitespace-nowrap">
                            {{ item.user?.email }}
                        </td>

                        <!-- Role Badge -->
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <span
                                class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider"
                                :class="getRoleBadgeClass(item.role)"
                            >
                                <span class="w-1.5 h-1.5 rounded-full" :class="getRoleDotClass(item.role)"></span>
                                {{ item.role }}
                            </span>
                        </td>

                        <!-- Status -->
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                {{ $t(item.status || 'Active') }}
                            </span>
                        </td>

                        <!-- Last Updated -->
                        <td class="py-3.5 px-4 text-right text-slate-500 dark:text-slate-400 whitespace-nowrap text-[11px]">
                            {{ item.updated_at }}
                        </td>

                        <!-- Actions Dropdown -->
                        <td v-if="isOwner" class="py-3.5 px-4 text-right">
                            <div v-if="item.role !== 'owner'" class="inline-flex items-center gap-1">
                                <button
                                    type="button"
                                    @click="edit(item.uuid, item.role, item.user?.email)"
                                    class="p-1.5 rounded-lg text-slate-400 hover:text-primary hover:bg-primary/10 transition"
                                    :title="$t('Change Role')"
                                >
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>

                                <button
                                    type="button"
                                    @click="openAlert(item.uuid)"
                                    class="p-1.5 rounded-lg text-slate-400 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-500/10 transition"
                                    :title="$t('Remove Member')"
                                >
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                            <span v-else class="text-[10px] font-bold uppercase tracking-wider text-slate-400 px-2 py-0.5 rounded bg-slate-100 dark:bg-white/5">
                                {{ $t('Primary Admin') }}
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Empty State -->
            <div
                v-else
                class="py-16 flex flex-col items-center justify-center text-center p-6"
            >
                <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-white/5 text-slate-400 flex items-center justify-center mb-3">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <h4 class="text-sm font-bold text-slate-900 dark:text-white mb-1">
                    {{ $t('No team members found') }}
                </h4>
                <p class="text-xs text-slate-400 max-w-sm">
                    {{ $t('Try searching with a different name or filter by a different role.') }}
                </p>
            </div>
        </div>

        <!-- Pagination -->
        <div
            v-if="rows?.links?.length > 3"
            class="px-4 py-3 border-t border-slate-100 dark:border-white/5 flex items-center justify-between gap-4 text-xs"
        >
            <div class="text-slate-500 dark:text-slate-400">
                {{ $t('Showing') }}
                <span class="font-bold text-slate-900 dark:text-white">{{ rows.meta?.from || 1 }}</span>
                {{ $t('to') }}
                <span class="font-bold text-slate-900 dark:text-white">{{ rows.meta?.to || rows.data.length }}</span>
                {{ $t('of') }}
                <span class="font-bold text-slate-900 dark:text-white">{{ rows.meta?.total || rows.data.length }}</span>
                {{ $t('members') }}
            </div>

            <div class="flex items-center gap-1">
                <template v-for="(link, i) in rows.links" :key="i">
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        v-html="link.label"
                        class="px-2.5 py-1 rounded-lg text-xs font-semibold transition"
                        :class="link.active ? 'bg-primary text-white' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-white/5'"
                    />
                    <span
                        v-else
                        v-html="link.label"
                        class="px-2.5 py-1 text-slate-300 dark:text-slate-600 cursor-not-allowed text-xs"
                    />
                </template>
            </div>
        </div>
    </div>

    <!-- Alert Modal for Delete Confirmation -->
    <AlertModal
        v-model="isOpenAlert"
        @confirm="() => confirmAlert(deleteAction)"
        :label="$t('Remove Team Member')"
        :description="$t('Are you sure you want to remove this member from your organization? They will immediately lose access to all WhatsApp conversations and tools.')"
    />
</template>

<script setup>
import { computed } from 'vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import AlertModal from '@/Components/AlertModal.vue';
import { useAlertModal } from '@/Composables/useAlertModal';

const props = defineProps({
    rows: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(['edit']);

const user = computed(() => usePage().props.auth?.user);
const isOwner = computed(() => user.value?.teams?.[0]?.role === 'owner');

const { isOpenAlert, openAlert, confirmAlert } = useAlertModal();
const form = useForm({});

function deleteAction(key) {
    form.delete('/team/' + key);
}

function edit(id, role, email) {
    emit('edit', { id, role, email });
}

function getInitials(first, last) {
    const f = first ? first[0] : '';
    const l = last ? last[0] : '';
    return (f + l).toUpperCase() || 'U';
}

function getRoleBadgeClass(role) {
    switch (role?.toLowerCase()) {
        case 'owner':
            return 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20';
        case 'manager':
            return 'bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 border border-cyan-500/20';
        case 'agent':
        default:
            return 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20';
    }
}

function getRoleDotClass(role) {
    switch (role?.toLowerCase()) {
        case 'owner':
            return 'bg-purple-500';
        case 'manager':
            return 'bg-cyan-500';
        case 'agent':
        default:
            return 'bg-emerald-500';
    }
}
</script>