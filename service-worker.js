const CACHE_NAME = "dashboard-cache-v1";
const urlsToCache = [
    "dashboard.html",
    "style/dashboard.css",
    "script.js", // Replace with your JavaScript file name
    "icon-192.png",
    "icon-512.png"
];

self.addEventListener("install", event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => cache.addAll(urlsToCache))
    );
});

self.addEventListener("fetch", event => {
    event.respondWith(
        caches.match(event.request)
            .then(response => response || fetch(event.request))
    );
});
