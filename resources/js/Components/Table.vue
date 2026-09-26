<script setup>
    import Pagination from '@/Components/Pagination.vue';

    const props = defineProps({
        rows: Object,
        className: String,
    })
</script>
<template>
    <div class="w-full bg-white dark:bg-[#111113] rounded-2xl border border-slate-200/80 dark:border-zinc-800/80 shadow-card overflow-hidden" :class="className">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <slot />
            </table>
        </div>
        
        <div v-if="rows && rows.meta" class="px-4 py-2 border-t border-slate-100 dark:border-zinc-800 bg-slate-50/50 dark:bg-zinc-900/30">
            <Pagination :pagination="rows.meta"/>
        </div>
    </div>
</template>