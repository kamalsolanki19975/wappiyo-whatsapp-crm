<script setup>
    import { ref, onMounted, onUnmounted } from 'vue';
    import { router, Link } from "@inertiajs/vue3";

    const props = defineProps({
        direction: String,
        url: String,
    })

    const isOpen = ref(false);

    const toggleDropdown = () => {
        isOpen.value = !isOpen.value;
    }

    const handleClickOutside = (event) => {
        if (isOpen.value && !event.target.closest('.sort-dd')) {
            isOpen.value = false;
        }
    }

    onMounted(() => {
        document.body.addEventListener('click', handleClickOutside);
    });

    onUnmounted(() => {
        document.body.removeEventListener('click', handleClickOutside);
    });

    const sort = (value) => {
        router.post(props.url, {
            'sort' : value
        }, {
            preserveState: false,
        })
    }
</script>
<template>
    <div class="relative text-sm">
        <button
            type="button"
            @click="toggleDropdown()"
            class="p-1.5 rounded-lg text-slate-500 dark:text-zinc-400 hover:text-slate-800 dark:hover:text-zinc-200 hover:bg-slate-100 dark:hover:bg-zinc-800 transition-colors sort-dd focus:outline-none"
            :title="direction === 'desc' ? 'Newest First' : 'Oldest First'"
        >
            <svg v-if="direction === 'desc'" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 16 16" fill="currentColor">
                <path d="M3.5 13.5a.5.5 0 0 1-1 0V4.707L1.354 5.854a.5.5 0 1 1-.708-.708l2-1.999l.007-.007a.5.5 0 0 1 .7.006l2 2a.5.5 0 1 1-.707.708L3.5 4.707zm4-9.5a.5.5 0 0 1 0-1h1a.5.5 0 0 1 0 1zm0 3a.5.5 0 0 1 0-1h3a.5.5 0 0 1 0 1zm0 3a.5.5 0 0 1 0-1h5a.5.5 0 0 1 0 1zM7 12.5a.5.5 0 0 0 .5.5h7a.5.5 0 0 0 0-1h-7a.5.5 0 0 0-.5.5"/>
            </svg>
            <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 16 16" fill="currentColor">
                <path d="M3.5 2.5a.5.5 0 0 0-1 0v8.793l-1.146-1.147a.5.5 0 0 0-.708.708l2 1.999l.007.007a.497.497 0 0 0 .7-.006l2-2a.5.5 0 0 0-.707-.708L3.5 11.293zm3.5 1a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5M7.5 6a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1zm0 3a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1zm0 3a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1z"/>
            </svg>
        </button>

        <div
            v-if="isOpen"
            class="absolute right-0 z-30 p-1.5 mt-1.5 bg-white dark:bg-[#18181B] border border-slate-200/80 dark:border-zinc-800 rounded-xl shadow-elevated w-32 text-xs transition-all"
        >
            <button
                type="button"
                @click="sort('desc')"
                :class="[
                    'w-full text-left px-2.5 py-1.5 rounded-lg transition-colors cursor-pointer',
                    direction === 'desc'
                        ? 'bg-purple-50 dark:bg-purple-950/50 text-[#6C5CE7] dark:text-purple-300 font-semibold'
                        : 'text-slate-700 dark:text-zinc-300 hover:bg-slate-50 dark:hover:bg-zinc-800'
                ]"
            >
                {{ $t('Newest First') }}
            </button>
            <button
                type="button"
                @click="sort('asc')"
                :class="[
                    'w-full text-left px-2.5 py-1.5 rounded-lg transition-colors cursor-pointer',
                    direction === 'asc'
                        ? 'bg-purple-50 dark:bg-purple-950/50 text-[#6C5CE7] dark:text-purple-300 font-semibold'
                        : 'text-slate-700 dark:text-zinc-300 hover:bg-slate-50 dark:hover:bg-zinc-800'
                ]"
            >
                {{ $t('Oldest First') }}
            </button>
        </div>
    </div>
</template>