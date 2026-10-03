<script setup>
import { computed } from 'vue';
import Avatar from '@/Components/UI/Avatar.vue';
import { trans } from 'laravel-vue-i18n';

const props = defineProps({
    activeCall: {
        type: Object,
        default: null,
    },
    duration: {
        type: Number,
        default: 0,
    },
    status: {
        type: String,
        default: 'connected',
    },
});

const emit = defineEmits(['openCall', 'endCall']);

const formattedDuration = computed(() => {
    const d = props.duration;
    const mins = Math.floor(d / 60);
    const secs = d % 60;
    return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
});
</script>

<template>
    <div
        v-if="activeCall && ['initiating', 'ringing', 'connecting', 'connected'].includes(status)"
        class="fixed bottom-6 right-6 z-40 flex items-center gap-3 px-4 py-3 rounded-2xl bg-slate-900/95 dark:bg-[#18181B]/95 text-white shadow-2xl border border-slate-700/60 dark:border-zinc-700 backdrop-blur-md animate-slide-up"
    >
        <!-- Pulsing green indicator -->
        <div class="relative flex items-center justify-center">
            <span class="w-3 h-3 rounded-full bg-emerald-500 animate-ping absolute" />
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500" />
        </div>

        <!-- Contact Avatar & Details -->
        <div class="flex items-center gap-2.5 min-w-0">
            <Avatar
                :src="activeCall.contact?.avatar || null"
                :name="activeCall.contact?.full_name || 'Contact'"
                size="sm"
            />
            <div class="min-w-0 pr-1">
                <p class="text-xs font-bold truncate max-w-[140px] text-white">
                    {{ activeCall.contact?.full_name || activeCall.customer_phone }}
                </p>
                <div class="text-[10px] font-mono text-emerald-400 font-semibold flex items-center gap-1.5">
                    <span>{{ status === 'connected' ? formattedDuration : $t(status) }}</span>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-1.5 ml-2">
            <!-- Open Call Window Button -->
            <button
                type="button"
                @click="emit('openCall')"
                class="px-2.5 py-1 rounded-xl bg-white/10 hover:bg-white/20 text-xs font-semibold transition-colors cursor-pointer"
            >
                {{ $t('Open') }}
            </button>

            <!-- End Call Button (Red) -->
            <button
                type="button"
                @click="emit('endCall')"
                class="p-1.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white transition-colors cursor-pointer"
                :title="$t('End Call')"
            >
                <svg class="w-3.5 h-3.5 rotate-[135deg]" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M20.01 15.38c-1.23 0-2.42-.2-3.53-.56a.977.977 0 0 0-1.01.24l-1.57 1.97c-2.83-1.45-5.15-3.76-6.59-6.59l1.97-1.57c.28-.28.37-.68.25-1.02A11.36 11.36 0 0 1 8.96 4c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1 0 9.39 7.61 17 17 17 .55 0 1-.45 1-1v-3.62c0-.55-.45-1-.99-1z"/>
                </svg>
            </button>
        </div>
    </div>
</template>
