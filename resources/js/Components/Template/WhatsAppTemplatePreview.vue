<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    name: {
        type: String,
        default: 'Template Preview',
    },
    category: {
        type: String,
        default: 'UTILITY',
    },
    language: {
        type: String,
        default: 'en',
    },
    header: {
        type: Object,
        default: () => ({ format: 'TEXT', text: '', example: null }),
    },
    body: {
        type: Object,
        default: () => ({ text: '', example: [] }),
    },
    footer: {
        type: Object,
        default: () => ({ text: '' }),
    },
    buttons: {
        type: Array,
        default: () => [],
    },
    // If a full template model or metadata JSON is passed directly:
    templateData: {
        type: Object,
        default: null,
    },
    // Optional toggle to show sample values or raw variable tags
    showSamples: {
        type: Boolean,
        default: true,
    },
    interactive: {
        type: Boolean,
        default: false,
    },
});

const isSampleMode = ref(props.showSamples);

// Normalize data whether passed as separated props or as a templateData object
const normalizedData = computed(() => {
    if (props.templateData) {
        let meta = {};
        if (typeof props.templateData.metadata === 'string') {
            try {
                meta = JSON.parse(props.templateData.metadata);
            } catch (e) {
                meta = {};
            }
        } else if (typeof props.templateData.metadata === 'object' && props.templateData.metadata !== null) {
            meta = props.templateData.metadata;
        }

        const components = meta.components || [];
        const headerComp = components.find(c => c.type === 'HEADER') || {};
        const bodyComp = components.find(c => c.type === 'BODY') || {};
        const footerComp = components.find(c => c.type === 'FOOTER') || {};
        const buttonsComp = components.find(c => c.type === 'BUTTONS') || {};

        let buttonsList = [];
        if (buttonsComp.buttons) {
            buttonsList = buttonsComp.buttons;
        } else if (Array.isArray(meta.buttons)) {
            buttonsList = meta.buttons;
        }

        return {
            name: props.templateData.name || props.name,
            category: props.templateData.category || props.category,
            language: props.templateData.language || props.language,
            header: {
                format: headerComp.format || 'TEXT',
                text: headerComp.text || '',
                example: headerComp.example || null,
            },
            body: {
                text: bodyComp.text || '',
                example: (bodyComp.example && bodyComp.example.body_text && bodyComp.example.body_text[0]) || [],
            },
            footer: {
                text: footerComp.text || '',
            },
            buttons: buttonsList,
        };
    }

    return {
        name: props.name,
        category: props.category,
        language: props.language,
        header: props.header || { format: 'TEXT', text: '', example: null },
        body: props.body || { text: '', example: [] },
        footer: props.footer || { text: '' },
        buttons: props.buttons || [],
    };
});

// Format markdown bold, italic, strikethrough, monospace, and variables
const formattedBodyHtml = computed(() => {
    let text = normalizedData.value.body.text || '';
    if (!text) return '<span class="text-slate-400 italic">No message body content yet...</span>';

    // Replace variables with sample values if sample mode is on
    if (isSampleMode.value) {
        const samples = Array.isArray(normalizedData.value.body.example) 
            ? normalizedData.value.body.example 
            : [];

        text = text.replace(/\{\{(\d+)\}\}/g, (match, p1) => {
            const index = parseInt(p1, 10) - 1;
            const sample = samples[index];
            if (sample !== undefined && sample !== '') {
                return `<span class="bg-purple-100 dark:bg-purple-900/50 text-purple-700 dark:text-purple-300 font-medium px-1 rounded">${sample}</span>`;
            }
            return `<span class="bg-amber-100 dark:bg-amber-900/50 text-amber-700 dark:text-amber-300 font-mono text-xs px-1 rounded">${match}</span>`;
        });
    } else {
        text = text.replace(/\{\{(\d+)\}\}/g, '<span class="bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 font-mono text-xs px-1 rounded">$&</span>');
    }

    // Markdown replacements
    const boldRegex = /\*(.*?)\*/g;
    const italicRegex = /_(.*?)_/g;
    const strikethroughRegex = /~(.*?)~/g;
    const monospaceRegex = /```(.*?)```/g;

    let html = text
        .replace(boldRegex, '<b>$1</b>')
        .replace(italicRegex, '<i>$1</i>')
        .replace(strikethroughRegex, '<del>$1</del>')
        .replace(monospaceRegex, '<code class="bg-slate-100 dark:bg-zinc-800 px-1 py-0.5 rounded text-xs">$1</code>')
        .replace(/\n/g, '<br>');

    return html;
});

const formattedHeaderText = computed(() => {
    let text = normalizedData.value.header.text || '';
    if (!text) return '';
    if (isSampleMode.value && normalizedData.value.header.example) {
        const sample = typeof normalizedData.value.header.example === 'string'
            ? normalizedData.value.header.example
            : (normalizedData.value.header.example?.header_text?.[0] || '');
        if (sample) {
            text = text.replace(/\{\{1\}\}/g, sample);
        }
    }
    return text;
});

const currentTime = computed(() => {
    const d = new Date();
    return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
});

const mediaPreviewUrl = computed(() => {
    const ex = normalizedData.value.header.example;
    if (!ex) return null;
    if (typeof ex === 'string' && (ex.startsWith('http') || ex.startsWith('/'))) return ex;
    if (ex instanceof File) {
        return URL.createObjectURL(ex);
    }
    return null;
});
</script>

<template>
    <div class="flex flex-col items-center w-full">
        <!-- Interactive Mode Controls -->
        <div v-if="interactive" class="flex items-center justify-between w-full max-w-[340px] mb-2 px-1 text-xs">
            <span class="text-slate-500 dark:text-zinc-400 font-medium">{{ $t('WhatsApp Live Preview') }}</span>
            <button
                type="button"
                @click="isSampleMode = !isSampleMode"
                class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-semibold transition-colors"
                :class="isSampleMode 
                    ? 'bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-300 border border-purple-300 dark:border-purple-700' 
                    : 'bg-slate-100 dark:bg-zinc-800 text-slate-600 dark:text-zinc-400 border border-slate-200 dark:border-zinc-700'"
            >
                <span v-text="isSampleMode ? $t('Sample Values') : $t('Raw Variable Tokens')"></span>
            </button>
        </div>

        <!-- Phone Shell -->
        <div class="relative w-full max-w-[340px] rounded-[32px] p-2.5 bg-slate-900 dark:bg-black shadow-2xl border-4 border-slate-800 dark:border-zinc-800 overflow-hidden">
            <!-- Speaker notch / camera island -->
            <div class="absolute top-2 left-1/2 -translate-x-1/2 w-28 h-4 bg-slate-900 dark:bg-black rounded-b-xl z-30 flex items-center justify-center">
                <div class="w-12 h-1 bg-slate-700 rounded-full"></div>
            </div>

            <!-- Phone Screen -->
            <div class="relative w-full rounded-[24px] overflow-hidden flex flex-col bg-[#EFEAE2] dark:bg-[#0b141a] text-[#111B21] dark:text-[#E9EDEF] min-h-[480px]">
                
                <!-- WhatsApp Top App Bar -->
                <div class="bg-[#075E54] dark:bg-[#1F2C34] text-white px-3 pt-6 pb-2.5 flex items-center justify-between shrink-0 shadow-sm z-20">
                    <div class="flex items-center gap-2">
                        <div class="w-4 flex items-center text-white/80">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m15 18-6-6 6-6"/></svg>
                        </div>
                        <div class="relative w-8 h-8 rounded-full bg-emerald-600 flex items-center justify-center text-white font-bold text-xs shadow-sm">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91C2.13 13.66 2.59 15.36 3.45 16.86L2.05 22L7.3 20.62C8.75 21.41 10.38 21.83 12.04 21.83C17.5 21.83 21.95 17.38 21.95 11.92C21.95 9.27 20.92 6.78 19.05 4.91C17.18 3.04 14.69 2 12.04 2Z"/>
                            </svg>
                        </div>
                        <div class="flex flex-col leading-tight min-w-0">
                            <div class="flex items-center gap-1">
                                <span class="font-semibold text-xs truncate max-w-[130px]">{{ $t('Wappiyo Business') }}</span>
                                <svg class="w-3.5 h-3.5 text-emerald-400 shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                </svg>
                            </div>
                            <span class="text-[10px] text-emerald-100/70">{{ $t('verified business') }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 text-white/80">
                        <svg class="w-4 h-4 cursor-pointer" viewBox="0 0 24 24" fill="currentColor"><path d="M20 15.5c-1.25 0-2.45-.2-3.57-.57a1.02 1.02 0 0 0-1.02.24l-2.2 2.2a15.045 15.045 0 0 1-6.59-6.59l2.2-2.21a.96.96 0 0 0 .25-1A11.36 11.36 0 0 1 8.5 4c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1 0 9.39 7.61 17 17 17 .55 0 1-.45 1-1v-3.5c0-.55-.45-1-1-1zM19 12h2a9 9 0 0 0-9-9v2c3.87 0 7 3.13 7 7zm-4 0h2c0-2.76-2.24-5-5-5v2c1.66 0 3 1.34 3 3z"/></svg>
                        <svg class="w-4 h-4 cursor-pointer" viewBox="0 0 24 24" fill="currentColor"><path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/></svg>
                    </div>
                </div>

                <!-- Chat Canvas Background Pattern -->
                <div class="flex-1 p-3 overflow-y-auto relative flex flex-col justify-end">
                    <!-- Subtle WhatsApp doodle background -->
                    <div 
                        class="absolute inset-0 opacity-[0.06] dark:opacity-[0.03] pointer-events-none bg-repeat"
                        style="background-image: radial-gradient(circle, #000 1px, transparent 1px); background-size: 16px 16px;"
                    ></div>

                    <!-- Date separator badge -->
                    <div class="flex justify-center mb-3">
                        <span class="bg-white/80 dark:bg-[#182229]/80 backdrop-blur-sm text-[10px] font-medium text-slate-600 dark:text-zinc-400 px-2.5 py-0.5 rounded-md shadow-sm">
                            {{ $t('TODAY') }}
                        </span>
                    </div>

                    <!-- Message Bubble & Buttons Wrapper -->
                    <div class="relative z-10 max-w-[92%] mr-auto">
                        <!-- Message Card -->
                        <div class="bg-white dark:bg-[#1F2C34] rounded-2xl rounded-tl-sm p-3 shadow-md border border-black/5 dark:border-white/5 space-y-2">
                            
                            <!-- Header Component -->
                            <div v-if="normalizedData.header && normalizedData.header.format !== 'NONE'" class="space-y-1">
                                <!-- Text Header -->
                                <div v-if="normalizedData.header.format === 'TEXT' && (formattedHeaderText || normalizedData.header.text)" class="font-bold text-sm text-[#111B21] dark:text-[#E9EDEF] leading-snug">
                                    {{ formattedHeaderText || normalizedData.header.text }}
                                </div>

                                <!-- Image Header -->
                                <div v-else-if="normalizedData.header.format === 'IMAGE'" class="relative rounded-xl overflow-hidden bg-slate-200 dark:bg-zinc-800 aspect-[16/9] flex items-center justify-center border border-black/5 dark:border-white/5">
                                    <img v-if="mediaPreviewUrl" :src="mediaPreviewUrl" alt="Header Preview" class="w-full h-full object-cover" />
                                    <div v-else class="flex flex-col items-center justify-center text-slate-400 dark:text-zinc-500 gap-1">
                                        <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect width="18" height="18" x="3" y="3" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                                        <span class="text-[10px] font-medium tracking-wide uppercase">{{ $t('Image Header') }}</span>
                                    </div>
                                </div>

                                <!-- Video Header -->
                                <div v-else-if="normalizedData.header.format === 'VIDEO'" class="relative rounded-xl overflow-hidden bg-slate-900 aspect-[16/9] flex items-center justify-center text-white border border-black/5">
                                    <div class="w-10 h-10 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center text-white shadow-lg">
                                        <svg class="w-5 h-5 fill-current ml-0.5" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                    </div>
                                    <span class="absolute bottom-2 left-2 text-[10px] bg-black/60 px-1.5 py-0.5 rounded text-white/90">{{ $t('Video') }}</span>
                                </div>

                                <!-- Document Header -->
                                <div v-else-if="normalizedData.header.format === 'DOCUMENT'" class="flex items-center gap-2.5 p-2.5 bg-slate-100 dark:bg-zinc-800/70 rounded-xl border border-black/5 dark:border-white/5">
                                    <div class="w-8 h-8 rounded-lg bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm4 18H6V4h7v5h5v11z"/></svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-semibold text-slate-800 dark:text-zinc-200 truncate">{{ $t('Document Attachment') }}</p>
                                        <p class="text-[10px] text-slate-400 dark:text-zinc-500">PDF • 1.2 MB</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Body Component -->
                            <div class="text-[13px] leading-relaxed text-[#111B21] dark:text-[#E9EDEF] whitespace-pre-wrap break-words" v-html="formattedBodyHtml"></div>

                            <!-- Footer Component -->
                            <div v-if="normalizedData.footer && normalizedData.footer.text" class="text-[11px] text-[#667781] dark:text-[#8696A0] pt-0.5">
                                {{ normalizedData.footer.text }}
                            </div>

                            <!-- Timestamp and Sent receipt ticks -->
                            <div class="flex items-center justify-end gap-1 text-[10px] text-[#667781] dark:text-[#8696A0] -mb-1 select-none">
                                <span>{{ currentTime }}</span>
                                <svg class="w-3.5 h-3.5 text-[#53bdeb]" viewBox="0 0 16 15" fill="currentColor">
                                    <path d="M15.01 3.316l-.478-.372a.365.365 0 0 0-.51.063L8.666 9.879a.32.32 0 0 1-.484.033l-.358-.325a.319.319 0 0 0-.484.032l-.378.483a.418.418 0 0 0 .036.541l1.32 1.266c.143.14.361.125.484-.033l6.272-8.048a.366.366 0 0 0-.064-.512zm-4.1 0l-.478-.372a.365.365 0 0 0-.51.063L4.566 9.879a.32.32 0 0 1-.484.033L1.891 7.769a.366.366 0 0 0-.515.006l-.423.433a.364.364 0 0 0 .006.514l3.258 3.185c.143.14.361.125.484-.033l6.272-8.048a.365.365 0 0 0-.063-.512z"/>
                                </svg>
                            </div>
                        </div>

                        <!-- Buttons Section (WhatsApp Pill Buttons) -->
                        <div v-if="normalizedData.buttons && normalizedData.buttons.length > 0" class="mt-1 space-y-1">
                            <div
                                v-for="(btn, idx) in normalizedData.buttons"
                                :key="idx"
                                class="w-full bg-white dark:bg-[#1F2C34] hover:bg-slate-50 dark:hover:bg-[#25323B] transition-colors rounded-xl py-2 px-3 flex items-center justify-center gap-2 text-[#00A5F4] dark:text-[#53BDEB] font-medium text-xs shadow-sm border border-black/5 dark:border-white/5 cursor-pointer select-none"
                            >
                                <!-- URL button -->
                                <svg v-if="btn.type === 'URL'" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" x2="21" y1="14" y2="3"/></svg>
                                <!-- Phone Call button -->
                                <svg v-else-if="btn.type === 'PHONE_NUMBER'" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M20 15.5c-1.25 0-2.45-.2-3.57-.57a1.02 1.02 0 0 0-1.02.24l-2.2 2.2a15.045 15.045 0 0 1-6.59-6.59l2.2-2.21a.96.96 0 0 0 .25-1A11.36 11.36 0 0 1 8.5 4c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1 0 9.39 7.61 17 17 17 .55 0 1-.45 1-1v-3.5c0-.55-.45-1-1-1z"/></svg>
                                <!-- Offer code button -->
                                <svg v-else-if="btn.type === 'COPY_CODE'" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
                                <!-- Quick reply / default -->
                                <svg v-else class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 17 4 12 9 7"/><path d="M20 18v-2a4 4 0 0 0-4-4H4"/></svg>
                                
                                <span class="truncate">{{ btn.text || (btn.type === 'COPY_CODE' ? $t('Copy Code') : $t('Action Button')) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- WhatsApp Fake Composer Bottom Bar -->
                <div class="bg-[#F0F2F5] dark:bg-[#1F2C34] px-3 py-2 flex items-center gap-2 border-t border-black/5 dark:border-white/5 shrink-0">
                    <div class="flex-1 bg-white dark:bg-[#2A3942] rounded-full px-3 py-1.5 text-xs text-slate-400 dark:text-zinc-500">
                        {{ $t('Message...') }}
                    </div>
                    <div class="w-7 h-7 rounded-full bg-[#00A884] flex items-center justify-center text-white shrink-0">
                        <svg class="w-3.5 h-3.5 fill-current ml-0.5" viewBox="0 0 24 24"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
