/**
 * Fix&Go — Service Worker
 * Strategy: Cache-first for static assets, Network-first for API/HTML
 */

const CACHE_NAME    = 'fixandgo-v4';
const OFFLINE_URL   = '/offline.php';

// Static assets to pre-cache on install
// Paths are relative to the server root (htdocs/)
const PRECACHE_URLS = [
  '/login.html',
  '/register.php',
  '/dashboard.php',
  '/offline.php',
  '/fixandgo/manifest.json',
  '/fixandgo/assets/css/auth.css',
  '/fixandgo/assets/css/dashboard.css',
  '/fixandgo/assets/css/supplier.css',
  '/fixandgo/assets/css/mobile.css',
  '/fixandgo/assets/js/auth-utils.js',
  '/fixandgo/assets/js/theme.js',
  '/fixandgo/assets/images/logo.png',
];

// ── Install ────────────────────────────────────────────────────
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => cache.addAll(PRECACHE_URLS.map(url => new Request(url, { cache: 'reload' }))))
      .then(() => self.skipWaiting())
      .catch(err => console.warn('[SW] Pre-cache failed (some files may not exist yet):', err))
  );
});

// ── Activate ───────────────────────────────────────────────────
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(keys =>
      Promise.all(keys.filter(k => k !== CACHE_NAME).map(k => caches.delete(k)))
    ).then(() => self.clients.claim())
  );
});

// ── Fetch ──────────────────────────────────────────────────────
self.addEventListener('fetch', event => {
  const { request } = event;
  const url = new URL(request.url);

  // Skip non-GET and cross-origin requests
  if (request.method !== 'GET') return;
  if (url.origin !== location.origin) return;

  // NEVER cache index.php — always fetch fresh from network
  if (url.pathname === '/' ||
      url.pathname === '/index.php') {
    event.respondWith(
      fetch(request, { cache: 'no-store' })
        .catch(() => caches.match(request))
    );
    return;
  }

  // Skip backend PHP API calls — always go to network
  if (url.pathname.includes('/backend/') && url.pathname.endsWith('.php')) {
    event.respondWith(
      fetch(request).catch(() =>
        new Response(JSON.stringify({ success: false, message: 'You are offline.' }), {
          headers: { 'Content-Type': 'application/json' }
        })
      )
    );
    return;
  }

  // Cache-first for static assets (CSS, JS, images, fonts)
  if (
    url.pathname.match(/\.(css|js|png|jpg|jpeg|svg|webp|gif|woff2?|ttf|ico)$/)
  ) {
    event.respondWith(
      caches.match(request).then(cached => {
        if (cached) return cached;
        return fetch(request).then(response => {
          if (response.ok) {
            const clone = response.clone();
            caches.open(CACHE_NAME).then(cache => cache.put(request, clone));
          }
          return response;
        });
      })
    );
    return;
  }

  // Network-first for HTML pages — fall back to offline page
  event.respondWith(
    fetch(request)
      .then(response => {
        if (response.ok) {
          const clone = response.clone();
          caches.open(CACHE_NAME).then(cache => cache.put(request, clone));
        }
        return response;
      })
      .catch(() =>
        caches.match(request).then(cached => cached || caches.match(OFFLINE_URL))
      )
  );
});
