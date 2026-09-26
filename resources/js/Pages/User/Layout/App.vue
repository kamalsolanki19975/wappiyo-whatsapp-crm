<template>
    <div class="min-h-screen bg-slate-50/70 dark:bg-[#09090B] text-slate-900 dark:text-zinc-100 antialiased flex flex-col font-sans transition-colors duration-200">
        <!-- Mobile Sidebar -->
        <MobileSidebar
            :user="user"
            :config="config"
            :organization="organization"
            :organizations="organizations"
            :title="currentPageTitle"
            :displayCreateBtn="displayCreateBtn"
            :displayTopBar="viewTopBar"
        />

        <div class="flex h-screen w-full overflow-hidden">
            <!-- Desktop Sidebar -->
            <Sidebar
                :user="user"
                :config="config"
                :organization="organization"
                :organizations="organizations"
                :unreadMessages="unreadMessages"
            />

            <!-- Main Content Area -->
            <div class="flex flex-col flex-1 min-w-0 h-full overflow-hidden bg-slate-50/70 dark:bg-[#09090B]">
                <!-- Top Header Bar -->
                <Header
                    v-if="viewTopBar"
                    class="hidden md:flex"
                    :user="user"
                    :organization="organization"
                    :organizations="organizations"
                    :unreadMessages="unreadMessages"
                    :languages="languages"
                    :currentLanguage="currentLanguage"
                    @openProfile="isProfileModalOpen = true"
                    @switchTeams="isLocationSwitchModalOpen = true"
                />

                <!-- Page View Slot -->
                <main class="flex-1 overflow-y-auto min-w-0">
                    <slot :user="user" :toggleNavBar="toggleTopBar" @testEmit="doSomething" />
                </main>
            </div>
        </div>

        <!-- Global Command Palette (⌘K) -->
        <CommandPalette />

        <!-- Switch Teams Modal from Header -->
        <Modal :label="$t('Switch teams')" :isOpen="isLocationSwitchModalOpen" @close="isLocationSwitchModalOpen = false">
            <div class="mt-2 space-y-2">
                <div
                    v-for="(item, index) in organizations"
                    :key="index"
                    class="flex items-center justify-between p-3 rounded-xl border border-slate-200 dark:border-zinc-800 hover:border-[#6C5CE7] hover:bg-purple-50/50 dark:hover:bg-purple-950/30 cursor-pointer transition-all duration-150"
                    @click="selectOrganization(item.organization?.uuid)"
                >
                    <div class="flex items-center gap-3">
                        <span class="w-9 h-9 rounded-xl bg-purple-100 dark:bg-purple-950/60 text-[#6C5CE7] flex items-center justify-center font-bold text-sm">
                            {{ item.organization?.name ? item.organization.name[0].toUpperCase() : 'T' }}
                        </span>
                        <div>
                            <h4 class="font-semibold text-sm text-slate-900 dark:text-white">{{ item.organization?.name }}</h4>
                            <p class="text-xs text-slate-400 dark:text-zinc-500 capitalize">{{ item.role }}</p>
                        </div>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 dark:border-zinc-800 flex justify-end">
                <button
                    type="button"
                    class="px-4 py-2 text-xs font-semibold text-slate-700 dark:text-zinc-300 bg-slate-100 dark:bg-zinc-800 hover:bg-slate-200 rounded-lg transition-colors"
                    @click="isLocationSwitchModalOpen = false"
                >
                    {{ $t('Cancel') }}
                </button>
            </div>
        </Modal>

        <!-- Profile Modal from Header -->
        <ProfileModal
            :user="user"
            :organization="organization"
            :isOpen="isProfileModalOpen"
            role="user"
            @close="isProfileModalOpen = false"
        />

        <audio ref="audioPlayer" allow="autoplay"></audio>
    </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import { usePage, useForm } from "@inertiajs/vue3";
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
import Sidebar from "./Sidebar.vue";
import MobileSidebar from "./MobileSidebar.vue";
import Header from "@/Components/UI/Header.vue";
import CommandPalette from "@/Components/UI/CommandPalette.vue";
import Modal from "@/Components/Modal.vue";
import ProfileModal from "@/Components/ProfileModal.vue";
import { getEchoInstance } from '../../../echo';
import { useCommandPalette } from "@/Composables/useCommandPalette";
import { useTheme } from "@/Composables/useTheme";

const { setupKeyboardListener } = useCommandPalette();
const { initTheme } = useTheme();

const viewTopBar = ref(true);
const user = computed(() => usePage().props.auth?.user || {});
const config = computed(() => usePage().props.config || []);
const organization = computed(() => usePage().props.organization || {});
const organizations = computed(() => usePage().props.organizations || []);
const currentPageTitle = computed(() => usePage().props.title || '');
const displayCreateBtn = computed(() => usePage().props.allowCreate || false);
const unreadMessages = ref(usePage().props.unreadMessages || 0);
const languages = computed(() => usePage().props.languages || {});
const currentLanguage = computed(() => usePage().props.currentLanguage || 'en');

const audioPlayer = ref(null);
const isLocationSwitchModalOpen = ref(false);
const isProfileModalOpen = ref(false);

const form = useForm({
    uuid: null,
});

const selectOrganization = (uuid) => {
    if (!uuid) return;
    form.uuid = uuid;
    form.post('/organization', {
        preserveScroll: true,
        onFinish: () => { isLocationSwitchModalOpen.value = false; },
    });
};

watch(() => [usePage().props.flash, { deep: true }], () => {
    if (usePage().props.flash?.status != null) {
        toast(usePage().props.flash.status.message, {
            autoClose: 3000,
        });
    }
});

const toggleTopBar = () => {
    viewTopBar.value = !viewTopBar.value;
};

const getValueByKey = (key) => {
    if (!config.value || !Array.isArray(config.value)) return '';
    const found = config.value.find(item => item.key === key);
    return found ? found.value : '';
};

const setupSound = () => {
    if (!organization.value?.metadata) return;
    try {
        const settings = JSON.parse(organization.value.metadata);
        const notifications = settings.notifications || {};

        if (notifications?.enable_sound && audioPlayer.value) {
            audioPlayer.value.src = notifications?.tone;
            audioPlayer.value.volume = notifications?.volume || 1.0;
        }
    } catch (_) {}
};

const playSound = () => {
    if (audioPlayer.value) {
        audioPlayer.value.play().catch((error) => {
            console.warn("Audio playback failed:", error);
        });
    }
};

const doSomething = () => {};

onMounted(() => {
    initTheme();
    setupKeyboardListener();
    setupSound();

    if (organization.value?.id) {
        try {
            const echo = getEchoInstance(
                getValueByKey('pusher_app_key'),
                getValueByKey('pusher_app_cluster')
            );

            if (echo) {
                echo.channel('chats.ch' + organization.value.id).listen('NewChatEvent', (event) => {
                    const chat = event.chat;

                    if (chat && chat[0]?.value?.deleted_at == null && chat[0]?.value?.type === 'inbound') {
                        playSound();
                        unreadMessages.value += 1;
                    }
                });
            }
        } catch (e) {
            console.warn("Pusher echo initialization skipped or failed:", e);
        }
    }
});

onUnmounted(() => {
    if (organization.value?.id) {
        try {
            const echo = getEchoInstance(
                getValueByKey('pusher_app_key'),
                getValueByKey('pusher_app_cluster')
            );
            if (echo) {
                echo.leave('chats.ch' + organization.value.id);
            }
        } catch (_) {}
    }
});
</script>