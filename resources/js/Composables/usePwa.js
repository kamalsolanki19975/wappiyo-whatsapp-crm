import { ref, onMounted } from 'vue';

// Shared singleton state across components
const isOnline = ref(typeof navigator !== 'undefined' ? navigator.onLine : true);
const wasOffline = ref(false);
const showReconnectedBanner = ref(false);
const isUpdateAvailable = ref(false);
const registration = ref(null);
const waitingWorker = ref(null);
const deferredInstallPrompt = ref(null);
const canInstall = ref(false);
const isIos = ref(false);
const isStandalone = ref(false);
const isInstallBannerDismissed = ref(false);

export function usePwa() {
    const checkDisplayMode = () => {
        if (typeof window === 'undefined') return;
        const isStandaloneMode = (
            window.matchMedia('(display-mode: standalone)').matches ||
            window.navigator.standalone === true ||
            document.referrer.includes('android-app://')
        );
        isStandalone.value = isStandaloneMode;
    };

    const initNetworkListeners = () => {
        if (typeof window === 'undefined') return;

        window.addEventListener('online', () => {
            isOnline.value = true;
            if (wasOffline.value) {
                showReconnectedBanner.value = true;
                setTimeout(() => {
                    showReconnectedBanner.value = false;
                    wasOffline.value = false;
                }, 3500);
            }
        });

        window.addEventListener('offline', () => {
            isOnline.value = false;
            wasOffline.value = true;
            showReconnectedBanner.value = false;
        });
    };

    const registerServiceWorker = async () => {
        if (typeof window === 'undefined' || !('serviceWorker' in navigator)) {
            return;
        }

        try {
            const reg = await navigator.serviceWorker.register('/sw.js', { scope: '/' });
            registration.value = reg;

            // Check if there is already a waiting worker
            if (reg.waiting) {
                waitingWorker.value = reg.waiting;
                isUpdateAvailable.value = true;
            }

            // Listen for newly installed workers
            reg.addEventListener('updatefound', () => {
                const newWorker = reg.installing;
                if (!newWorker) return;

                newWorker.addEventListener('statechange', () => {
                    if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                        waitingWorker.value = newWorker;
                        isUpdateAvailable.value = true;
                    }
                });
            });

            // Reload page once new service worker takes over control
            let refreshing = false;
            navigator.serviceWorker.addEventListener('controllerchange', () => {
                if (!refreshing) {
                    refreshing = true;
                    window.location.reload();
                }
            });
        } catch (error) {
            console.warn('[Wappiyo PWA] Service worker registration failed:', error);
        }
    };

    const updateApp = () => {
        if (waitingWorker.value) {
            waitingWorker.value.postMessage({ type: 'SKIP_WAITING' });
        } else if (registration.value && registration.value.waiting) {
            registration.value.waiting.postMessage({ type: 'SKIP_WAITING' });
        } else {
            window.location.reload();
        }
    };

    const initInstallPrompt = () => {
        if (typeof window === 'undefined') return;

        // Check if user previously dismissed prompt
        const dismissedAt = localStorage.getItem('wappiyo_pwa_dismissed');
        if (dismissedAt) {
            const daysSinceDismissal = (Date.now() - parseInt(dismissedAt, 10)) / (1000 * 60 * 60 * 24);
            if (daysSinceDismissal < 7) {
                isInstallBannerDismissed.value = true;
            }
        }

        // iOS detection
        const userAgent = window.navigator.userAgent.toLowerCase();
        isIos.value = /iphone|ipad|ipod/.test(userAgent) && !window.MSStream;

        // Listen for beforeinstallprompt event on Android / Chrome / Edge
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredInstallPrompt.value = e;
            canInstall.value = true;
        });

        // Listen for appinstalled event
        window.addEventListener('appinstalled', () => {
            deferredInstallPrompt.value = null;
            canInstall.value = false;
            isStandalone.value = true;
            console.log('[Wappiyo PWA] App was successfully installed!');
        });
    };

    const promptInstall = async () => {
        if (!deferredInstallPrompt.value) return false;

        deferredInstallPrompt.value.prompt();
        const choiceResult = await deferredInstallPrompt.value.userChoice;
        if (choiceResult.outcome === 'accepted') {
            canInstall.value = false;
        }
        deferredInstallPrompt.value = null;
        return choiceResult.outcome === 'accepted';
    };

    const dismissInstallPrompt = () => {
        isInstallBannerDismissed.value = true;
        try {
            localStorage.setItem('wappiyo_pwa_dismissed', Date.now().toString());
        } catch (_) {}
    };

    const setAppBadge = async (count) => {
        if (typeof navigator !== 'undefined' && 'setAppBadge' in navigator) {
            try {
                const numericCount = parseInt(count, 10);
                if (numericCount > 0) {
                    await navigator.setAppBadge(numericCount);
                } else {
                    await navigator.clearAppBadge();
                }
            } catch (_) {}
        }
    };

    const clearAppBadge = async () => {
        if (typeof navigator !== 'undefined' && 'clearAppBadge' in navigator) {
            try {
                await navigator.clearAppBadge();
            } catch (_) {}
        }
    };

    const requestNotificationPermission = async () => {
        if (typeof window === 'undefined' || !('Notification' in window)) {
            return 'unsupported';
        }
        try {
            const permission = await Notification.requestPermission();
            return permission;
        } catch (_) {
            return 'denied';
        }
    };

    return {
        isOnline,
        wasOffline,
        showReconnectedBanner,
        isUpdateAvailable,
        canInstall,
        isIos,
        isStandalone,
        isInstallBannerDismissed,
        checkDisplayMode,
        initNetworkListeners,
        registerServiceWorker,
        updateApp,
        initInstallPrompt,
        promptInstall,
        dismissInstallPrompt,
        setAppBadge,
        clearAppBadge,
        requestNotificationPermission,
    };
}
