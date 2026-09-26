<script setup>
import { ref, computed } from 'vue';
import Modal from '@/Components/Modal.vue';
import Button from '@/Components/UI/Button.vue';
import TemplateStatusBadge from '@/Components/Template/TemplateStatusBadge.vue';
import WhatsAppTemplatePreview from '@/Components/Template/WhatsAppTemplatePreview.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    modelValue: Boolean,
    template: {
        type: Object,
        default: null,
    },
});

const emit = defineEmits(['update:modelValue', 'duplicate']);

const showRawJson = ref(false);

const parsedMetadata = computed(() => {
    if (!props.template || !props.template.metadata) return null;
    if (typeof props.template.metadata === 'object') return props.template.metadata;
    try {
        return JSON.parse(props.template.metadata);
    } catch (e) {
        return null;
    }
});

const closeModal = () => {
    emit('update:modelValue', false);
    showRawJson.value = false;
};
</script>

<template>
    <Modal :isOpen="modelValue" @close="closeModal" maxWidth="2xl">
        <div v-if="template" class="p-6">
            <!-- Modal Header -->
            <div class="flex items-start justify-between pb-4 border-b border-slate-200 dark:border-zinc-800">
                <div class="space-y-1">
                    <div class="flex items-center gap-2.5">
                        <h3 class="text-lg font-bold font-mono text-slate-900 dark:text-white">
                            {{ template.name }}
                        </h3>
                        <TemplateStatusBadge :status="template.status" size="xs" />
                    </div>
                    <div class="flex items-center gap-3 text-xs text-slate-500 dark:text-zinc-400">
                        <span class="inline-flex items-center gap-1">
                            <span class="font-semibold text-slate-700 dark:text-zinc-300">{{ $t('Category') }}:</span>
                            {{ template.category }}
                        </span>
                        <span>•</span>
                        <span class="inline-flex items-center gap-1">
                            <span class="font-semibold text-slate-700 dark:text-zinc-300">{{ $t('Language') }}:</span>
                            {{ template.language }}
                        </span>
                        <span v-if="template.updated_at">•</span>
                        <span v-if="template.updated_at" class="text-slate-400 dark:text-zinc-500">
                            {{ $t('Updated') }} {{ template.updated_at }}
                        </span>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        @click="showRawJson = !showRawJson"
                        class="text-xs px-2.5 py-1 rounded-lg border border-slate-200 dark:border-zinc-700 text-slate-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800 transition-colors"
                    >
                        {{ showRawJson ? $t('View Visual') : $t('{ } JSON') }}
                    </button>
                    <button
                        type="button"
                        @click="closeModal"
                        class="p-1 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-zinc-200 hover:bg-slate-100 dark:hover:bg-zinc-800 transition-colors"
                    >
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="py-6">
                <!-- Visual WhatsApp Mockup -->
                <div v-if="!showRawJson" class="flex justify-center">
                    <WhatsAppTemplatePreview
                        :templateData="template"
                        :interactive="true"
                    />
                </div>

                <!-- Raw JSON Inspection -->
                <div v-else class="space-y-2">
                    <div class="text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">
                        {{ $t('Meta WhatsApp Template Payload') }}
                    </div>
                    <pre class="bg-slate-900 text-emerald-400 p-4 rounded-xl text-xs font-mono overflow-x-auto max-h-[460px] leading-relaxed border border-slate-800">{{ JSON.stringify(parsedMetadata, null, 2) }}</pre>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-between pt-4 border-t border-slate-200 dark:border-zinc-800">
                <Button
                    type="button"
                    variant="secondary"
                    size="sm"
                    @click="$emit('duplicate', template)"
                >
                    <template #icon>
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
                    </template>
                    {{ $t('Duplicate') }}
                </Button>

                <div class="flex items-center gap-2">
                    <Button
                        type="button"
                        variant="secondary"
                        size="sm"
                        @click="closeModal"
                    >
                        {{ $t('Close') }}
                    </Button>
                    <Link :href="'/templates/' + template.uuid">
                        <Button
                            type="button"
                            variant="primary"
                            size="sm"
                        >
                            <template #icon>
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                            </template>
                            {{ $t('Edit Template') }}
                        </Button>
                    </Link>
                </div>
            </div>
        </div>
    </Modal>
</template>
