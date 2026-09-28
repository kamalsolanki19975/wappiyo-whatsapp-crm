<template>
    <div :class="className" class="w-full relative" ref="containerRef">
        <!-- Label -->
        <label v-if="name" :for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-zinc-300 mb-1.5" :class="labelClass">
            {{ name }}
            <span v-if="required" class="text-rose-500">*</span>
        </label>

        <!-- Main Combined Input Container -->
        <div
            class="relative flex items-center w-full rounded-xl border bg-white dark:bg-[#111113] shadow-xs transition-all duration-150"
            :class="[
                error || (showValidationHint && !indianCheck.valid)
                    ? 'border-rose-500 ring-2 ring-rose-500/20'
                    : isFocused
                        ? 'border-[#6C5CE7] dark:border-[#8B5CF6] ring-2 ring-[#6C5CE7]/20 dark:ring-[#8B5CF6]/25'
                        : 'border-slate-300 dark:border-zinc-700 hover:border-slate-400 dark:hover:border-zinc-600',
                disabled ? 'opacity-60 cursor-not-allowed bg-slate-50 dark:bg-zinc-900' : ''
            ]"
        >
            <!-- Country Selector Button (Split Experience) -->
            <button
                type="button"
                :id="name ? `${name}-country-btn` : 'phone-country-btn'"
                @click="toggleDropdown"
                :disabled="disabled"
                aria-haspopup="listbox"
                :aria-expanded="isDropdownOpen"
                class="flex items-center gap-1.5 px-3 py-2.5 rounded-l-xl bg-slate-50 dark:bg-zinc-800/80 hover:bg-slate-100 dark:hover:bg-zinc-700 text-slate-800 dark:text-zinc-100 text-xs sm:text-sm font-semibold border-r border-slate-300 dark:border-zinc-700 transition-colors shrink-0 cursor-pointer select-none focus:outline-none"
                :title="`${selectedCountry.name} (${selectedCountry.dialCode})`"
            >
                <span class="text-base leading-none">{{ selectedCountry.flag }}</span>
                <span class="font-mono text-xs sm:text-sm font-bold tracking-tight text-slate-900 dark:text-white">
                    {{ selectedCountry.dialCode }}
                </span>
                <svg
                    class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200"
                    :class="{ 'rotate-180': isDropdownOpen }"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                >
                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                </svg>
            </button>

            <!-- Phone Number Input -->
            <input
                ref="inputRef"
                :id="name || 'phone-input'"
                type="tel"
                :value="displayLocalNumber"
                @input="handleInput"
                @paste="handlePaste"
                @focus="isFocused = true; showValidationHint = true"
                @blur="isFocused = false"
                :placeholder="selectedCountry.code === 'IN' ? '98765 43210' : (placeholder || $t('Enter phone number'))"
                :disabled="disabled"
                :required="required"
                autocomplete="tel-national"
                class="flex-1 bg-transparent px-3.5 py-2 text-sm text-slate-900 dark:text-zinc-100 placeholder:text-slate-400 dark:placeholder:text-zinc-500 outline-none w-full"
            />

            <!-- Clear Button -->
            <button
                v-if="rawDigits && !disabled"
                type="button"
                @click="clearNumber"
                class="pr-3 text-slate-400 hover:text-slate-600 dark:hover:text-zinc-200 text-xs"
                title="Clear"
            >
                &times;
            </button>
        </div>

        <!-- Country Selection Dropdown Menu -->
        <transition
            enter-active-class="transition duration-150 ease-out"
            enter-from-class="transform scale-95 opacity-0 -translate-y-1"
            enter-to-class="transform scale-100 opacity-100 translate-y-0"
            leave-active-class="transition duration-100 ease-in"
            leave-from-class="transform scale-100 opacity-100 translate-y-0"
            leave-to-class="transform scale-95 opacity-0 -translate-y-1"
        >
            <div
                v-if="isDropdownOpen"
                class="z-50 absolute left-0 top-full mt-1.5 w-80 max-w-[90vw] rounded-2xl bg-white dark:bg-[#18181B] border border-slate-200 dark:border-zinc-700 shadow-xl overflow-hidden focus:outline-none"
            >
                <!-- Search Box -->
                <div class="p-2.5 border-b border-slate-100 dark:border-zinc-800 bg-slate-50/70 dark:bg-zinc-900/60">
                    <div class="relative flex items-center">
                        <svg class="w-4 h-4 absolute left-2.5 text-slate-400 dark:text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input
                            ref="searchRef"
                            v-model="searchQuery"
                            type="text"
                            :placeholder="$t('Search country or dial code...')"
                            class="w-full pl-8 pr-3 py-1.5 rounded-xl border border-slate-200 dark:border-zinc-700 bg-white dark:bg-[#111113] text-xs text-slate-900 dark:text-zinc-100 placeholder:text-slate-400 dark:placeholder:text-zinc-500 outline-none focus:ring-1 focus:ring-primary"
                        />
                    </div>
                </div>

                <!-- Country List -->
                <ul class="max-h-60 overflow-y-auto divide-y divide-slate-100/60 dark:divide-zinc-800/60 text-xs py-1" role="listbox">
                    <li
                        v-for="c in filteredCountries"
                        :key="c.code + c.dialCode"
                        @click="selectCountry(c)"
                        class="flex items-center justify-between px-3 py-2 cursor-pointer transition-colors select-none"
                        :class="[
                            selectedCountry.code === c.code && selectedCountry.dialCode === c.dialCode
                                ? 'bg-primary/10 text-primary dark:bg-primary/20 dark:text-purple-300 font-bold'
                                : 'hover:bg-slate-50 dark:hover:bg-zinc-800/60 text-slate-700 dark:text-zinc-200'
                        ]"
                        role="option"
                        :aria-selected="selectedCountry.code === c.code"
                    >
                        <div class="flex items-center gap-2.5 truncate">
                            <span class="text-base leading-none">{{ c.flag }}</span>
                            <span class="truncate">{{ c.name }}</span>
                            <span v-if="c.code === 'IN'" class="text-[10px] px-1 rounded bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 font-bold">Default</span>
                        </div>
                        <span class="font-mono text-xs text-slate-400 dark:text-zinc-400 shrink-0 ml-2">
                            {{ c.dialCode }}
                        </span>
                    </li>

                    <li v-if="filteredCountries.length === 0" class="p-4 text-center text-slate-400 text-xs">
                        {{ $t('No country found.') }}
                    </li>
                </ul>
            </div>
        </transition>

        <!-- Inline Validation & Hint for Indian numbers -->
        <div v-if="showIndianHint" class="text-amber-600 dark:text-amber-400 text-xs mt-1.5 flex items-center gap-1 font-medium">
            <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <span>{{ indianCheck.message }}</span>
        </div>

        <!-- Server Error Message -->
        <div v-if="error" class="form-error text-rose-500 text-xs mt-1.5 font-medium flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>
            <span>{{ error }}</span>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted, nextTick } from 'vue';
import {
    defaultCountry,
    countries,
    parsePhoneNumber,
    formatIndianNumber,
    validateIndianNumber,
} from '@/Utils/countryData';

const props = defineProps({
    modelValue: [String, Number],
    name: String,
    placeholder: String,
    className: String,
    labelClass: String,
    required: Boolean,
    error: String,
    disabled: Boolean,
});

const emit = defineEmits(['update:modelValue']);

const containerRef = ref(null);
const inputRef = ref(null);
const searchRef = ref(null);

const isFocused = ref(false);
const isDropdownOpen = ref(false);
const searchQuery = ref('');
const showValidationHint = ref(false);

// State: selected country & raw local number digits
const selectedCountry = ref(defaultCountry);
const rawDigits = ref('');

// Computed filtered countries based on search
const filteredCountries = computed(() => {
    if (!searchQuery.value.trim()) return countries;
    const q = searchQuery.value.toLowerCase().trim();
    return countries.filter(c =>
        c.name.toLowerCase().includes(q) ||
        c.dialCode.toLowerCase().includes(q) ||
        c.code.toLowerCase().includes(q)
    );
});

// Display format for Indian numbers (98765 43210) vs other countries
const displayLocalNumber = computed(() => {
    if (selectedCountry.value.code === 'IN') {
        return formatIndianNumber(rawDigits.value);
    }
    return rawDigits.value;
});

// Indian mobile validation check
const indianCheck = computed(() => {
    if (selectedCountry.value.code !== 'IN') return { valid: true };
    return validateIndianNumber(rawDigits.value);
});

const showIndianHint = computed(() => {
    return (
        selectedCountry.value.code === 'IN' &&
        showValidationHint.value &&
        rawDigits.value.length > 0 &&
        !indianCheck.value.valid &&
        !props.error
    );
});

// Emit normalized E.164 string to parent (e.g. "+919876543210")
const emitNormalized = () => {
    if (!rawDigits.value) {
        emit('update:modelValue', '');
        return;
    }

    let clean = rawDigits.value.replace(/\D/g, '');

    // For Indian numbers, clean up accidental duplicate 91 or leading 0
    if (selectedCountry.value.code === 'IN') {
        if (clean.length === 12 && clean.startsWith('91')) {
            clean = clean.slice(2);
        } else if (clean.length === 11 && clean.startsWith('0')) {
            clean = clean.slice(1);
        }
    }

    const normalized = `${selectedCountry.value.dialCode}${clean}`;
    emit('update:modelValue', normalized);
};

// Handle manual keystrokes
const handleInput = (event) => {
    let val = event.target.value;

    // Check if the user typed or pasted something starting with +
    if (val.startsWith('+')) {
        const parsed = parsePhoneNumber(val);
        selectedCountry.value = parsed.country;
        rawDigits.value = parsed.localNumber;
    } else {
        // Strip non-digits
        let digits = val.replace(/\D/g, '');
        // If Indian number, cap at 10 digits
        if (selectedCountry.value.code === 'IN' && digits.length > 10) {
            digits = digits.slice(0, 10);
        }
        rawDigits.value = digits;
    }

    emitNormalized();
};

// Handle paste event specifically for numbers with +91, hyphens, spaces, etc.
const handlePaste = (event) => {
    event.preventDefault();
    const pasted = (event.clipboardData || window.clipboardData).getData('text') || '';
    if (!pasted) return;

    const parsed = parsePhoneNumber(pasted);
    selectedCountry.value = parsed.country;
    let digits = parsed.localNumber;

    if (selectedCountry.value.code === 'IN' && digits.length > 10) {
        digits = digits.slice(0, 10);
    }

    rawDigits.value = digits;
    emitNormalized();
};

const selectCountry = (country) => {
    selectedCountry.value = country;
    isDropdownOpen.value = false;
    searchQuery.value = '';
    emitNormalized();
    nextTick(() => {
        inputRef.value?.focus();
    });
};

const toggleDropdown = () => {
    if (props.disabled) return;
    isDropdownOpen.value = !isDropdownOpen.value;
    if (isDropdownOpen.value) {
        searchQuery.value = '';
        nextTick(() => {
            searchRef.value?.focus();
        });
    }
};

const clearNumber = () => {
    rawDigits.value = '';
    emitNormalized();
    nextTick(() => {
        inputRef.value?.focus();
    });
};

// Close dropdown on outside click
const handleClickOutside = (e) => {
    if (containerRef.value && !containerRef.value.contains(e.target)) {
        isDropdownOpen.value = false;
    }
};

// Close dropdown on Escape key
const handleKeyDown = (e) => {
    if (e.key === 'Escape' && isDropdownOpen.value) {
        isDropdownOpen.value = false;
    }
};

// Sync internal state when props.modelValue changes (e.g. form initialization or edit)
const syncFromModelValue = (val) => {
    if (!val) {
        // Keep default country as India (+91)
        if (!rawDigits.value) {
            selectedCountry.value = defaultCountry;
        }
        rawDigits.value = '';
        return;
    }

    const parsed = parsePhoneNumber(val);
    selectedCountry.value = parsed.country;
    rawDigits.value = parsed.localNumber;
};

watch(
    () => props.modelValue,
    (newVal) => {
        const currentE164 = rawDigits.value ? `${selectedCountry.value.dialCode}${rawDigits.value.replace(/\D/g, '')}` : '';
        if (newVal !== currentE164) {
            syncFromModelValue(newVal);
        }
    },
    { immediate: true }
);

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
    document.addEventListener('keydown', handleKeyDown);

    // Initial check: if modelValue is empty on mount, India (+91) is already set as default
    if (props.modelValue) {
        syncFromModelValue(props.modelValue);
    }
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
    document.removeEventListener('keydown', handleKeyDown);
});
</script>