<script setup>
import { usePage } from "@inertiajs/vue3";
import { ref, computed } from 'vue';
import { GoogleMap, Marker } from "vue3-google-map";

const props = defineProps({
    content: {
        type: Object,
        required: true,
    },
    type: {
        type: String,
        default: 'inbound',
    },
});

const downloading = ref(false);

const downloadClicked = () => {
    downloading.value = true;
    setTimeout(() => {
        downloading.value = false;
    }, 2000);
};

const getExtension = (fileFormat) => {
    const formatMap = {
        'text/plain': 'TXT',
        'application/pdf': 'PDF',
        'application/powerpoint': 'PPT',
        'application/vnd.ms-powerpoint': 'PPT',
        'application/msword': 'DOC',
        'application/vnd.ms-excel': 'XLS',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document': 'DOCX',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation': 'PPTX',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet': 'XLSX',
    };
    return formatMap[fileFormat] || 'FILE';
};

const formatFileSize = (sizeInBytes) => {
    if (!sizeInBytes || sizeInBytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    const i = parseInt(Math.floor(Math.log(sizeInBytes) / Math.log(k)));
    return Math.round((sizeInBytes / Math.pow(k, i)) * 100) / 100 + ' ' + sizes[i];
};

const getContactDisplayName = (metadata) => {
    try {
        const parsed = typeof metadata === 'string' ? JSON.parse(metadata) : metadata;
        const contacts = parsed.contacts || [];
        if (contacts.length === 1) {
            const contact = contacts[0];
            return contact.name?.formatted_name || `${contact.name?.first_name || ''} ${contact.name?.last_name || ''}`.trim() || 'Contact';
        } else if (contacts.length > 1) {
            return `${contacts[0].name?.first_name || 'Contact'} +${contacts.length - 1} other contacts`;
        }
    } catch (_) {}
    return 'Contact';
};

const parsedMetadata = computed(() => {
    try {
        return typeof props.content?.metadata === 'string'
            ? JSON.parse(props.content.metadata)
            : (props.content?.metadata || {});
    } catch (_) {
        return {};
    }
});

const location = (metadata) => {
    try {
        const item = typeof metadata === 'string' ? JSON.parse(metadata) : metadata;
        return { lat: item.location?.latitude || 0, lng: item.location?.longitude || 0 };
    } catch (_) {
        return { lat: 0, lng: 0 };
    }
};

const getValueByKey = (key) => {
    const config = computed(() => usePage().props.config);
    if (!config.value || !Array.isArray(config.value)) return '';
    const found = config.value.find(item => item.key === key);
    return found ? found.value : '';
};

const chatStatus = (logs) => {
    if (!logs || !Array.isArray(logs) || logs.length === 0) return 'sent';
    let status = 'sent';

    logs.forEach(log => {
        try {
            const metadata = typeof log.metadata === 'string' ? JSON.parse(log.metadata) : log.metadata;
            const logStatus = metadata?.status;

            if (logStatus === 'failed') {
                status = 'failed';
            } else if (logStatus === 'read') {
                status = 'read';
            } else if (logStatus === 'delivered' && status !== 'read') {
                status = 'delivered';
            }
        } catch (_) {}
    });

    return status;
};

const isOutbound = computed(() => props.type === 'outbound');
</script>

<template>
    <div
        class="group relative max-w-[85%] sm:max-w-[75%] md:max-w-[65%] rounded-2xl p-3 sm:p-3.5 text-sm shadow-subtle transition-all duration-150"
        :class="isOutbound
            ? 'ml-auto rounded-tr-xs bg-gradient-to-tr from-[#6C5CE7] to-[#5B46D6] text-white'
            : 'mr-auto rounded-tl-xs bg-white dark:bg-[#18181B] text-slate-900 dark:text-zinc-100 border border-slate-200/80 dark:border-zinc-800'"
    >
        <!-- Message Content Based on Type -->
        <div>
            <!-- 1. Text Message -->
            <div v-if="parsedMetadata.type === 'text'" class="space-y-2">
                <p class="whitespace-pre-wrap leading-relaxed break-words text-xs sm:text-sm">
                    {{ parsedMetadata.text?.body }}
                </p>

                <!-- Interactive Buttons attached to text -->
                <div v-if="parsedMetadata.buttons && parsedMetadata.buttons.length > 0" class="pt-1.5 space-y-1">
                    <div
                        v-for="(item, index) in parsedMetadata.buttons"
                        :key="index"
                        class="flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold shadow-xs"
                        :class="isOutbound
                            ? 'bg-white/15 text-white hover:bg-white/20'
                            : 'bg-purple-50 dark:bg-purple-950/40 text-[#6C5CE7] dark:text-purple-300 border border-purple-200/60 dark:border-purple-800/40'"
                    >
                        <svg v-if="item.type === 'COPY_CODE'" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
                        <svg v-else-if="item.type === 'PHONE_NUMBER'" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        <svg v-else-if="item.type === 'URL'" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 17 4 12 9 7"/><path d="M20 18v-2a4 4 0 0 0-4-4H4"/></svg>
                        <span>{{ item.text }}</span>
                    </div>
                </div>
            </div>

            <!-- 2. Button Reply -->
            <div v-else-if="parsedMetadata.type === 'button'">
                <p class="whitespace-pre-wrap leading-relaxed break-words text-xs sm:text-sm">
                    {{ parsedMetadata.button?.text }}
                </p>
            </div>

            <!-- 3. Interactive Response -->
            <div v-else-if="parsedMetadata.type === 'interactive'">
                <p class="whitespace-pre-wrap leading-relaxed break-words text-xs sm:text-sm font-semibold">
                    {{ parsedMetadata.interactive?.button_reply?.title || parsedMetadata.interactive?.list_reply?.title }}
                </p>
            </div>

            <!-- 4. Image Message -->
            <div v-else-if="parsedMetadata.type === 'image'" class="space-y-1.5">
                <div v-if="content.media" class="overflow-hidden rounded-xl border border-black/10 dark:border-white/10 max-w-sm">
                    <img
                        :src="content.media.path"
                        alt="Image attachment"
                        class="w-full object-cover max-h-72 hover:scale-102 transition-transform duration-200"
                        loading="lazy"
                    />
                </div>
                <div v-else class="flex items-center gap-2 p-4 text-xs opacity-80">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <span>{{ $t('Photo unavailable') }}</span>
                </div>

                <p v-if="parsedMetadata.image?.caption" class="text-xs pt-1 whitespace-pre-wrap">
                    {{ parsedMetadata.image.caption }}
                </p>
            </div>

            <!-- 5. Document / File Attachment -->
            <div v-else-if="parsedMetadata.type === 'document'" class="space-y-1.5">
                <a
                    v-if="content.media"
                    :href="'/chats/' + content.id + '/media'"
                    @click="downloadClicked"
                    class="flex items-center justify-between gap-3 p-3 rounded-xl border transition-colors max-w-sm"
                    :class="isOutbound
                        ? 'bg-white/15 border-white/20 hover:bg-white/25 text-white'
                        : 'bg-slate-50 dark:bg-zinc-800/80 border-slate-200 dark:border-zinc-700 text-slate-800 dark:text-zinc-200 hover:bg-slate-100'"
                >
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="h-9 w-9 rounded-lg flex items-center justify-center font-bold text-xs uppercase" :class="isOutbound ? 'bg-white text-[#6C5CE7]' : 'bg-[#6C5CE7] text-white'">
                            {{ getExtension(content.media.type) }}
                        </div>
                        <div class="min-w-0">
                            <h4 class="text-xs font-semibold truncate max-w-[180px]">
                                {{ content.media.file_name || 'Document' }}
                            </h4>
                            <p class="text-[10px] opacity-75">
                                {{ formatFileSize(content.media.file_size) }}
                            </p>
                        </div>
                    </div>

                    <div class="shrink-0 p-1.5 rounded-lg" :class="isOutbound ? 'bg-white/20' : 'bg-slate-200 dark:bg-zinc-700'">
                        <svg v-if="!downloading" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        <svg v-else class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                    </div>
                </a>

                <p v-if="parsedMetadata.document?.caption" class="text-xs pt-1 whitespace-pre-wrap">
                    {{ parsedMetadata.document.caption }}
                </p>
            </div>

            <!-- 6. Video Message -->
            <div v-else-if="parsedMetadata.type === 'video'" class="space-y-1.5">
                <div v-if="content.media" class="overflow-hidden rounded-xl border border-black/10 dark:border-white/10 max-w-sm">
                    <video controls class="w-full max-h-72 rounded-xl">
                        <source :src="content.media.path" type="video/mp4" />
                        {{ $t('Your browser does not support video playback') }}
                    </video>
                </div>
                <div v-else class="flex items-center gap-2 p-4 text-xs opacity-80">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="23 7 16 12 23 17 23 7"/><rect width="15" height="14" x="1" y="5" rx="2" ry="2"/></svg>
                    <span>{{ $t('Video unavailable') }}</span>
                </div>

                <p v-if="parsedMetadata.video?.caption" class="text-xs pt-1 whitespace-pre-wrap">
                    {{ parsedMetadata.video.caption }}
                </p>
            </div>

            <!-- 7. Audio / Voice Note -->
            <div v-else-if="parsedMetadata.type === 'audio'" class="min-w-[240px] max-w-xs">
                <audio v-if="content.media" controls class="w-full h-9">
                    <source :src="content.media.path" />
                    {{ $t('Your browser does not support audio playback') }}
                </audio>
                <div v-else class="flex items-center gap-2 p-2 text-xs opacity-80">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/></svg>
                    <span>{{ $t('Voice note unavailable') }}</span>
                </div>
            </div>

            <!-- 8. Location Card -->
            <div v-else-if="parsedMetadata.type === 'location'" class="space-y-1.5">
                <div class="h-44 w-64 rounded-xl overflow-hidden border border-black/10 dark:border-white/10">
                    <GoogleMap
                        :api-key="getValueByKey('google_maps_api_key')"
                        class="w-full h-full"
                        :center="location(content.metadata)"
                        :zoom="15"
                    >
                        <Marker :options="{ position: location(content.metadata) }" />
                    </GoogleMap>
                </div>
                <div v-if="parsedMetadata.location?.name" class="text-xs font-semibold">
                    📍 {{ parsedMetadata.location.name }}
                </div>
            </div>

            <!-- 9. Contact Card (vCard) -->
            <div v-else-if="parsedMetadata.type === 'contacts'" class="flex items-center gap-3 p-2 min-w-[220px]">
                <div class="h-10 w-10 rounded-xl flex items-center justify-center shrink-0" :class="isOutbound ? 'bg-white/20' : 'bg-slate-100 dark:bg-zinc-800'">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" x2="19" y1="8" y2="14"/><line x1="22" x2="16" y1="11" y2="11"/></svg>
                </div>
                <div class="min-w-0">
                    <div class="text-xs font-bold truncate">
                        {{ getContactDisplayName(content.metadata) }}
                    </div>
                    <div class="text-[10px] opacity-75">
                        {{ $t('WhatsApp Contact') }}
                    </div>
                </div>
            </div>

            <!-- 10. Sticker Message -->
            <div v-else-if="parsedMetadata.type === 'sticker'" class="max-w-[120px]">
                <img v-if="content.media" :src="content.media.path" alt="Sticker" class="w-full h-auto" />
                <div v-else class="text-xs opacity-75">
                    {{ $t('Sticker') }}
                </div>
            </div>
        </div>

        <!-- Bubble Footer: Agent name, Timestamp & Delivery Receipts -->
        <div
            class="flex items-center justify-end gap-1.5 mt-1 pt-0.5 text-[10px]"
            :class="isOutbound ? 'text-white/80' : 'text-slate-400 dark:text-zinc-500'"
        >
            <!-- Agent Name on Outbound -->
            <span v-if="isOutbound && content.user" class="font-medium truncate max-w-[120px]">
                {{ content.user.first_name }} •
            </span>

            <!-- Timestamp -->
            <span>{{ content.created_at }}</span>

            <!-- Outbound Delivery Status Icon -->
            <span v-if="isOutbound" class="inline-flex items-center" :title="chatStatus(content.logs)">
                <!-- Read: Double Check Cyan -->
                <svg
                    v-if="chatStatus(content.logs) === 'read'"
                    xmlns="http://www.w3.org/2000/svg"
                    class="w-3.5 h-3.5 text-cyan-300"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2.5"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path d="M18 6 7 17l-5-5"/><path d="m22 10-7.5 7.5L13 16"/>
                </svg>

                <!-- Delivered: Double Check Muted White -->
                <svg
                    v-else-if="chatStatus(content.logs) === 'delivered'"
                    xmlns="http://www.w3.org/2000/svg"
                    class="w-3.5 h-3.5 text-white/70"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2.5"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path d="M18 6 7 17l-5-5"/><path d="m22 10-7.5 7.5L13 16"/>
                </svg>

                <!-- Sent: Single Check -->
                <svg
                    v-else-if="chatStatus(content.logs) === 'sent'"
                    xmlns="http://www.w3.org/2000/svg"
                    class="w-3.5 h-3.5 text-white/60"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2.5"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <polyline points="20 6 9 17 4 12"/>
                </svg>

                <!-- Failed: Red Warning -->
                <svg
                    v-else-if="chatStatus(content.logs) === 'failed'"
                    xmlns="http://www.w3.org/2000/svg"
                    class="w-3.5 h-3.5 text-rose-300"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2.5"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
            </span>
        </div>
    </div>
</template>