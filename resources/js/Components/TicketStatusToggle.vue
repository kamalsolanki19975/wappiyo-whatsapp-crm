<script setup>
    import { ref, onMounted, onUnmounted } from 'vue';
    import { Link } from "@inertiajs/vue3";

    const props = defineProps({
        languages: Object,
        currentLanguage: String,
        status: String,
        rowCount: Number,
    })

    const isOpen = ref(false);

    const toggleDropdown = () => {
        isOpen.value = !isOpen.value;
    }

    const handleClickOutside = (event) => {
        if (isOpen.value && !event.target.closest('.status-dd')) {
            isOpen.value = false;
        }
    }

    const capitalizeString = (str) => {
        // Check if the string is empty or null
        if (!str) return '';
        
        // Capitalize the first character and concatenate it with the rest of the string
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    onMounted(() => {
        document.body.addEventListener('click', handleClickOutside);
    });

    onUnmounted(() => {
        document.body.removeEventListener('click', handleClickOutside);
    });
</script>
<template>
    <div class="relative text-sm">
        <div @click="toggleDropdown()" class="status-dd">
            <button
                type="button"
                class="cursor-pointer inline-flex items-center gap-1.5 bg-slate-100/80 dark:bg-zinc-800/80 hover:bg-slate-200/80 dark:hover:bg-zinc-700/80 border border-slate-200/80 dark:border-zinc-700/80 rounded-xl px-2.5 py-1 text-xs font-semibold text-slate-700 dark:text-zinc-300 transition-colors focus:outline-none"
            >
                <span class="capitalize">{{ $t(capitalizeString(props.status)) }}</span>
                <span class="text-slate-400 dark:text-zinc-500 font-normal">({{ props.rowCount }})</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>

        <div
            v-if="isOpen"
            class="absolute left-0 z-30 p-1.5 mt-1.5 bg-white dark:bg-[#18181B] border border-slate-200/80 dark:border-zinc-800 rounded-xl shadow-elevated w-36 text-xs transition-all"
        >
            <div class="space-y-0.5">
                <Link
                    :href="'/chats?status=all'"
                    :class="[
                        'block px-2.5 py-1.5 rounded-lg transition-colors',
                        props.status === 'all'
                            ? 'bg-purple-50 dark:bg-purple-950/50 text-[#6C5CE7] dark:text-purple-300 font-semibold'
                            : 'text-slate-700 dark:text-zinc-300 hover:bg-slate-50 dark:hover:bg-zinc-800'
                    ]"
                >
                    {{ $t('All Chats') }} 
                </Link>
                <Link
                    :href="'/chats?status=open'"
                    :class="[
                        'block px-2.5 py-1.5 rounded-lg transition-colors',
                        props.status === 'open'
                            ? 'bg-purple-50 dark:bg-purple-950/50 text-[#6C5CE7] dark:text-purple-300 font-semibold'
                            : 'text-slate-700 dark:text-zinc-300 hover:bg-slate-50 dark:hover:bg-zinc-800'
                    ]"
                >
                    {{ $t('Open Tickets') }} 
                </Link>
                <Link
                    :href="'/chats?status=unassigned'"
                    :class="[
                        'block px-2.5 py-1.5 rounded-lg transition-colors',
                        props.status === 'unassigned'
                            ? 'bg-purple-50 dark:bg-purple-950/50 text-[#6C5CE7] dark:text-purple-300 font-semibold'
                            : 'text-slate-700 dark:text-zinc-300 hover:bg-slate-50 dark:hover:bg-zinc-800'
                    ]"
                >
                    {{ $t('Unassigned') }} 
                </Link>
                <Link
                    :href="'/chats?status=closed'"
                    :class="[
                        'block px-2.5 py-1.5 rounded-lg transition-colors',
                        props.status === 'closed'
                            ? 'bg-purple-50 dark:bg-purple-950/50 text-[#6C5CE7] dark:text-purple-300 font-semibold'
                            : 'text-slate-700 dark:text-zinc-300 hover:bg-slate-50 dark:hover:bg-zinc-800'
                    ]"
                >
                    {{ $t('Closed') }} 
                </Link>
            </div>
        </div>
    </div>
</template>