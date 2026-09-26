<script setup>
    import { ref, onMounted, onUnmounted } from 'vue';

    const props = defineProps({
        languages: Object,
        currentLanguage: String,
    })

    const isOpen = ref(false);

    const toggleDropdown = () => {
        isOpen.value = !isOpen.value;
    }

    const handleClickOutside = (event) => {
        if (isOpen.value && !event.target.closest('.lang-dd')) {
            isOpen.value = false;
        }
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
        <div @click="toggleDropdown()" class="lang-dd flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg border border-slate-200 dark:border-zinc-800 bg-white dark:bg-[#18181B] text-slate-700 dark:text-zinc-300 hover:bg-slate-50 dark:hover:bg-zinc-800 hover:text-slate-900 dark:hover:text-white transition-colors cursor-pointer select-none">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400 dark:text-zinc-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
            <span class="uppercase text-xs font-semibold tracking-wider">{{ props.currentLanguage }}</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-slate-400 dark:text-zinc-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
        </div>
        <div v-if="isOpen" class="absolute right-0 bg-white dark:bg-[#18181B] z-50 p-1.5 mt-1.5 shadow-floating w-36 rounded-xl border border-slate-200/80 dark:border-zinc-700 text-slate-800 dark:text-zinc-200">
            <div class="space-y-0.5">
                <a v-for="(item, index) in props.languages" :key="index" :href="'/language/' + item.code" class="block px-3 py-1.5 text-xs font-medium cursor-pointer hover:bg-purple-50 dark:hover:bg-purple-950/50 hover:text-[#6C5CE7] dark:hover:text-purple-300 rounded-lg transition-colors">
                    {{ item.name }}
                </a>
            </div>
        </div>
    </div>
</template>