<script setup>
    import { computed } from 'vue';
    import {
      Listbox,
      ListboxButton,
      ListboxOptions,
      ListboxOption,
    } from '@headlessui/vue'
    import { CheckIcon, ChevronUpDownIcon } from '@heroicons/vue/20/solid';

    const props = defineProps({
      options: {
        type: Array,
        default: () => []
      },
      modelValue: [String, Number, Array],
      name: String,
      className: String,
      optionClassName: String,
      placeholder: {
        type: String, 
        default: 'Select option'
      },
      multiple: Boolean,
      required: Boolean,
      error: String,
    })

    const emit = defineEmits(['update:modelValue'])

    const label = computed(() => {
      if (!props.options) return '';
      return props.options.filter(option => {
        if(Array.isArray(props.modelValue)){
          return props.modelValue.includes(option.value);
        }

        return props.modelValue === option.value;
      }).map(option => option.label).join(', ');
    })
</script>
<template>
    <div :class="className" class="w-full">
        <label v-if="name" :for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-zinc-300 mb-1.5">{{ name }}</label>
        <div>
            <Listbox 
                :multiple="props.multiple"
                @update:modelValue="value => emit('update:modelValue', value)"
                :model-value="props.modelValue">
                <div class="relative">
                    <ListboxButton
                    class="relative w-full cursor-pointer rounded-lg bg-white dark:bg-[#111113] py-2 px-3.5 pr-10 shadow-sm text-left border outline-none text-sm transition-all duration-150"
                    :class="error ? 'border-rose-500 focus:ring-2 focus:ring-rose-500/20' : 'border-slate-300 dark:border-zinc-700 hover:border-slate-400 dark:hover:border-zinc-600 focus:border-[#6C5CE7] focus:ring-2 focus:ring-[#6C5CE7]/20 dark:focus:border-[#8B5CF6] dark:focus:ring-[#8B5CF6]/25'"
                    >
                    <span class="block truncate text-slate-900 dark:text-zinc-100" v-if="label">{{ label }}</span>
                    <span v-else class="text-slate-400 dark:text-zinc-500">{{ props.placeholder }}</span>
                    <span
                        class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2.5 text-slate-400 dark:text-zinc-500"
                    >
                        <ChevronUpDownIcon
                        class="h-4 w-4"
                        aria-hidden="true"
                        />
                    </span>
                    </ListboxButton>

                    <transition
                    leave-active-class="transition duration-100 ease-in"
                    leave-from-class="opacity-100 scale-100"
                    leave-to-class="opacity-0 scale-95"
                    >
                    <ListboxOptions
                        class="z-50 absolute mt-1 max-h-60 w-full overflow-auto rounded-xl bg-white dark:bg-[#18181B] py-1 text-sm shadow-floating border border-slate-200 dark:border-zinc-700 focus:outline-none" :class="optionClassName"
                    >
                        <ListboxOption
                        v-slot="{ active, selected }"
                        v-for="option in props.options"
                        :key="option.label"
                        :value="option.value"
                        as="template"
                        >
                        <li :class="[active ? 'bg-purple-50 dark:bg-purple-950/50 text-[#6C5CE7] dark:text-purple-300' : 'text-slate-900 dark:text-zinc-200','relative cursor-pointer select-none py-2 pl-9 pr-4 transition-colors']">
                            <span :class="[selected ? 'font-semibold text-[#6C5CE7] dark:text-purple-400' : 'font-normal', 'block truncate']">{{ option.label }}</span>
                            <span v-if="selected" class="absolute inset-y-0 left-0 flex items-center pl-2.5 text-[#6C5CE7] dark:text-purple-400">
                                <CheckIcon class="h-4 w-4" aria-hidden="true" />
                            </span>
                        </li>
                        </ListboxOption>
                    </ListboxOptions>
                    </transition>
                </div>
            </Listbox>
        </div>
        <div v-if="error" class="form-error text-rose-500 text-xs mt-1.5 font-medium flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>
            <span>{{ error }}</span>
        </div>
    </div>
</template>