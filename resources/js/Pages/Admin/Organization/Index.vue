<template>
    <AppLayout>
        <div class="pt-6 px-4 md:p-8 text-slate-900 dark:text-zinc-100 h-full md:overflow-y-auto">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">{{ $t('Organizations') }}</h1>
                    <p class="flex items-center gap-1.5 text-sm text-slate-500 dark:text-zinc-400 mt-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                        <span>{{ $t('Manage organization accounts') }}</span>
                    </p>
                </div>
                <div>
                    <Link href="/admin/organizations/create" class="inline-flex items-center gap-2 rounded-xl bg-[#6C5CE7] hover:bg-[#5B46D6] px-4 py-2 text-sm font-semibold text-white shadow-sm shadow-purple-600/30 transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        <span>{{ $t('Add organization') }}</span>
                    </Link>
                </div>
            </div>
            <OrganizationTable :rows="props.rows" :filters="props.filters" :type="'user'"/>
        </div>
    </AppLayout>
</template>
<script setup>
    import AppLayout from "./../Layout/App.vue";
    import { Link } from "@inertiajs/vue3";
    import { ref } from 'vue';
    import OrganizationTable from '@/Components/Tables/OrganizationTable.vue';

    const props = defineProps({ 
        allowCreate: {
            type: Boolean,
            default: true
        }, 
        title: String, 
        rows: Object, 
        filters: Object
    });
</script>