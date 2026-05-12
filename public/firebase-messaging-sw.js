importScripts("https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js");
importScripts(
    "https://www.gstatic.com/firebasejs/8.10.1/firebase-messaging.js",
);

// 1. Config Firebase
const firebaseConfig = {
    apiKey: "AIzaSyAiTgBrhFBuSFJV9tDlVXleJhIhsNav0H4",
    authDomain: "grisapkl.firebaseapp.com",
    projectId: "grisapkl",
    storageBucket: "grisapkl.firebasestorage.app",
    messagingSenderId: "352687462221",
    appId: "1:352687462221:web:4293afee4d3d79d0678904",
};

// 2. Inisialisasi Firebase
firebase.initializeApp(firebaseConfig);
const messaging = firebase.messaging();

// 3. Tangkap Notifikasi saat Aplikasi Berjalan di Background (Ditutup)
messaging.onBackgroundMessage(function (payload) {
    console.log(
        "[firebase-messaging-sw.js] Menerima pesan background ",
        payload,
    );

    const notificationTitle = payload.notification.title;
    const notificationOptions = {
        body: payload.notification.body,
        icon: "/images/logo.png",
        badge: "/favicon.ico",
    };

    self.registration.showNotification(notificationTitle, notificationOptions);
});
