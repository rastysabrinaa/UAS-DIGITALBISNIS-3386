const CACHE_NAME = 'amikomeventhub-v1';
const urlsToCache = [
  '/',
  '/manifest.json',
];

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        return cache.addAll(urlsToCache);
      })
  );
});

self.addEventListener('fetch', event => {
  // Hanya melayani dari cache jika sedang offline.
  // Untuk web dinamis (Laravel), lebih baik network-first strategy.
  event.respondWith(
    fetch(event.request).catch(() => {
      return caches.match(event.request);
    })
  );
});
