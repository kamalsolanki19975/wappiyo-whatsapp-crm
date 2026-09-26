<script setup>
    import { Menu, MenuButton, MenuItems } from '@headlessui/vue';
    
    const props = defineProps({
        align: {
            type: String,
            default: "right"
        },
        width: {
            type: String,
            default: "w-48"
        }
    })
</script>
<template>
    <Menu as="div" class="relative inline-block text-left">
        <div>
            <MenuButton as="template">
                <slot />
            </MenuButton>
        </div>

        <transition
            enter-active-class="transition duration-150 ease-out"
            enter-from-class="transform scale-95 opacity-0 -translate-y-1"
            enter-to-class="transform scale-100 opacity-100 translate-y-0"
            leave-active-class="transition duration-100 ease-in"
            leave-from-class="transform scale-100 opacity-100 translate-y-0"
            leave-to-class="transform scale-95 opacity-0 -translate-y-1"
        >
            <MenuItems 
                :class="[
                    {
                        'right-0 origin-top-right' : props.align === 'right',
                        'left-0 origin-top-left' : props.align === 'left',
                        'bottom-full right-0 origin-bottom-right mb-2' : props.align === 'top-right',
                        'bottom-full left-0 origin-bottom-left mb-2' : props.align === 'top-left' || props.align === 'top',
                    },
                    props.width || 'w-48'
                ]"
                class="z-50 absolute mt-1.5 p-1 divide-y divide-slate-100 dark:divide-zinc-800 rounded-xl bg-white dark:bg-[#18181B] shadow-floating border border-slate-200/80 dark:border-zinc-700/80 focus:outline-none">
                <slot name="items" />
            </MenuItems>
        </transition>
    </Menu>
</template>