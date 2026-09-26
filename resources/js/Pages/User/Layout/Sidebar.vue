<template>
    <aside
        class="md:flex flex-col h-full bg-white dark:bg-[#111113] hidden relative shrink-0 transition-all duration-300 ease-in-out z-20"
        :class="menuIconsOnly ? 'w-20' : 'w-64 lg:w-72'"
    >
        <!-- Floating Sidebar Collapse Toggle Button -->
        <button
            @click="toggleMenu"
            type="button"
            class="absolute -right-3.5 top-5 bg-white dark:bg-[#18181B] text-slate-500 dark:text-zinc-400 hover:text-slate-900 dark:hover:text-white w-7 h-7 flex items-center justify-center rounded-full shadow-md border border-slate-200/80 dark:border-zinc-700 z-30 transition-transform duration-150 hover:scale-110 focus:outline-none"
            :title="menuIconsOnly ? 'Expand Sidebar' : 'Collapse Sidebar'"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="w-3.5 h-3.5 transition-transform duration-300"
                :class="menuIconsOnly ? 'rotate-180' : ''"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
            >
                <polyline points="15 18 9 12 15 6" />
            </svg>
        </button>

        <Menu
            :config="props.config"
            :user="props.user"
            :unreadMessages="unreadMessages"
            :organization="props.organization"
            :organizations="props.organizations"
            :menuIconsOnly="menuIconsOnly"
        />
    </aside>
</template>

<script setup>
import { defineProps, ref } from "vue";
import Menu from "./Menu.vue";

const props = defineProps(['user', 'organization', 'organizations', 'config', 'unreadMessages']);

const menuIconsOnly = ref(localStorage.getItem('MenuOpen') === 'true');

const toggleMenu = () => {
    menuIconsOnly.value = !menuIconsOnly.value;
    localStorage.setItem('MenuOpen', String(menuIconsOnly.value));
};
</script>

