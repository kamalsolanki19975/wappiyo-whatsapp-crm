/**
 * Wappiyo Production Progressive Web App (PWA) Service Worker
 * Version: wappiyo-v1.0.0
 */

const CACHE_VERSION = 'wappiyo-v1';
const STATIC_CACHE_NAME = `wappiyo-static-${CACHE_VERSION}`;
const ASSETS_CACHE_NAME = `wappiyo-assets-${CACHE_VERSION}`;

// Core application shell and offline fallbacks to precache
const PRECACHE_ASSETS = [
    '/offline.html',
    '/favicon.ico',
    '/favicon-32x32.png',
    '/favicon-16x16.png',
    '/apple-touch-icon.png',
    '/images/logo.png',
    '/images/logo-dark.png',
    '/images/logo-mark.png',
    '/android-chrome-192x192.png',
    '/android-chrome-512x512.png',
    '/maskable-icon-192x192.png',
    '/maskable-icon-512x512.png',
    '/site.webmanifest'
];

// Sensitive routes and API endpoints that must NEVER be cached by the service worker
const SENSITIVE_URL_PATTERNS = [
    /\/api\//i,
    /\/login/i,
    /\/logout/i,
    /\/register/i,
    /\/password/i,
    /\/reset-password/i,
    /\/verify-email/i,
    /\/two-factor/i,
    /\/sanctum/i,
    /\/csrf-cookie/i,
    /\/organization/i,
    /\/translations\//i,
    /\/locales/i,
    /\/current-locale/i,
    /\/pusher\//i,
    /\/broadcasting\//i,
    /\/webhook/i,
    /\/media\//i
];

/**
 * Install Event: Precache offline shell & essential brand assets
 */
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(STATIC_CACHE_NAME).then((cache) => {
            return cache.addAll(PRECACHE_ASSETS).catch((err) => {
                console.warn('[Wappiyo SW] Precache warning:', err);
            });
        })
    );
});

/**
 * Activate Event: Clean up outdated caches & claim clients immediately
 */
self.addEventListener('activate', (event) => {
    const currentCaches = [STATIC_CACHE_NAME, ASSETS_CACHE_NAME];
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName.startsWith('wappiyo-') && !currentCaches.includes(cacheName)) {
                        console.log('[Wappiyo SW] Deleting legacy cache:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});

/**
 * Message Event: Handle SKIP_WAITING for smooth PWA updates
 */
self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});

/**
 * Fetch Event: Smart routing & caching strategies
 */
self.addEventListener('fetch', (event) => {
    const request = event.request;
    const url = new URL(request.url);

    // Only handle GET requests with HTTP/HTTPS protocols
    if (request.method !== 'GET' || !url.protocol.startsWith('http')) {
        return;
    }

    // 1. Never cache sensitive routes, authentication, or dynamic API endpoints
    const isSensitive = SENSITIVE_URL_PATTERNS.some((pattern) => pattern.test(url.pathname));
    if (isSensitive) {
        return; // Normal network request
    }

    // 2. Navigation requests (HTML pages): Network-First with Offline Fallback
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request).catch(() => {
                return caches.match('/offline.html');
            })
        );
        return;
    }

    // 3. Vite hashed build assets (/build/assets/*): Cache-First
    if (url.pathname.startsWith('/build/assets/')) {
        event.respondWith(
            caches.open(ASSETS_CACHE_NAME).then((cache) => {
                return cache.match(request).then((cachedResponse) => {
                    if (cachedResponse) {
                        return cachedResponse;
                    }
                    return fetch(request).then((networkResponse) => {
                        if (networkResponse && networkResponse.status === 200) {
                            cache.put(request, networkResponse.clone());
                        }
                        return networkResponse;
                    });
                });
            })
        );
        return;
    }

    // 4. Static images, icons, and Google Fonts: Stale-While-Revalidate
    const isStaticAsset = (
        url.pathname.startsWith('/images/') ||
        url.pathname.match(/\.(png|jpg|jpeg|svg|webp|ico|woff2?|ttf|eot)$/i) ||
        url.hostname.includes('fonts.googleapis.com') ||
        url.hostname.includes('fonts.gstatic.com')
    );

    if (isStaticAsset) {
        event.respondWith(
            caches.open(ASSETS_CACHE_NAME).then((cache) => {
                return cache.match(request).then((cachedResponse) => {
                    const fetchPromise = fetch(request).then((networkResponse) => {
                        if (networkResponse && networkResponse.status === 200) {
                            cache.put(request, networkResponse.clone());
                        }
                        return networkResponse;
                    }).catch(() => null);

                    return cachedResponse || fetchPromise;
                });
            })
        );
        return;
    }
});

/**
 * Web Push Notification Event
 */
self.addEventListener('push', (event) => {
    let data = {};
    if (event.data) {
        try {
            data = event.data.json();
        } catch (_) {
            data = { title: 'Wappiyo', body: event.data.text() };
        }
    }

    const title = data.title || 'Wappiyo';
    const options = {
        body: data.body || 'You have a new conversation update in Wappiyo.',
        icon: data.icon || '/android-chrome-192x192.png',
        badge: data.badge || '/favicon-32x32.png',
        data: {
            url: data.url || '/chats',
            dateOfArrival: Date.now(),
        },
        tag: data.tag || 'wappiyo-notification',
        renotify: true,
        vibrate: [100, 50, 100],
    };

    event.waitUntil(self.registration.showNotification(title, options));
});

/**
 * Notification Click Event: Navigate to destination URL
 */
self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    const targetUrl = event.notification.data?.url || '/chats';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then((windowClients) => {
            for (let i = 0; i < windowClients.length; i++) {
                const client = windowClients[i];
                if (client.url.includes(targetUrl) && 'focus' in client) {
                    return client.focus();
                }
            }
            if (clients.openWindow) {
                return clients.openWindow(targetUrl);
            }
        })
    );
});
