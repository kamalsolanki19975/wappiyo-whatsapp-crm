<template>
    <div class="min-h-screen bg-slate-50/70 dark:bg-[#09090B] text-slate-900 dark:text-zinc-100 antialiased flex flex-col font-sans transition-colors duration-200">
        <!-- Mobile Sidebar -->
        <MobileSidebar
            :user="user"
            :config="config"
            :title="currentPageTitle"
            :displayCreateBtn="displayCreateBtn"
        />

        <div class="flex h-screen w-full overflow-hidden">
            <!-- Desktop Sidebar -->
            <Sidebar :user="user" :config="config" />

            <!-- Main Content Area -->
            <div class="flex flex-col flex-1 min-w-0 h-full overflow-hidden bg-slate-50/70 dark:bg-[#09090B]">
                <!-- Top Header Bar -->
                <Header
                    class="hidden md:flex"
                    :user="user"
                    :languages="languages"
                    :currentLanguage="currentLanguage"
                    @openProfile="isProfileModalOpen = true"
                />

                <!-- Page View Slot -->
                <main class="flex-1 overflow-y-auto min-w-0">
                    <slot :user="user" />
                </main>
            </div>
        </div>

        <!-- Global Command Palette (⌘K) -->
        <CommandPalette />

        <!-- Profile Modal from Header -->
        <ProfileModal
            :user="user"
            :organization="{}"
            :isOpen="isProfileModalOpen"
            role="admin"
            @close="isProfileModalOpen = false"
        />
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { usePage } from "@inertiajs/vue3";
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
import Sidebar from "./Sidebar.vue";
import MobileSidebar from "./MobileSidebar.vue";
import Header from "@/Components/UI/Header.vue";
import CommandPalette from "@/Components/UI/CommandPalette.vue";
import ProfileModal from "@/Components/ProfileModal.vue";
import { useCommandPalette } from "@/Composables/useCommandPalette";
import { useTheme } from "@/Composables/useTheme";

const { setupKeyboardListener } = useCommandPalette();
const { initTheme } = useTheme();

const user = computed(() => usePage().props.auth?.user || {});
const config = computed(() => usePage().props.config || []);
const currentPageTitle = computed(() => usePage().props.title || '');
const displayCreateBtn = computed(() => usePage().props.allowCreate || false);
const languages = computed(() => usePage().props.languages || {});
const currentLanguage = computed(() => usePage().props.currentLanguage || 'en');

const isProfileModalOpen = ref(false);

watch(() => [usePage().props.flash, { deep: true }], () => {
    if (usePage().props.flash?.status != null) {
        toast(usePage().props.flash.status.message, {
            autoClose: 3000,
        });
    }
});

onMounted(() => {
    initTheme();
    setupKeyboardListener();
});
</script>