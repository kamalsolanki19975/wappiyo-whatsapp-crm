<template>
    <!-- Mobile Glass Header Bar -->
    <div
        v-if="props.displayTopBar !== false"
        class="sticky top-0 z-40 w-full glass-header px-4 py-3 flex items-center justify-between md:hidden transition-colors"
    >
        <div class="flex items-center gap-3">
            <button
                type="button"
                class="p-2 rounded-xl text-slate-700 dark:text-zinc-300 hover:bg-slate-100 dark:hover:bg-zinc-800 transition-colors focus:outline-none"
                @click="isSidebarOpen = true"
                aria-label="Open menu"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
            </button>

            <h3 class="text-base font-bold text-slate-900 dark:text-white truncate">
                {{ props.title || 'Admin Center' }}
            </h3>
        </div>

        <div class="flex items-center gap-2">
            <Link
                v-if="props.displayCreateBtn"
                :href="$page.url + '/create'"
                class="p-2 rounded-xl bg-emerald-600 text-white shadow-sm shadow-emerald-600/30 hover:bg-emerald-700 transition-colors"
                title="Create"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            </Link>
            <button
                v-else
                type="button"
                class="p-1.5 rounded-xl text-slate-700 dark:text-zinc-300 hover:bg-slate-100 dark:hover:bg-zinc-800 transition-colors"
                @click="openModal"
                aria-label="Profile"
            >
                <Avatar
                    :src="props.user?.avatar ? '/media/' + props.user.avatar : null"
                    :name="`${props.user?.first_name || ''} ${props.user?.last_name || ''}`"
                    size="sm"
                />
            </button>
        </div>
    </div>

    <!-- Mobile Slide-out Drawer Backdrop & Container -->
    <div
        v-if="isSidebarOpen"
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 transition-opacity duration-300 md:hidden"
        @click="closeSidebar"
    />

    <aside
        class="fixed top-0 left-0 bottom-0 w-4/5 max-w-xs bg-white dark:bg-[#111113] z-50 transform transition-transform duration-300 ease-in-out md:hidden shadow-2xl flex flex-col"
        :class="isSidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    >
        <Menu
            :isSidebarOpen="isSidebarOpen"
            :config="props.config"
            :user="props.user"
            @closeSidebar="closeSidebar"
        />
    </aside>

    <ProfileModal
        :user="props.user"
        :organization="{}"
        :isOpen="isProfileOpen"
        role="admin"
        @close="closeModal"
    />
</template>

<script setup>
import { Link } from "@inertiajs/vue3";
import { defineProps, ref } from "vue";
import Menu from "./Menu.vue";
import ProfileModal from '@/Components/ProfileModal.vue';
import Avatar from '@/Components/UI/Avatar.vue';

const props = defineProps({
    title: {
        type: String,
    },
    displayTopBar: {
        type: Boolean,
        default: true,
    },
    displayCreateBtn: {
        type: [Boolean, String],
    },
    user: {
        type: Object,
        required: true,
    },
    config: {
        type: Array,
        required: true
    }
});

const isSidebarOpen = ref(false);
const isProfileOpen = ref(false);

const closeSidebar = () => {
    isSidebarOpen.value = false;
};

function openModal() {
    isProfileOpen.value = true;
    isSidebarOpen.value = false;
}

const closeModal = () => {
    isProfileOpen.value = false;
};

const openSidebar = () => {
    isSidebarOpen.value = true;
};

defineExpose({
    openSidebar,
    closeSidebar,
});
</script>