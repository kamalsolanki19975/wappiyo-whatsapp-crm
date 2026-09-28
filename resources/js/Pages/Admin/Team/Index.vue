<template>
    <AppLayout>
        <div class="p-4 sm:p-6 lg:p-8 max-w-7xl mx-auto space-y-6 text-slate-900 dark:text-zinc-100 h-full md:overflow-y-auto">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-200/80 dark:border-zinc-800 pb-5">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900 dark:text-white mb-1">{{ $t('Users') }}</h1>
                    <p class="flex items-center text-xs sm:text-sm text-slate-500 dark:text-zinc-400">
                        <svg class="w-4 h-4 mr-1.5 shrink-0 text-slate-400 dark:text-zinc-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z"/>
                        </svg>
                        <span>{{ $t('Manage admin accounts') }}</span>
                    </p>
                </div>
                <div>
                    <Link
                        href="/admin/team/users/create"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20 active:scale-[0.98] transition-all cursor-pointer"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14m-7-7h14"/></svg>
                        <span>{{ $t('Add user') }}</span>
                    </Link>
                </div>
            </div>
            <UserTable :rows="props.rows" :filters="props.filters" :type="'admin'"/>
        </div>
    </AppLayout>
</template>
<script setup>
    import AppLayout from "./../Layout/App.vue";
    import { Link } from "@inertiajs/vue3";
    import UserTable from '@/Components/Tables/UserTable.vue';

    const props = defineProps({ title: String, rows: Object, filters: Object });
</script>