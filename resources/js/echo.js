import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

let echoInstance = null;
let currentKey = null;

export function getEchoInstance(pusherKey, pusherCluster) {
    if (!pusherKey || !pusherCluster) {
        return null;
    }

    // Reinitialize if key changed (e.g. workspace or tenant switch)
    if (echoInstance && currentKey !== pusherKey) {
        disconnectEcho();
    }

    if (!echoInstance) {
        window.Pusher = Pusher;
        currentKey = pusherKey;
        
        echoInstance = new Echo({
            broadcaster: 'pusher',
            key: pusherKey,
            cluster: pusherCluster,
            encrypted: true,
            disableStats: true,
        });

        // Connection state monitoring
        if (echoInstance.connector?.pusher?.connection) {
            const connection = echoInstance.connector.pusher.connection;
            
            connection.bind('state_change', (states) => {
                // states = { previous: 'connecting', current: 'connected' }
                if (states.current === 'unavailable' || states.current === 'failed') {
                    console.warn('[Wappiyo Realtime] WebSocket connection lost. Reconnecting...');
                } else if (states.current === 'connected' && states.previous !== 'connecting') {
                    console.log('[Wappiyo Realtime] WebSocket reconnected successfully.');
                }
            });
        }
    }

    return echoInstance;
}

export function disconnectEcho() {
    if (echoInstance) {
        try {
            echoInstance.disconnect();
        } catch (_) {}
        echoInstance = null;
        currentKey = null;
    }
}