/* PRISM SDA Service Worker */
const VERSION = 'v1.0.0';
const CACHE_NAME = `prism-sda-${VERSION}`;
const CORE_ASSETS = [
    '/', '/offline.html',
    '/assets/css/core.css',
    '/assets/css/style.css',
    '/assets/css/iconfont.css',
    '/assets/js/color-modes.js'
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => cache.addAll(CORE_ASSETS))
            .then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then(keys =>
            Promise.all(keys.map(k => (k !== CACHE_NAME ? caches.delete(k) : null)))
        ).then(() => self.clients.claim())
    );
});

const isHTML = (req) =>
    req.mode === 'navigate' ||
    (req.method === 'GET' && req.headers.get('accept')?.includes('text/html'));

self.addEventListener('fetch', (event) => {
    const req = event.request;
    const url = new URL(req.url);

    if (req.method !== 'GET') return;

    // HTML pages: Network-first + cache + offline fallback
    if (isHTML(req)) {
        event.respondWith((async () => {
            try {
                const fresh = await fetch(req);
                const cache = await caches.open(CACHE_NAME);
                cache.put(req, fresh.clone());
                return fresh;
            } catch {
                const cached = await caches.match(req);
                return cached || caches.match('/offline.html');
            }
        })());
        return;
    }

    // Third-party CDNs: stale-while-revalidate
    if (/fonts\.googleapis\.com|fonts\.gstatic\.com|cdn\.datatables\.net|cdnjs\.cloudflare\.com/.test(url.hostname)) {
        event.respondWith((async () => {
            const cache = await caches.open(CACHE_NAME);
            const cached = await cache.match(req);
            const network = fetch(req).then(res => {
                cache.put(req, res.clone());
                return res;
            }).catch(() => null);
            return cached || network || fetch(req);
        })());
        return;
    }

    // Static assets: cache-first
    if (/\.(?:css|js|png|svg|jpg|jpeg|gif|ico|webp|woff2?)$/i.test(url.pathname)) {
        event.respondWith((async () => {
            const cached = await caches.match(req);
            if (cached) return cached;
            try {
                const res = await fetch(req);
                const cache = await caches.open(CACHE_NAME);
                cache.put(req, res.clone());
                return res;
            } catch {
                return new Response('', { status: 504, statusText: 'Gateway Timeout' });
            }
        })());
    }
});
