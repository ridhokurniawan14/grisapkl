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
// 3. FIX UTAMA: Handler notifikasi background/app tertutup
//    Tanpa ini, notif TIDAK AKAN MUNCUL saat app di background
// ============================================================
messaging.onBackgroundMessage(function (payload) {
    console.log("[SW] Background message received:", payload);

    const title = payload.notification?.title || "GrisaPKL";
    const body = payload.notification?.body || "";

    // Ambil redirect URL dari data payload atau fcmOptions
    const redirectUrl =
        payload.data?.link ||
        payload.fcmOptions?.link ||
        payload.data?.click_action ||
        "/";

    const options = {
        body: body,
        icon: "/images/logo.png", // Ganti path logo sesuai project kamu
        badge: "/images/logo.png", // Badge kecil di status bar Android
        vibrate: [200, 100, 200], // Pola getar Android
        requireInteraction: false,
        data: {
            url: redirectUrl,
        },
    };

    return self.registration.showNotification(title, options);
});

// ============================================================
// 4. Handle klik notifikasi → buka/fokus ke URL tujuan
// ============================================================
self.addEventListener("notificationclick", function (event) {
    event.notification.close();

    const targetUrl = event.notification.data?.url || "/";

    event.waitUntil(
        clients
            .matchAll({ type: "window", includeUncontrolled: true })
            .then(function (clientList) {
                // Kalau tab yang sama sudah terbuka → fokus saja
                for (const client of clientList) {
                    if (client.url === targetUrl && "focus" in client) {
                        return client.focus();
                    }
                }
                // Kalau belum ada → buka tab baru
                if (clients.openWindow) {
                    return clients.openWindow(targetUrl);
                }
            }),
    );
});

// ============================================================
// 5. Fetch handler (wajib ada agar SW tidak error)
// ============================================================
self.addEventListener("fetch", function (event) {});
