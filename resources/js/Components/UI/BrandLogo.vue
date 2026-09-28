<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { BRAND } from '../../Config/brand';

const props = defineProps({
    variant: {
        type: String,
        default: 'full', // 'full' | 'mark'
        validator: (v) => ['full', 'mark'].includes(v)
    },
    mode: {
        type: String,
        default: 'auto', // 'auto' | 'light' | 'dark'
        validator: (v) => ['auto', 'light', 'dark'].includes(v)
    },
    customLogo: {
        type: String,
        default: null
    },
    companyName: {
        type: String,
        default: 'Wappiyo'
    },
    href: {
        type: String,
        default: null
    },
    imgClass: {
        type: String,
        default: ''
    }
});

const altText = computed(() => props.companyName || 'Wappiyo');

const isCustomTenantLogo = computed(() => {
    if (!props.customLogo) return false;
    const clean = String(props.customLogo).trim().toLowerCase();
    if (clean === '' || clean === '/images/logo.png' || clean === 'images/logo.png' || clean === '/images/logo-dark.png' || clean === 'logo.png') {
        return false;
    }
    return true;
});

const resolvedCustomLogo = computed(() => {
    if (!props.customLogo) return '';
    if (props.customLogo.startsWith('http') || props.customLogo.startsWith('/')) {
        return props.customLogo;
    }
    return '/media/' + props.customLogo;
});
</script>

<template>
    <component 
        :is="href ? Link : 'div'" 
        :href="href"
        class="inline-flex items-center select-none focus:outline-none"
    >
        <!-- Custom Tenant / Organization Logo if configured -->
        <template v-if="isCustomTenantLogo">
            <img 
                :src="resolvedCustomLogo" 
                :alt="altText"
                :class="[
                    variant === 'mark' ? 'h-8 w-8 object-contain' : 'h-8 sm:h-9 w-auto max-w-[160px] object-contain',
                    imgClass
                ]"
            />
        </template>

        <!-- Official Wappiyo Compact Brand Mark (for collapsed sidebars, mobile icons, compact spaces) -->
        <template v-else-if="variant === 'mark'">
            <img 
                :src="BRAND.mark" 
                :alt="altText"
                :class="[
                    'h-8 w-8 object-contain transition-transform duration-200 hover:scale-105',
                    imgClass
                ]"
            />
        </template>

        <!-- Official Wappiyo Full Logo (Mark + Wordmark) -->
        <template v-else>
            <!-- Auto Mode: switch between light & dark using CSS classes -->
            <template v-if="mode === 'auto'">
                <img 
                    :src="BRAND.logo" 
                    :alt="altText"
                    :class="[
                        'h-8 sm:h-9 w-auto max-w-[155px] sm:max-w-[175px] object-contain dark:hidden transition-opacity',
                        imgClass
                    ]"
                />
                <img 
                    :src="BRAND.logoDark" 
                    :alt="altText"
                    :class="[
                        'h-8 sm:h-9 w-auto max-w-[155px] sm:max-w-[175px] object-contain hidden dark:block transition-opacity',
                        imgClass
                    ]"
                />
            </template>

            <!-- Explicit Light Mode -->
            <template v-else-if="mode === 'light'">
                <img 
                    :src="BRAND.logo" 
                    :alt="altText"
                    :class="[
                        'h-8 sm:h-9 w-auto max-w-[155px] sm:max-w-[175px] object-contain',
                        imgClass
                    ]"
                />
            </template>

            <!-- Explicit Dark Mode -->
            <template v-else>
                <img 
                    :src="BRAND.logoDark" 
                    :alt="altText"
                    :class="[
                        'h-8 sm:h-9 w-auto max-w-[155px] sm:max-w-[175px] object-contain',
                        imgClass
                    ]"
                />
            </template>
        </template>
    </component>
</template>
