<script setup>
import { ref } from 'vue';

const props = defineProps({
    content: {
        type: String,
        required: true,
    },
    position: {
        type: String,
        default: 'top', // 'top', 'bottom', 'left', 'right'
    },
    className: {
        type: String,
        default: '',
    },
});

const isVisible = ref(false);

const positionClasses = {
    top: 'bottom-full left-1/2 -translate-x-1/2 mb-2',
    bottom: 'top-full left-1/2 -translate-x-1/2 mt-2',
    left: 'right-full top-1/2 -translate-y-1/2 mr-2',
    right: 'left-full top-1/2 -translate-y-1/2 ml-2',
};
</script>

<template>
    <div
        class="relative inline-flex"
        @mouseenter="isVisible = true"
        @mouseleave="isVisible = false"
        @focusin="isVisible = true"
        @focusout="isVisible = false"
    >
        <slot />

        <transition
            enter-active-class="transition duration-150 ease-out"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition duration-100 ease-in"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
            <div
                v-if="isVisible && content"
                :class="[
                    'absolute z-50 px-2.5 py-1 text-xs font-medium text-white bg-slate-900/95 dark:bg-zinc-800/95 backdrop-blur-md rounded-md shadow-lg whitespace-nowrap pointer-events-none border border-slate-700/50 dark:border-zinc-700/60',
                    positionClasses[position] || positionClasses.top,
                    className
                ]"
            >
                {{ content }}
            </div>
        </transition>
    </div>
</template>
