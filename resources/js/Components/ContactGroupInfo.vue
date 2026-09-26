<script setup>
import { ref, watchEffect } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import FormModal from '@/Components/FormModal.vue';
import Button from '@/Components/UI/Button.vue';
import Badge from '@/Components/UI/Badge.vue';
import { trans } from 'laravel-vue-i18n';

const props = defineProps({
    group: {
        type: Object,
        required: true,
    },
});

const group = ref(props.group);

watchEffect(() => {
    group.value = props.group;
});

const isOpenFormModal = ref(false);
const form = ref({
    name: group.value.name,
});

const formInputs = [
    {
        inputType: 'FormInput',
        name: 'name',
        label: trans('Group Name'),
        type: 'text',
        className: 'sm:col-span-6',
        required: true,
    },
];

const deleteRow = () => {
    if (confirm(trans('Are you sure you want to delete this contact group? Contacts in this group will not be deleted.'))) {
        router.visit('/contact-groups', {
            method: 'delete',
            data: { uuids: [group.value.uuid] },
            preserveState: true,
        });
    }
};

const openModal = () => {
    form.value.name = group.value.name;
    isOpenFormModal.value = true;
};

const handleCallback = () => {
    isOpenFormModal.value = false;
};
</script>

<template>
    <div class="h-full overflow-y-auto bg-slate-50/70 dark:bg-[#09090B] flex flex-col transition-colors">
        <!-- Sticky Header Bar -->
        <div class="sticky top-0 z-20 px-4 sm:px-6 lg:px-8 py-3.5 glass-header border-b border-slate-200/80 dark:border-zinc-800 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <Link
                    href="/contact-groups"
                    class="md:hidden p-1.5 -ml-1 text-slate-500 hover:text-slate-800 dark:hover:text-zinc-200 rounded-lg hover:bg-slate-100 dark:hover:bg-zinc-800 transition-colors"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
                </Link>
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <span>{{ group.name }}</span>
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-zinc-400">
                        {{ group.contact_count ?? 0 }} {{ $t('total assigned contacts') }}
                    </p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2">
                <Button variant="secondary" size="xs" @click="openModal" class="gap-1.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    <span>{{ $t('Edit') }}</span>
                </Button>
                <Button variant="danger" size="xs" @click="deleteRow" class="gap-1.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                    <span>{{ $t('Delete') }}</span>
                </Button>
            </div>
        </div>

        <!-- Content Area -->
        <div class="flex-1 p-4 sm:p-6 lg:p-8 max-w-3xl mx-auto w-full space-y-6">
            <!-- Group Card -->
            <div class="relative overflow-hidden rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 p-6 shadow-xs transition-colors">
                <div class="absolute top-0 inset-x-0 h-1.5 bg-gradient-to-r from-[#6C5CE7] to-[#00CEC9]"></div>

                <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5 text-center sm:text-left">
                    <div class="w-16 h-16 rounded-2xl bg-purple-100 dark:bg-purple-950/60 text-[#6C5CE7] flex items-center justify-center font-extrabold text-2xl shrink-0 shadow-xs">
                        {{ group.name ? group.name.substring(0, 2).toUpperCase() : 'GP' }}
                    </div>

                    <div class="min-w-0 flex-1 space-y-2">
                        <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white">
                                {{ group.name }}
                            </h1>
                            <Badge variant="primary" size="md">
                                {{ group.contact_count ?? 0 }} {{ $t('Contacts') }}
                            </Badge>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-zinc-400">
                            {{ $t('Use this group for targeted broadcast campaigns, automation workflows, and customer segment tagging.') }}
                        </p>
                    </div>

                    <div class="shrink-0">
                        <Link :href="'/contacts?search=' + encodeURIComponent(group.name)">
                            <Button variant="outline" size="sm" class="gap-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#6C5CE7]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                                <span>{{ $t('View Contacts') }}</span>
                            </Button>
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Group Details Card -->
            <div class="rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 p-5 shadow-xs space-y-4">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">
                    {{ $t('Group Details') }}
                </h3>

                <dl class="divide-y divide-slate-100 dark:divide-zinc-800/60 text-xs sm:text-sm">
                    <div class="py-2.5 flex justify-between gap-4">
                        <dt class="text-slate-500 dark:text-zinc-400">{{ $t('Group Name') }}</dt>
                        <dd class="font-semibold text-slate-800 dark:text-zinc-200 text-right">{{ group.name }}</dd>
                    </div>
                    <div class="py-2.5 flex justify-between gap-4">
                        <dt class="text-slate-500 dark:text-zinc-400">{{ $t('Total Members') }}</dt>
                        <dd class="font-bold text-purple-600 dark:text-purple-400 text-right">{{ group.contact_count ?? 0 }}</dd>
                    </div>
                    <div class="py-2.5 flex justify-between gap-4">
                        <dt class="text-slate-500 dark:text-zinc-400">{{ $t('Created At') }}</dt>
                        <dd class="text-slate-600 dark:text-zinc-400 text-right">{{ group.created_at || '—' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Edit Group Modal -->
        <FormModal 
            v-model="isOpenFormModal" 
            :label="$t('Edit Contact Group')" 
            :url="'/contact-groups/' + group?.uuid" 
            :form="form"
            :formInputs="formInputs"
            @callback="handleCallback"
        />
    </div>
</template>