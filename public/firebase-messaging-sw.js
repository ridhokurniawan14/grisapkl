importScripts("https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js");
importScripts(
    "https://www.gstatic.com/firebasejs/8.10.1/firebase-messaging.js",
);

// ============================================================
// 1. Config Firebase
// ============================================================
const firebaseConfig = {
    apiKey: "AIzaSyAiTgBrhFBuSFJV9tDlVXleJhIhsNav0H4",
    authDomain: "grisapkl.firebaseapp.com",
    projectId: "grisapkl",
    storageBucket: "grisapkl.firebasestorage.app",
    messagingSenderId: "352687462221",
    appId: "1:352687462221:web:4293afee4d3d79d0678904",
};

// ============================================================
// 2. Inisialisasi Firebase
// ============================================================
firebase.initializeApp(firebaseConfig);
const messaging = firebase.messaging();

// ============================================================
// 3. Handler Notifikasi Saat Aplikasi di Background
// ============================================================
messaging.onBackgroundMessage(function (payload) {
    console.log("[SW] Background message received:", payload);

    const title = payload.notification?.title || "GrisaPKL";
    const body = payload.notification?.body || "";
    const redirectUrl =
        payload.data?.link ||
        payload.fcmOptions?.link ||
        payload.data?.click_action ||
        "/";

    const options = {
        body: body,
        icon: "/images/logo.png",
        badge: "/images/logo.png",
        vibrate: [200, 100, 200],
        requireInteraction: false,
        data: {
            url: redirectUrl,
        },
    };

    return self.registration.showNotification(title, options);
});

// ============================================================
// 4. Handle Klik Notifikasi → Buka/Fokus ke URL
// ============================================================
self.addEventListener("notificationclick", function (event) {
    event.notification.close();
    const targetUrl = event.notification.data?.url || "/";

    event.waitUntil(
        clients
            .matchAll({ type: "window", includeUncontrolled: true })
            .then(function (clientList) {
                for (const client of clientList) {
                    if (client.url === targetUrl && "focus" in client) {
                        return client.focus();
                    }
                }
                if (clients.openWindow) {
                    return clients.openWindow(targetUrl);
                }
            }),
    );
});

// ============================================================
// 5. MANTRA SAKTI: MODE OFFLINE PWA (NYAWA CADANGAN)
// ============================================================
const CACHE_NAME = "grisapkl-offline-v1";
const OFFLINE_URL = "/offline";

// A. Saat Install: Simpan halaman /offline ke memori cache HP
self.addEventListener("install", (event) => {
    event.waitUntil(
        caches
            .open(CACHE_NAME)
            .then((cache) => {
                // FIX UNTUK iOS: Hapus parameter { cache: 'reload' } karena Safari sering memblokirnya
                return cache.add(OFFLINE_URL);
            })
            .catch((err) => {
                console.error("[SW] Gagal menyimpan cache offline:", err);
            }),
    );
    self.skipWaiting();
});

// B. Saat Aktivasi: Bersihkan cache versi lama jika ada update
self.addEventListener("activate", (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames
                    .filter((name) => name !== CACHE_NAME)
                    .map((name) => caches.delete(name)),
            );
        }),
    );
    self.clients.claim();
});

// C. Saat Fetch (Request Data): Tangkap request! Jika gagal (offline), tampilkan layar cache!
self.addEventListener("fetch", (event) => {
    // Hanya memproses request untuk memuat halaman web (navigate)
    if (event.request.mode === "navigate") {
        event.respondWith(
            fetch(event.request).catch(() => {
                // Jika koneksi internet mati (fetch gagal), munculkan halaman offline
                return caches.match(OFFLINE_URL);
            }),
        );
    }
});
