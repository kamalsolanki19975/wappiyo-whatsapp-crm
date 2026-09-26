<template>
    <div class="h-screen w-screen overflow-hidden flex flex-col bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-white select-none">
        <!-- Top Toolbar -->
        <WorkflowToolbar
            :automationName="automationName"
            :status="status"
            :isSaving="isSaving"
            :hasUnsavedChanges="hasUnsavedChanges"
            :validationErrors="validationErrors"
            :showMinimap="showMinimap"
            :zoomLevel="zoomLevel"
            @update:automationName="onNameChange"
            @update:status="onStatusChange"
            @save="saveWorkflow"
            @test="isTestModalOpen = true"
            @zoomIn="onZoomIn"
            @zoomOut="onZoomOut"
            @zoomFit="onZoomFit"
            @zoomReset="onZoomReset"
            @toggleMinimap="showMinimap = !showMinimap"
            @togglePalette="isPaletteOpen = !isPaletteOpen"
            @validate="isValidationModalOpen = true"
            @back="handleBack"
        />

        <!-- Main Workspace Area -->
        <div class="flex-1 flex overflow-hidden relative">
            <!-- Left: Node Palette Drawer -->
            <WorkflowNodePalette
                v-show="isPaletteOpen"
                @addNode="addNodeToCanvas"
            />

            <!-- Center: Vue Flow Canvas -->
            <div
                class="flex-1 h-full relative"
                @drop="onDrop"
                @dragover.prevent
            >
                <VueFlow
                    v-model:nodes="nodes"
                    v-model:edges="edges"
                    :node-types="nodeTypes"
                    :default-viewport="{ zoom: 1, x: 100, y: 50 }"
                    :min-zoom="0.2"
                    :max-zoom="4"
                    :fit-view-on-init="true"
                    class="h-full w-full bg-slate-50 dark:bg-[#0B0F19]"
                    @node-click="onNodeClick"
                    @pane-click="onPaneClick"
                    @connect="onConnect"
                    @viewport-change="onViewportChange"
                >
                    <!-- Background Pattern -->
                    <Background
                        :gap="20"
                        :size="1.5"
                        pattern-color="#94a3b8"
                        class="opacity-30 dark:opacity-15"
                    />

                    <!-- Controls -->
                    <Controls position="bottom-left" />

                    <!-- Minimap -->
                    <MiniMap
                        v-if="showMinimap"
                        position="bottom-right"
                        :node-stroke-width="3"
                        :node-color="getMinimapNodeColor"
                        class="!bg-white/80 dark:!bg-slate-900/80 !border !border-slate-200 dark:!border-white/10 !rounded-xl !shadow-lg"
                    />
                </VueFlow>

                <!-- Floating Canvas Actions / Quick Add bar -->
                <div class="absolute bottom-6 left-1/2 -translate-x-1/2 z-10 flex items-center gap-1.5 p-1.5 rounded-2xl bg-white/90 dark:bg-slate-900/90 backdrop-blur-md border border-slate-200/80 dark:border-white/10 shadow-xl">
                    <button
                        type="button"
                        @click="addQuickNode('action')"
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-white/5 text-xs font-semibold text-slate-700 dark:text-slate-200 transition"
                    >
                        <span class="w-2 h-2 rounded-full bg-primary"></span>
                        <span>{{ $t('+ Message') }}</span>
                    </button>

                    <button
                        type="button"
                        @click="addQuickNode('condition')"
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-white/5 text-xs font-semibold text-slate-700 dark:text-slate-200 transition"
                    >
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        <span>{{ $t('+ Condition') }}</span>
                    </button>

                    <button
                        type="button"
                        @click="addQuickNode('variable')"
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-white/5 text-xs font-semibold text-slate-700 dark:text-slate-200 transition"
                    >
                        <span class="w-2 h-2 rounded-full bg-cyan-500"></span>
                        <span>{{ $t('+ Variable') }}</span>
                    </button>

                    <button
                        type="button"
                        @click="addQuickNode('llm')"
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-white/5 text-xs font-semibold text-slate-700 dark:text-slate-200 transition"
                    >
                        <span class="w-2 h-2 rounded-full bg-fuchsia-500"></span>
                        <span>{{ $t('+ AI') }}</span>
                    </button>
                </div>
            </div>

            <!-- Right: Node Inspector Drawer -->
            <WorkflowNodeInspector
                v-if="selectedNode"
                :selectedNode="selectedNode"
                :templates="templates"
                :placeholders="placeholders"
                @close="selectedNode = null"
                @deleteNode="deleteNodeById"
                @updateNodeData="onUpdateNodeData"
            />
        </div>

        <!-- Validation Modal -->
        <WorkflowValidation
            :isOpen="isValidationModalOpen"
            :errors="validationErrors"
            @close="isValidationModalOpen = false"
            @selectNode="selectNodeById"
        />

        <!-- Test Workflow Simulation Modal -->
        <WorkflowTestModal
            :isOpen="isTestModalOpen"
            :contacts="contacts"
            :triggerPhrases="startNodeTrigger"
            :matchCriteria="startNodeCriteria"
            :primaryResponse="primaryActionResponse"
            :responseType="primaryActionResponseType"
            @close="isTestModalOpen = false"
        />
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, markRaw } from 'vue';
import { router } from '@inertiajs/vue3';
import { VueFlow, useVueFlow, MarkerType } from '@vue-flow/core';
import { Background } from '@vue-flow/background';
import { Controls } from '@vue-flow/controls';
import { MiniMap } from '@vue-flow/minimap';
import axios from 'axios';
import { trans } from 'laravel-vue-i18n';

// Import Vue Flow stylesheets
import '@vue-flow/core/dist/style.css';
import '@vue-flow/core/dist/theme-default.css';
import '@vue-flow/controls/dist/style.css';
import '@vue-flow/minimap/dist/style.css';

// Subcomponents
import WorkflowToolbar from './WorkflowToolbar.vue';
import WorkflowNodePalette from './WorkflowNodePalette.vue';
import WorkflowNodeInspector from './WorkflowNodeInspector.vue';
import WorkflowValidation from './WorkflowValidation.vue';
import WorkflowTestModal from './WorkflowTestModal.vue';

// Custom Node Components
import StartNode from './Nodes/StartNode.vue';
import ActionNode from './Nodes/ActionNode.vue';
import ConditionNode from './Nodes/ConditionNode.vue';
import VariableNode from './Nodes/VariableNode.vue';
import LLMNode from './Nodes/LLMNode.vue';
import KnowledgeNode from './Nodes/KnowledgeNode.vue';
import EndNode from './Nodes/EndNode.vue';

const props = defineProps({
    title: { type: String, default: 'Workflow Builder' },
    autoreply: { type: Object, required: true },
    placeholders: { type: Array, default: () => [] },
    templates: { type: Array, default: () => [] },
    contacts: { type: Array, default: () => [] },
});

// Register Node Types
const nodeTypes = {
    start: markRaw(StartNode),
    action: markRaw(ActionNode),
    condition: markRaw(ConditionNode),
    variable: markRaw(VariableNode),
    llm: markRaw(LLMNode),
    knowledge: markRaw(KnowledgeNode),
    end: markRaw(EndNode),
};

const { project, zoomIn, zoomOut, fitView, setViewport, getViewport } = useVueFlow();

// State
const nodes = ref([]);
const edges = ref([]);
const selectedNode = ref(null);
const isPaletteOpen = ref(true);
const showMinimap = ref(true);
const isValidationModalOpen = ref(false);
const isTestModalOpen = ref(false);
const isSaving = ref(false);
const hasUnsavedChanges = ref(false);
const zoomLevel = ref(1);

const parsedMetadata = computed(() => {
    try {
        return typeof props.autoreply.metadata === 'string'
            ? JSON.parse(props.autoreply.metadata)
            : (props.autoreply.metadata || {});
    } catch (e) {
        return {};
    }
});

const automationName = ref(props.autoreply.name || 'Untitled Automation');
const status = ref(parsedMetadata.value?.status || 'active');

// Edge styling preset
const edgeStylePreset = {
    type: 'smoothstep',
    animated: false,
    style: { stroke: '#6C5CE7', strokeWidth: 2 },
    markerEnd: MarkerType.ArrowClosed,
};

// Initialize workflow elements
onMounted(() => {
    const savedWorkflow = parsedMetadata.value?.workflow;

    if (savedWorkflow?.nodes && savedWorkflow.nodes.length > 0) {
        // Load saved workflow
        nodes.value = savedWorkflow.nodes;
        edges.value = (savedWorkflow.edges || []).map(e => ({
            ...edgeStylePreset,
            ...e,
        }));
    } else {
        // Initialize intuitive default flow: Start -> Action -> End
        const triggerText = props.autoreply.trigger || 'hi, hello, menu';
        const matchCrit = props.autoreply.match_criteria || 'contains';
        const respType = parsedMetadata.value?.type || 'text';
        let respContent = '';
        if (respType === 'text') {
            respContent = parsedMetadata.value?.data?.text || 'Hello! Thank you for contacting us. How can we help you today?';
        } else if (respType === 'template') {
            respContent = parsedMetadata.value?.data?.template || '';
        }

        nodes.value = [
            {
                id: 'start-1',
                type: 'start',
                position: { x: 260, y: 60 },
                data: {
                    title: 'Incoming WhatsApp Message',
                    trigger: triggerText,
                    matchCriteria: matchCrit,
                },
            },
            {
                id: 'action-1',
                type: 'action',
                position: { x: 260, y: 240 },
                data: {
                    title: 'Send Response',
                    responseType: respType,
                    response: respContent,
                    templateName: respType === 'template' ? respContent : '',
                },
            },
            {
                id: 'end-1',
                type: 'end',
                position: { x: 280, y: 440 },
                data: {
                    title: 'End Flow',
                },
            },
        ];

        edges.value = [
            {
                id: 'e-start-action',
                source: 'start-1',
                target: 'action-1',
                ...edgeStylePreset,
            },
            {
                id: 'e-action-end',
                source: 'action-1',
                target: 'end-1',
                ...edgeStylePreset,
            },
        ];
    }

    window.addEventListener('keydown', onKeyDown);
});

onUnmounted(() => {
    window.removeEventListener('keydown', onKeyDown);
});

// Keyboard shortcuts: Cmd+S / Ctrl+S to save, Backspace / Delete to delete selected node
const onKeyDown = (e) => {
    if ((e.metaKey || e.ctrlKey) && e.key === 's') {
        e.preventDefault();
        saveWorkflow();
    }
    if ((e.key === 'Delete' || e.key === 'Backspace') && selectedNode.value) {
        // Only if not focused on an input or textarea
        const activeTag = document.activeElement?.tagName?.toLowerCase();
        if (activeTag !== 'input' && activeTag !== 'textarea' && activeTag !== 'select') {
            e.preventDefault();
            deleteNodeById(selectedNode.value.id);
        }
    }
};

// Canvas events
const onNodeClick = ({ node }) => {
    selectedNode.value = node;
};

const onPaneClick = () => {
    selectedNode.value = null;
};

const onConnect = (params) => {
    hasUnsavedChanges.value = true;
    edges.value.push({
        id: `e-${params.source}-${params.target}-${Date.now()}`,
        ...edgeStylePreset,
        ...params,
    });
};

const onViewportChange = (vp) => {
    if (vp?.zoom) {
        zoomLevel.value = vp.zoom;
    }
};

// Drag and drop onto canvas
const onDrop = (event) => {
    event.preventDefault();
    const dataStr = event.dataTransfer?.getData('application/vueflow');
    if (!dataStr) return;

    try {
        const item = JSON.parse(dataStr);
        const bounds = event.currentTarget.getBoundingClientRect();
        const position = project({
            x: event.clientX - bounds.left,
            y: event.clientY - bounds.top,
        });

        const newNode = {
            id: `${item.type}-${Date.now()}`,
            type: item.type,
            position,
            data: { ...item.defaultData },
        };

        nodes.value.push(newNode);
        selectedNode.value = newNode;
        hasUnsavedChanges.value = true;
    } catch (e) {
        console.error('Failed to drop node', e);
    }
};

// Add node directly from palette or quick bar
const addNodeToCanvas = (item) => {
    const vp = getViewport ? getViewport() : { x: 0, y: 0, zoom: 1 };
    const position = {
        x: Math.abs(vp.x) + 280,
        y: Math.abs(vp.y) + 200,
    };

    const newNode = {
        id: `${item.type}-${Date.now()}`,
        type: item.type,
        position,
        data: { ...item.defaultData },
    };

    nodes.value.push(newNode);
    selectedNode.value = newNode;
    hasUnsavedChanges.value = true;
};

const addQuickNode = (type) => {
    const defaults = {
        action: {
            type: 'action',
            defaultData: {
                title: 'Send Message',
                responseType: 'text',
                response: 'Thank you for messaging us!',
            },
        },
        condition: {
            type: 'condition',
            defaultData: {
                title: 'Check Condition',
                field: 'message',
                operator: 'contains',
                value: 'pricing',
            },
        },
        variable: {
            type: 'variable',
            defaultData: {
                title: 'Set Variable',
                variableName: 'customer_status',
                variableValue: 'active',
            },
        },
        llm: {
            type: 'llm',
            defaultData: {
                title: 'AI Assistant',
                model: 'GPT-4o',
                prompt: 'Assist the user with their inquiry.',
            },
        },
    };

    if (defaults[type]) {
        addNodeToCanvas(defaults[type]);
    }
};

const deleteNodeById = (nodeId) => {
    nodes.value = nodes.value.filter(n => n.id !== nodeId);
    edges.value = edges.value.filter(e => e.source !== nodeId && e.target !== nodeId);
    if (selectedNode.value?.id === nodeId) {
        selectedNode.value = null;
    }
    hasUnsavedChanges.value = true;
};

const selectNodeById = (nodeId) => {
    const found = nodes.value.find(n => n.id === nodeId);
    if (found) {
        selectedNode.value = found;
        isValidationModalOpen.value = false;
    }
};

const onUpdateNodeData = ({ id, data }) => {
    const target = nodes.value.find(n => n.id === id);
    if (target) {
        target.data = { ...target.data, ...data };
        hasUnsavedChanges.value = true;
    }
};

// Top Toolbar Handlers
const onNameChange = (val) => {
    automationName.value = val;
    hasUnsavedChanges.value = true;
};

const onStatusChange = (val) => {
    status.value = val;
    hasUnsavedChanges.value = true;
};

const onZoomIn = () => zoomIn();
const onZoomOut = () => zoomOut();
const onZoomFit = () => fitView({ padding: 0.2 });
const onZoomReset = () => {
    setViewport({ x: 100, y: 50, zoom: 1 });
};

const handleBack = () => {
    if (hasUnsavedChanges.value) {
        if (!confirm(trans('You have unsaved workflow changes. Are you sure you want to leave?'))) {
            return;
        }
    }
    router.visit('/automation/basic');
};

// Computed references for test modal and backend sync
const startNode = computed(() => {
    return nodes.value.find(n => n.type === 'start') || null;
});

const startNodeTrigger = computed(() => {
    return startNode.value?.data?.trigger || props.autoreply.trigger || '';
});

const startNodeCriteria = computed(() => {
    return startNode.value?.data?.matchCriteria || props.autoreply.match_criteria || 'contains';
});

const firstActionNode = computed(() => {
    return nodes.value.find(n => n.type === 'action') || null;
});

const primaryActionResponse = computed(() => {
    return firstActionNode.value?.data?.response || '';
});

const primaryActionResponseType = computed(() => {
    return firstActionNode.value?.data?.responseType || 'text';
});

// Graph Validation
const validationErrors = computed(() => {
    const errs = [];

    // Check 1: Start node exists
    const starts = nodes.value.filter(n => n.type === 'start');
    if (starts.length === 0) {
        errs.push({
            nodeId: null,
            nodeTitle: null,
            message: trans('Workflow must contain at least one Trigger / Start node.'),
        });
    } else if (starts.length > 1) {
        errs.push({
            nodeId: starts[1].id,
            nodeTitle: starts[1].data?.title,
            message: trans('Only one Trigger node is permitted per workflow.'),
        });
    } else {
        if (!starts[0].data?.trigger || !starts[0].data.trigger.trim()) {
            errs.push({
                nodeId: starts[0].id,
                nodeTitle: starts[0].data?.title || 'Trigger Node',
                message: trans('Trigger node requires at least one keyword or phrase.'),
            });
        }
    }

    // Check 2: Action nodes have content
    nodes.value.forEach(node => {
        if (node.type === 'action') {
            if (node.data?.responseType === 'template' && !node.data.response) {
                errs.push({
                    nodeId: node.id,
                    nodeTitle: node.data?.title || 'Action Node',
                    message: trans('WhatsApp Template action is missing a selected template.'),
                });
            } else if (node.data?.responseType === 'text' && !node.data.response) {
                errs.push({
                    nodeId: node.id,
                    nodeTitle: node.data?.title || 'Action Node',
                    message: trans('Message action requires text content.'),
                });
            }
        } else if (node.type === 'condition') {
            if (!node.data?.value && node.data?.operator !== 'not_empty') {
                errs.push({
                    nodeId: node.id,
                    nodeTitle: node.data?.title || 'Condition Node',
                    message: trans('Condition node requires a comparison value.'),
                });
            }
        }
    });

    return errs;
});

// Minimap Node coloring
const getMinimapNodeColor = (node) => {
    switch (node.type) {
        case 'start': return '#22C55E';
        case 'action': return '#6C5CE7';
        case 'condition': return '#F59E0B';
        case 'variable': return '#06B6D4';
        case 'llm': return '#EC4899';
        case 'knowledge': return '#3B82F6';
        case 'end': return '#64748B';
        default: return '#6C5CE7';
    }
};

// Save Workflow
const saveWorkflow = async () => {
    isSaving.value = true;
    try {
        const payload = {
            name: automationName.value,
            status: status.value,
            trigger: startNodeTrigger.value,
            match_criteria: startNodeCriteria.value,
            response_type: primaryActionResponseType.value,
            response: primaryActionResponse.value,
            workflow: {
                nodes: nodes.value,
                edges: edges.value,
                updated_at: new Date().toISOString(),
            },
        };

        const res = await axios.post(`/automation/builder/${props.autoreply.uuid}/save`, payload);
        if (res.data?.success) {
            hasUnsavedChanges.value = false;
        }
    } catch (err) {
        console.error('Failed to save workflow', err);
        alert(trans('Error saving workflow. Please try again.'));
    } finally {
        isSaving.value = false;
    }
};
</script>
