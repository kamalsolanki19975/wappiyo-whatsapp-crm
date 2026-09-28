<template>
    <div class="flex min-h-screen w-full items-center justify-center bg-slate-50 dark:bg-[#09090B] p-4 text-slate-900 dark:text-zinc-100 transition-colors">
        <div class="w-full max-w-md bg-white dark:bg-[#111113] rounded-2xl p-6 sm:p-8 border border-slate-200/80 dark:border-zinc-800 shadow-xl">
            <div class="text-center mb-6">
                <div class="w-12 h-12 rounded-xl bg-purple-100 dark:bg-purple-950/60 text-[#6C5CE7] flex items-center justify-center mx-auto mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <h2 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">{{ $t('Select organization') }}</h2>
                <p class="text-xs text-slate-500 dark:text-zinc-400 mt-1">{{ $t('Choose an organization workspace to continue') }}</p>
            </div>

            <div class="space-y-3">
                <div
                    v-for="(item, index) in props.organizations"
                    :key="index"
                    @click="item.organization?.uuid ? selectOrganization(item.organization.uuid) : null"
                    class="flex items-center justify-between p-3.5 rounded-xl border border-slate-200/80 dark:border-zinc-800 bg-slate-50/50 dark:bg-[#18181B] hover:border-[#6C5CE7] hover:bg-purple-50/40 dark:hover:bg-purple-950/20 cursor-pointer transition-all duration-150"
                >
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="w-10 h-10 rounded-xl bg-purple-100 dark:bg-purple-950/80 text-[#6C5CE7] dark:text-purple-300 flex items-center justify-center font-bold text-sm shrink-0">
                            {{ item.organization?.name ? item.organization.name[0].toUpperCase() : 'O' }}
                        </span>
                        <div class="min-w-0">
                            <h3 class="font-semibold text-sm text-slate-900 dark:text-white truncate">{{ item.organization?.name || 'Unnamed Organization' }}</h3>
                            <p class="text-xs text-slate-400 dark:text-zinc-500 capitalize">{{ item.role || 'Member' }}</p>
                        </div>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
    import { useForm } from "@inertiajs/vue3";

    const props = defineProps({ organizations: Object });

    const form = useForm({
        uuid: null,
    })

    const selectOrganization = (uuid) => {
        form.uuid = uuid;

        submitForm();
    }

    const submitForm = async () => {
        form.post('/organization', {
            preserveScroll: true,
        })
    };
</script>