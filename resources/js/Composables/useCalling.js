import { ref, computed } from 'vue';
import axios from 'axios';

// Global shared state across all components and pages
const activeCall = ref(null);
const callContact = ref(null);
const isCallModalOpen = ref(false);

const incomingCall = ref(null);
const isIncomingModalOpen = ref(false);

const isCallBarMinimized = ref(false);

export function useCalling() {
    /**
     * Start an outbound call to a contact
     */
    const startCall = (contact, phone = null) => {
        callContact.value = contact || { phone: phone };
        activeCall.value = null;
        isCallModalOpen.value = true;
        isCallBarMinimized.value = false;
    };

    /**
     * Open modal for an existing active call
     */
    const openActiveCall = (call) => {
        if (call) {
            activeCall.value = call;
            callContact.value = call.contact || { phone: call.customer_phone };
        }
        isCallModalOpen.value = true;
        isCallBarMinimized.value = false;
    };

    /**
     * Close the call modal (if call is still active, show floating call bar)
     */
    const closeCallModal = () => {
        isCallModalOpen.value = false;
        if (activeCall.value && ['initiating', 'ringing', 'connecting', 'connected'].includes(activeCall.value.status)) {
            isCallBarMinimized.value = true;
        } else {
            isCallBarMinimized.value = false;
        }
    };

    /**
     * Accept incoming call
     */
    const acceptIncomingCall = (call) => {
        isIncomingModalOpen.value = false;
        incomingCall.value = null;
        activeCall.value = call;
        callContact.value = call?.contact || { phone: call?.customer_phone };
        isCallModalOpen.value = true;
    };

    /**
     * Decline incoming call
     */
    const declineIncomingCall = () => {
        isIncomingModalOpen.value = false;
        incomingCall.value = null;
    };

    /**
     * Handle incoming real-time broadcast from Echo
     */
    const handleCallBroadcast = (event) => {
        if (!event || !event.call) return;
        const call = event.call;

        // If it's an incoming call ringing
        if (call.direction === 'inbound' && (call.status === 'ringing' || call.status === 'initiating')) {
            if (!activeCall.value || activeCall.value.uuid !== call.uuid) {
                incomingCall.value = call;
                isIncomingModalOpen.value = true;
            }
        }

        // If it updates the currently active call
        if (activeCall.value && activeCall.value.uuid === call.uuid) {
            activeCall.value = { ...activeCall.value, ...call };

            if (['completed', 'failed', 'missed', 'cancelled', 'rejected'].includes(call.status)) {
                // Call terminated
                isCallBarMinimized.value = false;
            }
        }

        // If incoming call was answered elsewhere or missed/cancelled
        if (incomingCall.value && incomingCall.value.uuid === call.uuid) {
            if (['completed', 'failed', 'missed', 'cancelled', 'rejected'].includes(call.status)) {
                isIncomingModalOpen.value = false;
                incomingCall.value = null;
            }
        }
    };

    return {
        activeCall,
        callContact,
        isCallModalOpen,
        incomingCall,
        isIncomingModalOpen,
        isCallBarMinimized,
        startCall,
        openActiveCall,
        closeCallModal,
        acceptIncomingCall,
        declineIncomingCall,
        handleCallBroadcast,
    };
}
