<script setup>
    import { MenuItem } from '@headlessui/vue';
    import { Link } from "@inertiajs/vue3";
    
    const props = defineProps({
        as: {
            type: String,
            default: "a"
        }
    })
</script>
<template>
    <MenuItem v-slot="{ active }">
        <component v-if="props.as != 'a'"
            :is="props.as"
            :class="[
            active ? 'bg-purple-50 dark:bg-purple-950/60 text-[#6C5CE7] dark:text-purple-300 font-medium' : 'text-slate-700 dark:text-zinc-300',
            'group flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-left transition-colors cursor-pointer',
            ]"
        >
            <slot />
        </component>
        <Link v-else
            :is="props.as"
            :class="[
            active ? 'bg-purple-50 dark:bg-purple-950/60 text-[#6C5CE7] dark:text-purple-300 font-medium' : 'text-slate-700 dark:text-zinc-300',
            'group flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-sm transition-colors cursor-pointer',
            ]"
        >
            <slot />
        </Link>
    </MenuItem>
</template>