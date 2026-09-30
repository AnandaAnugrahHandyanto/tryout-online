const CACHE = 'akademikpro-assets-v2';

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE).then((cache) =>
            cache.addAll([
                '/manifest.json',
                '/icons/icon-192x192.png',
                '/icons/icon-512x512.png',
            ])
        ).then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((keys) =>
                Promise.all(
                    keys
                        .filter((key) => key !== CACHE)
                        .map((key) => caches.delete(key))
                )
            )
            .then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (event) => {
    const request = event.request;

    // Jangan pernah intercept POST/PUT/PATCH/DELETE.
    if (request.method !== 'GET') {
        return;
    }

    const url = new URL(request.url);

    // Jangan cache halaman Laravel/authenticated pages.
    if (
        url.pathname === '/' ||
        url.pathname === '/login' ||
        url.pathname.startsWith('/admin') ||
        url.pathname.startsWith('/guru') ||
        url.pathname.startsWith('/siswa') ||
        url.pathname.startsWith('/orang-tua') ||
        url.pathname.startsWith('/notifikasi') ||
        url.pathname.startsWith('/profile') ||
        url.pathname.startsWith('/dashboard')
    ) {
        return;
    }

    // Hanya cache asset statis.
    if (
        url.pathname.startsWith('/build/') ||
        url.pathname.startsWith('/icons/') ||
        url.pathname === '/manifest.json'
    ) {
        event.respondWith(
            caches.match(request).then((cached) => {
                if (cached) {
                    return cached;
                }

                return fetch(request).then((response) => {
                    if (response.ok) {
                        const clone = response.clone();

                        caches.open(CACHE).then((cache) => {
                            cache.put(request, clone);
                        });
                    }

                    return response;
                });
            })
        );
    }
});
