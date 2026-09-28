<script setup>
import { computed, ref } from 'vue';
import { router } from "@inertiajs/vue3";
import AppLayout from "./Layout/App.vue";
import DashboardHero from "./DashboardComponents/DashboardHero.vue";
import DashboardKpis from "./DashboardComponents/DashboardKpis.vue";
import DashboardAnalytics from "./DashboardComponents/DashboardAnalytics.vue";
import DashboardWhatsAppStatus from "./DashboardComponents/DashboardWhatsAppStatus.vue";
import DashboardCampaigns from "./DashboardComponents/DashboardCampaigns.vue";
import DashboardSubscription from "./DashboardComponents/DashboardSubscription.vue";
import DashboardQuickActions from "./DashboardComponents/DashboardQuickActions.vue";
import DashboardAiCard from "./DashboardComponents/DashboardAiCard.vue";
import DashboardTeamBanner from "./DashboardComponents/DashboardTeamBanner.vue";
import DashboardOnboardingChecklist from "./DashboardComponents/DashboardOnboardingChecklist.vue";

const props = defineProps({ 
    user: Object, 
    auth: Object, 
    subscription: Object, 
    subscriptionIsActive: Boolean, 
    subscriptionDetails: Object, 
    chatCount: Number, 
    contactCount: Number, 
    campaignCount: Number, 
    templateCount: Number, 
    setupWhatsapp: Boolean, 
    organization: Object, 
    campaigns: [Object, Array], 
    period: [Object, Array], 
    inbound: [Object, Array], 
    outbound: [Object, Array],
    embeddedSignupActive: Number,
    appId: String,
    configId: String,
    graphAPIVersion: String,
    onboardingState: {
        type: Object,
        default: () => ({}),
    },
});

const isOwner = computed(() => {
    return props.auth?.user?.teams?.[0]?.role === 'owner';
});

const campaignsList = computed(() => {
    if (!props.campaigns) return [];
    if (Array.isArray(props.campaigns)) return props.campaigns;
    if (props.campaigns.data && Array.isArray(props.campaigns.data)) return props.campaigns.data;
    return Object.values(props.campaigns);
});

const periodList = computed(() => {
    if (!props.period) return [];
    return Array.isArray(props.period) ? props.period : Object.values(props.period);
});

const inboundList = computed(() => {
    if (!props.inbound) return [];
    return Array.isArray(props.inbound) ? props.inbound : Object.values(props.inbound);
});

const outboundList = computed(() => {
    if (!props.outbound) return [];
    return Array.isArray(props.outbound) ? props.outbound : Object.values(props.outbound);
});

const teamNotification = ref(true);
const displayTeamNotification = () => {
    try {
        teamNotification.value = props.organization?.metadata
            ? JSON.parse(props.organization.metadata)?.notification?.team ?? true
            : true;
    } catch (error) {
        teamNotification.value = true;
    }

    return teamNotification.value;
};

const dismissNotification = () => {
    router.delete('/dismiss-notification/team', {
        preserveScroll: true,
        onSuccess: () => {
            teamNotification.value = false;
        }
    });
};
</script>

<template>
    <AppLayout>
        <div class="p-4 sm:p-6 lg:p-8 space-y-6 max-w-7xl mx-auto w-full transition-colors duration-200">
            <!-- 1. Hero / Command Banner -->
            <DashboardHero
                :user="props.auth?.user || props.user"
                :organization="props.organization"
                :setupWhatsapp="props.setupWhatsapp"
            />

            <!-- 1b. Guided Company Onboarding Progress Checklist -->
            <DashboardOnboardingChecklist
                v-if="isOwner && props.onboardingState"
                :onboardingState="props.onboardingState"
            />

            <!-- 2. Team Invitation Alert (if owner & not dismissed) -->
            <DashboardTeamBanner
                :show="isOwner && displayTeamNotification()"
                @dismiss="dismissNotification"
            />

            <!-- 3. KPI Stat Cards -->
            <DashboardKpis
                :contactCount="props.contactCount"
                :campaignCount="props.campaignCount"
                :templateCount="props.templateCount"
                :chatCount="props.chatCount"
                :inbound="inboundList"
                :outbound="outboundList"
            />

            <!-- 4. WhatsApp Connection Card -->
            <DashboardWhatsAppStatus
                :setupWhatsapp="props.setupWhatsapp"
                :embeddedSignupActive="props.embeddedSignupActive"
                :appId="props.appId"
                :configId="props.configId"
                :graphAPIVersion="props.graphAPIVersion"
                :organization="props.organization"
                :isOwner="isOwner"
            />

            <!-- 5. Main Analytics & Activity Section (Grid 3 cols on lg: 2 cols chart + 1 col right) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left 2 Cols: Communication Activity Chart & Quick Actions -->
                <div class="lg:col-span-2 space-y-6">
                    <DashboardAnalytics
                        :period="periodList"
                        :inbound="inboundList"
                        :outbound="outboundList"
                    />

                    <!-- Quick Action Shortcuts -->
                    <DashboardQuickActions />
                </div>

                <!-- Right 1 Col: Campaigns, AI Assistant & Subscription -->
                <div class="space-y-6">
                    <!-- Outgoing / Scheduled Campaigns -->
                    <DashboardCampaigns
                        :campaigns="campaignsList"
                    />

                    <!-- Wappiyo AI Card -->
                    <DashboardAiCard />

                    <!-- Subscription / Plan Info -->
                    <DashboardSubscription
                        :subscription="props.subscription"
                        :subscriptionIsActive="props.subscriptionIsActive"
                        :subscriptionDetails="props.subscriptionDetails"
                        :isOwner="isOwner"
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>