<script setup>
import { ref } from 'vue';
import Button from '@/Components/UI/Button.vue';
import CallModal from '@/Components/Calling/CallModal.vue';
import { trans } from 'laravel-vue-i18n';

const props = defineProps({
    contact: {
        type: Object,
        default: null,
    },
    phone: {
        type: String,
        default: '',
    },
    variant: {
        type: String,
        default: 'secondary', // primary, secondary, outline, icon, green
    },
    size: {
        type: String,
        default: 'xs', // xs, sm, md
    },
    showLabel: {
        type: Boolean,
        default: true,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
});

const isModalOpen = ref(false);

const openCall = () => {
    if (props.disabled) return;
    isModalOpen.value = true;
};

const closeCall = () => {
    isModalOpen.value = false;
};
</script>

<template>
    <div class="inline-block">
        <!-- Variant: Icon Only or Green Phone Button -->
        <button
            v-if="variant === 'icon' || variant === 'green'"
            type="button"
            @click.stop="openCall"
            :disabled="disabled"
            :class="[
                'rounded-xl flex items-center justify-center transition-all duration-150 focus:outline-none cursor-pointer',
                size === 'xs' ? 'p-1.5' : size === 'sm' ? 'p-2' : 'p-2.5',
                variant === 'green'
                    ? 'bg-emerald-500 hover:bg-emerald-600 text-white shadow-sm shadow-emerald-500/20 active:scale-95'
                    : 'text-slate-600 dark:text-zinc-300 hover:bg-slate-100 dark:hover:bg-zinc-800'
            ]"
            :title="$t('Call via WhatsApp')"
        >
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                <path d="M20.01 15.38c-1.23 0-2.42-.2-3.53-.56a.977.977 0 0 0-1.01.24l-1.57 1.97c-2.83-1.45-5.15-3.76-6.59-6.59l1.97-1.57c.28-.28.37-.68.25-1.02A11.36 11.36 0 0 1 8.96 4c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1 0 9.39 7.61 17 17 17 .55 0 1-.45 1-1v-3.62c0-.55-.45-1-.99-1z"/>
            </svg>
            <span v-if="showLabel" class="ml-1.5 text-xs font-semibold">{{ $t('Call') }}</span>
        </button>

        <!-- Standard Button Component -->
        <Button
            v-else
            type="button"
            :variant="variant"
            :size="size"
            :disabled="disabled"
            @click.stop="openCall"
            class="gap-1.5"
            :title="$t('Call via WhatsApp')"
        >
            <template #icon>
                <svg class="w-3.5 h-3.5 text-emerald-500" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M20.01 15.38c-1.23 0-2.42-.2-3.53-.56a.977.977 0 0 0-1.01.24l-1.57 1.97c-2.83-1.45-5.15-3.76-6.59-6.59l1.97-1.57c.28-.28.37-.68.25-1.02A11.36 11.36 0 0 1 8.96 4c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1 0 9.39 7.61 17 17 17 .55 0 1-.45 1-1v-3.62c0-.55-.45-1-.99-1z"/>
                </svg>
            </template>
            <span v-if="showLabel">{{ $t('Call') }}</span>
        </Button>

        <!-- Self-contained Call Modal -->
        <CallModal
            :is-open="isModalOpen"
            :contact="contact"
            @close="closeCall"
        />
    </div>
</template>
