/**
 * KDKMP Service Worker — PWA Support
 * Caches static assets for offline functionality.
 *
 * Security: Only caches first-party assets, no external resources.
 * Does NOT cache: auth tokens, session data, or sensitive pages.
 */

const CACHE_NAME = 'kdkmp-v1';

// Static assets to pre-cache (only public, non-sensitive resources)
const STATIC_ASSETS = [
    '/',
    '/catalog',
    '/css/airbnb.css',
    '/manifest.json',
    '/icons/icon-192.png',
    '/icons/icon-512.png',
];

// Pages that should NEVER be cached (auth & sensitive)
const NO_CACHE_PATTERNS = [
    /\/staff\//,
    /\/member\//,
    /\/admin\//,
    /\/cart\/checkout/,
    /\/logout/,
    /\/login/,
    /\/register/,
    /\/reports\//,
    /\.pdf$/,
    /\.xlsx$/,
    /\.csv$/,
];

// ── Install Event: Pre-cache static assets ──────────────────────────────────
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(STATIC_ASSETS);
        }).then(() => {
            return self.skipWaiting();
        })
    );
});

// ── Activate Event: Clean old caches ────────────────────────────────────────
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames
                    .filter((name) => name !== CACHE_NAME)
                    .map((name) => caches.delete(name))
            );
        }).then(() => {
            return self.clients.claim();
        })
    );
});

// ── Fetch Event: Network-first with offline fallback ────────────────────────
self.addEventListener('fetch', (event) => {
    const url = new URL(event.request.url);

    // Skip non-GET requests (POST, PUT, DELETE — mutations must go to server)
    if (event.request.method !== 'GET') return;

    // Skip cross-origin requests
    if (url.origin !== self.location.origin) return;

    // Skip sensitive/auth pages — never cache, always network
    const isSensitive = NO_CACHE_PATTERNS.some((pattern) => pattern.test(url.pathname));
    if (isSensitive) return;

    // Network-first strategy: try network, fall back to cache, then offline page
    event.respondWith(
        fetch(event.request)
            .then((networkResponse) => {
                // Cache successful responses for static-like paths
                if (networkResponse.ok && event.request.url.includes('/css/')) {
                    const responseClone = networkResponse.clone();
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(event.request, responseClone);
                    });
                }
                return networkResponse;
            })
            .catch(() => {
                // Return cached version if available
                return caches.match(event.request).then((cachedResponse) => {
                    if (cachedResponse) return cachedResponse;
                    // Return offline page for HTML navigation requests
                    if (event.request.headers.get('accept')?.includes('text/html')) {
                        return caches.match('/offline');
                    }
                    return new Response('', { status: 503 });
                });
            })
    );
});
