const CACHE = 'akademikpro-v1';
const ASSETS = [
  '/',
  '/manifest.json',
  '/icons/icon-192x192.png',
  '/icons/icon-512x512.png',
];

self.addEventListener('install', (e) => {
  e.waitUntil(caches.open(CACHE).then((c) => c.addAll(ASSETS)).then(() => self.skipWaiting()));
});

self.addEventListener('activate', (e) => {
  e.waitUntil(
    caches.keys().then((keys) => Promise.all(keys.filter((k) => k !== CACHE).map((k) => caches.delete(k)))).then(() => self.clients.claim())
  );
});

self.addEventListener('fetch', (e) => {
  const req = e.request;
  if (req.method !== 'GET') return;
  const url = new URL(req.url);
  // jangan cache API auth, csrf, POST, atau chrome extension
  if (url.pathname.startsWith('/admin') || url.pathname.startsWith('/guru') || url.pathname.startsWith('/siswa') || url.pathname.startsWith('/orang-tua') || url.pathname.startsWith('/notifikasi')) {
    e.respondWith(
      fetch(req)
        .then((res) => {
          const clone = res.clone();
          caches.open(CACHE).then((c) => c.put(req, clone));
          return res;
        })
        .catch(() => caches.match(req))
    );
    return;
  }
  // cache-first untuk assets, network-first fallback untuk navigasi
  e.respondWith(
    caches.match(req).then((cached) => {
      if (cached) {
        // stale-while-revalidate
        e.waitUntil(fetch(req).then((res) => caches.open(CACHE).then((c) => c.put(req, res))).catch(() => {}));
        return cached;
      }
      return fetch(req)
        .then((res) => {
          if (res.ok && req.url.startsWith(self.location.origin)) {
            const clone = res.clone();
            caches.open(CACHE).then((c) => c.put(req, clone));
          }
          return res;
        })
        .catch(() => caches.match('/'));
    })
  );
});
