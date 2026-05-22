<!DOCTYPE html>
<html class="light" lang="id">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />
    <title>{{ $title ?? 'GrisaPKL' }}</title>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#3525cd">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $school = \App\Models\SchoolProfile::first();
        $dynamicFavicon = $school && $school->app_logo_path ? asset('storage/' . $school->app_logo_path) : null;
    @endphp

    {{-- Dynamic Favicon dari logo sekolah --}}
    @if ($dynamicFavicon)
        <link rel="icon" type="image/png" href="{{ $dynamicFavicon }}">
        <link rel="shortcut icon" href="{{ $dynamicFavicon }}">
        <link rel="apple-touch-icon" href="{{ $dynamicFavicon }}">
    @else
        <link rel="icon" href="/images/logo.png">
    @endif

    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;600;700;800&display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "background": "#fcf8ff",
                        "on-background": "#1b1b24",
                        "primary": "#3525cd",
                    },
                    fontFamily: {
                        "body-md": ["Lexend"],
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background-color: theme('colors.background');
            color: theme('colors.on-background');
            -webkit-tap-highlight-color: transparent;
        }

        @keyframes subtle-pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(79, 70, 229, 0.4);
            }

            70% {
                box-shadow: 0 0 0 20px rgba(79, 70, 229, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(79, 70, 229, 0);
            }
        }

        .animate-subtle-pulse {
            animation: subtle-pulse 2s infinite;
        }
    </style>
</head>

<body
    class="antialiased flex flex-col font-body-md text-[14px] w-full min-h-[100dvh] bg-slate-100 sm:items-center sm:justify-center m-0 p-0">

    {{-- KANVAS PWA --}}
    <div id="app-canvas"
        class="relative w-full h-[100dvh] sm:w-[390px] sm:h-[800px] sm:max-h-[90dvh] bg-background flex flex-col overflow-hidden sm:rounded-[2.5rem] sm:shadow-2xl sm:border-[8px] sm:border-slate-800">

        @php
            $isProfile =
                request()->routeIs('siswa.profil') ||
                request()->routeIs('pembimbing.profil') ||
                request()->routeIs('dudika.profil');
            $isBeranda =
                request()->routeIs('siswa.beranda') ||
                request()->routeIs('pembimbing.beranda') ||
                request()->routeIs('dudika.beranda');

            $headerBg = $isProfile
                ? 'bg-gradient-to-b from-[#1c10a0] via-[#2d1fc5] to-[#3525cd]'
                : 'bg-white/80 backdrop-blur-md border-b border-slate-200/60';
            $headerText = $isProfile ? 'text-white' : 'text-primary';
            $headerIcon = $isProfile ? 'text-white hover:bg-white/10' : 'text-primary hover:bg-slate-50';

            $user = auth()->user();
            $studentData = null;
            $avatarPath = null;

            if ($user) {
                $studentData = \App\Models\Student::where('user_id', $user->id)->first();
                $avatarPath = $studentData->avatar ?? ($user->avatar ?? null);
            }

            $name = $user->name ?? 'User';
            $initials = collect(explode(' ', $name))->map(fn($s) => substr($s, 0, 1))->take(2)->join('');
            $initials = strtoupper($initials);

            $homeRoute = url('/');
            if ($user) {
                if ($user->hasRole('siswa')) {
                    $homeRoute = route('siswa.beranda');
                } elseif ($user->hasRole('dudika')) {
                    $homeRoute = route('dudika.beranda');
                } elseif ($user->hasRole(['guru', 'pembimbing'])) {
                    $homeRoute = route('pembimbing.beranda');
                }
            }

            $notifications = collect();
            if ($user) {
                $notifications = \App\Models\Announcement::where('is_active', true)
                    ->where(function ($q) use ($user) {
                        $q->where('target_audience', 'Umum');
                        if ($user->hasRole('siswa')) {
                            $q->orWhere('target_audience', 'Siswa');
                        }
                        if ($user->hasRole('dudika')) {
                            $q->orWhere('target_audience', 'Dudika');
                        }
                        if ($user->hasRole(['guru', 'pembimbing'])) {
                            $q->orWhere('target_audience', 'Guru');
                        }
                    })
                    ->orderBy('created_at', 'desc')
                    ->take(3)
                    ->get();
            }

            $hasUnread = $notifications->isNotEmpty();
            $latestNotifId = $notifications->first()->id ?? 0;
        @endphp

        <header
            class="{{ $headerBg }} absolute top-0 w-full z-50 flex justify-between items-center px-4 h-16 transition-colors duration-300">

            @if ($isBeranda)
                <div class="flex items-center">
                    <h1 class="text-xl font-extrabold text-[#3525cd] font-['Lexend'] tracking-tight">GrisaPKL</h1>
                </div>
            @elseif (!$isProfile)
                <div class="flex items-center gap-3">
                    <div
                        class="flex items-center justify-center w-10 h-10 rounded-full bg-[#e2dfff] overflow-hidden flex-shrink-0 border border-slate-200 shadow-sm">
                        @if (!empty($avatarPath))
                            <img src="{{ asset('storage/' . $avatarPath) }}" class="w-full h-full object-cover" />
                        @else
                            <span class="text-[#3525cd] font-bold text-[15px]">{{ $initials }}</span>
                        @endif
                    </div>
                    <div class="flex flex-col justify-center">
                        <span class="text-[11px] font-medium text-slate-500 leading-tight">Hai,</span>
                        <span class="text-[14px] font-bold text-slate-800 leading-tight truncate max-w-[120px]">
                            @php
                                $nameParts = explode(' ', trim($name));
                                $displayName = $nameParts[0] ?? 'User';
                                if (strlen($displayName) <= 2 && isset($nameParts[1])) {
                                    $displayName .= ' ' . $nameParts[1];
                                }
                            @endphp
                            {{ ucfirst($displayName) }}
                        </span>
                    </div>
                </div>
            @else
                <a href="{{ $homeRoute }}"
                    class="w-10 h-10 flex items-center justify-center rounded-full text-white hover:bg-white/10 active:scale-95 transition-all flex-shrink-0"
                    title="Kembali">
                    <span class="material-symbols-outlined">arrow_back</span>
                </a>
            @endif

            <div class="flex-grow"></div>

            {{-- DROPDOWN NOTIFIKASI DENGAN ALPINE YANG SAKTI --}}
            <div x-data="{
                open: false,
                latestId: {{ $latestNotifId }},
                hasNew: false,
                isGranted: false,
                init() {
                    let lastSeen = localStorage.getItem('last_seen_announcement_id');
                    if (this.latestId > 0 && lastSeen != this.latestId) {
                        this.hasNew = true;
                    }
                    this.checkPermission();
                },
                checkPermission() {
                    if ('Notification' in window && Notification.permission === 'granted') {
                        this.isGranted = true;
                    } else {
                        this.isGranted = false;
                    }
                }
            }" @new-notification.window="hasNew = true"
                @update-permission.window="checkPermission()" class="relative">

                <button
                    @click="open = !open; hasNew = false; localStorage.setItem('last_seen_announcement_id', latestId); handleBellClick();"
                    class="relative w-10 h-10 flex items-center justify-center rounded-full {{ $headerIcon }} transition-all active:scale-95 flex-shrink-0">
                    <span class="material-symbols-outlined">notifications</span>
                    <span x-show="hasNew" x-cloak
                        class="absolute top-2 right-2.5 w-2 h-2 bg-red-500 rounded-full border border-white"></span>
                </button>

                {{-- Konten Dropdown --}}
                <div x-show="open" @click.away="open = false" x-cloak
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-[-10px]"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    class="absolute right-0 mt-2 w-72 bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden z-[100]">

                    {{-- HEADER DENGAN INDIKATOR AKTIF --}}
                    <div class="p-4 border-b border-slate-50 bg-slate-50/50 flex items-center justify-between">
                        <h3 class="text-sm font-extrabold text-slate-800">Notifikasi Terbaru</h3>
                        <div x-show="isGranted" x-cloak
                            class="flex items-center gap-1 bg-emerald-50 text-emerald-600 px-2.5 py-1 rounded-full border border-emerald-100 shadow-sm"
                            title="Notifikasi sudah aktif">
                            <span class="material-symbols-outlined text-[13px] font-bold">check_circle</span>
                            <span class="text-[9px] font-extrabold uppercase tracking-widest">Aktif</span>
                        </div>
                    </div>

                    <div class="flex flex-col divide-y divide-slate-50 max-h-[300px] overflow-y-auto">
                        @forelse($notifications as $notif)
                            <a href="{{ $isBeranda ? '#' : $homeRoute }}" @click="open = false"
                                class="p-4 hover:bg-slate-50 transition-colors flex gap-3 items-start">
                                <div
                                    class="w-8 h-8 rounded-full bg-indigo-50 flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-[#3525cd] text-[18px]">campaign</span>
                                </div>
                                <div class="flex-1">
                                    <p class="text-[12px] font-bold text-slate-800 leading-tight mb-1">
                                        {{ $notif->title }}</p>
                                    <p class="text-[10px] text-slate-500 line-clamp-2">
                                        {{ strip_tags($notif->content) }}</p>
                                    <p class="text-[9px] text-[#3525cd] font-bold mt-1 uppercase">
                                        {{ $notif->created_at->diffForHumans() }}</p>
                                </div>
                            </a>
                        @empty
                            <div class="p-8 text-center">
                                <span
                                    class="material-symbols-outlined text-slate-300 text-[32px] mb-2">notifications_off</span>
                                <p class="text-[12px] font-medium text-slate-400">Belum ada pengumuman.</p>
                            </div>
                        @endforelse
                    </div>

                    @if ($notifications->isNotEmpty())
                        <div class="p-3 bg-slate-50 text-center border-t border-slate-100">
                            <a href="{{ $homeRoute }}" @click="open = false"
                                class="text-[11px] font-extrabold text-[#3525cd] hover:underline">
                                Lihat Semua Pengumuman
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </header>

        @php
            $isProfileOrDudika = request()->routeIs('siswa.dudika') || request()->routeIs('dudika.profil');
            $mainPadding = $isProfileOrDudika ? 'pt-0' : 'pt-20';
        @endphp

        <main class="flex-grow {{ $mainPadding }} pb-24 px-4 overflow-y-auto w-full">
            <div class="absolute -inset-x-4 inset-y-0 z-0 pointer-events-none overflow-hidden">
                <div class="absolute -top-10 -left-10 w-72 h-72 bg-blue-500/15 rounded-full blur-[80px] animate-pulse">
                </div>
                <div class="absolute top-[30%] -right-10 w-80 h-80 bg-purple-500/15 rounded-full blur-[80px]"
                    style="animation: pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite;"></div>
                <div
                    class="absolute bottom-10 left-1/3 w-64 h-64 bg-teal-400/15 rounded-full blur-[80px] animate-pulse">
                </div>
                <div
                    class="absolute -bottom-10 -right-10 w-60 h-60 bg-indigo-400/10 rounded-full blur-[70px] animate-pulse">
                </div>
            </div>

            {{ $slot }}
        </main>

        <x-bottom-nav />
    </div>

    @livewireScripts
    @stack('scripts')

    {{-- FIREBASE NOTIFICATION SCRIPTS --}}
    <script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-messaging.js"></script>
    <script>
        const firebaseConfig = {
            apiKey: "AIzaSyAiTgBrhFBuSFJV9tDlVXleJhIhsNav0H4",
            authDomain: "grisapkl.firebaseapp.com",
            projectId: "grisapkl",
            storageBucket: "grisapkl.firebasestorage.app",
            messagingSenderId: "352687462221",
            appId: "1:352687462221:web:4293afee4d3d79d0678904"
        };

        if (!firebase.apps.length) {
            firebase.initializeApp(firebaseConfig);
        }
        const messaging = firebase.messaging();

        // ============================================================
        // FIX #1: Fungsi inti dengan SW activation wait (Android fix)
        // ============================================================
        async function registerAndGetToken() {
            if (!('serviceWorker' in navigator)) {
                console.warn('[FCM] Browser tidak support Service Worker.');
                return;
            }

            try {
                const registration = await navigator.serviceWorker.register('/firebase-messaging-sw.js');
                await navigator.serviceWorker.ready; // Tunggu SW ready

                // TAMBAHAN: Pastikan SW sudah jadi controller
                // Kalau belum, tunggu maksimal 5 detik
                if (!navigator.serviceWorker.controller) {
                    console.log('[FCM] Menunggu SW jadi controller...');
                    await new Promise((resolve) => {
                        const timeout = setTimeout(resolve, 5000); // max tunggu 5 detik
                        navigator.serviceWorker.addEventListener('controllerchange', () => {
                            clearTimeout(timeout);
                            resolve();
                        });
                    });
                }

                const currentToken = await messaging.getToken({
                    vapidKey: '{{ config('services.firebase.vapid_key') }}',
                    serviceWorkerRegistration: registration
                });

                if (currentToken) {
                    console.log('[FCM] Token didapat:', currentToken.substring(0, 20) + '...');
                    await saveTokenToDatabase(currentToken);
                } else {
                    console.warn('[FCM] Token kosong, permission mungkin belum granted.');
                }
            } catch (err) {
                console.error('[FCM] Error saat register/getToken:', err);
            }
        }

        // ============================================================
        // FIX #2: requestNotificationPermission yang proper
        // ============================================================
        async function requestNotificationPermission() {
            const permission = await Notification.requestPermission();

            // Beri tahu Alpine.js kalau status izin baru saja diupdate
            window.dispatchEvent(new CustomEvent('update-permission'));

            if (permission === 'granted') {
                console.log('[FCM] Izin notifikasi diberikan!');
                await registerAndGetToken();
            } else {
                console.warn('[FCM] Izin notifikasi ditolak:', permission);
            }
        }

        // ============================================================
        // FIX #3: saveTokenToDatabase dengan error handling & retry
        // ============================================================
        async function saveTokenToDatabase(token) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            if (!csrfToken) {
                console.error('[FCM] CSRF token tidak ditemukan!');
                return;
            }

            try {
                const response = await fetch('/save-fcm-token', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        token
                    })
                });

                const data = await response.json();

                if (data.success) {
                    console.log('[FCM] Token berhasil disimpan ke server.');
                } else {
                    console.error('[FCM] Server tolak token:', data.message);
                }
            } catch (err) {
                console.error('[FCM] Gagal kirim token ke server:', err);
            }
        }

        // ============================================================
        // FIX #4: Auto-init saat page load kalau sudah pernah granted
        // ============================================================
        // FIX #4 REVISED: Tunggu SW controller ready dulu sebelum getToken (Mobile/PWA fix)
        document.addEventListener('DOMContentLoaded', function() {
            if (!('Notification' in window) || !('serviceWorker' in navigator)) return;
            if (Notification.permission !== 'granted') return;

            console.log('[FCM] Permission granted, menunggu SW siap...');

            // Tunggu SW benar-benar controlling page ini
            if (navigator.serviceWorker.controller) {
                // SW sudah active, langsung getToken
                console.log('[FCM] SW sudah aktif, langsung getToken...');
                registerAndGetToken();
            } else {
                // SW belum jadi controller (fresh install / reload pertama)
                // Tunggu event controllerchange
                navigator.serviceWorker.addEventListener('controllerchange', function() {
                    console.log('[FCM] SW baru aktif via controllerchange, getToken...');
                    registerAndGetToken();
                });

                // Fallback: kalau 3 detik SW belum jadi controller, coba tetap getToken
                // Ini handle kasus SW sudah register tapi tidak trigger controllerchange
                setTimeout(function() {
                    if (Notification.permission === 'granted') {
                        console.log('[FCM] Fallback timeout: mencoba getToken...');
                        registerAndGetToken();
                    }
                }, 3000);
            }
        });

        // ============================================================
        // FIX #5: handleBellClick handle semua state permission
        // ============================================================
        function handleBellClick() {
            if (!('Notification' in window)) {
                return; // Silently abort jika browser tidak support
            }

            const permission = Notification.permission;

            if (permission === 'granted') {
                // HAPUS SWEETALERT DI SINI!
                // Biarkan saja dia nge-refresh token diam-diam di background.
                registerAndGetToken();
                return;
            }

            if (permission === 'denied') {
                Swal.fire({
                    title: 'Notifikasi Diblokir',
                    text: 'Silakan aktifkan notifikasi secara manual di pengaturan browser Anda.',
                    icon: 'error',
                    confirmButtonColor: '#3525cd',
                });
                return;
            }

            // permission === 'default' → minta izin dulu
            Swal.fire({
                title: 'Aktifkan Notifikasi?',
                text: 'Izinkan aplikasi mengirimkan pemberitahuan penting.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3525cd',
                confirmButtonText: 'Ya, Izinkan',
                cancelButtonText: 'Nanti saja'
            }).then((result) => {
                if (result.isConfirmed) {
                    requestNotificationPermission();
                }
            });
        }

        // ============================================================
        // Foreground message handler (tidak berubah)
        // ============================================================
        const userHomeRoute = "{{ $homeRoute }}";

        messaging.onMessage((payload) => {
            Swal.fire({
                title: payload.notification.title,
                text: payload.notification.body,
                icon: 'info',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 6000,
                timerProgressBar: true,
                customClass: {
                    popup: 'cursor-pointer shadow-xl border border-indigo-100'
                },
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                    toast.addEventListener('click', () => {
                        window.location.href = userHomeRoute;
                    });
                }
            });
            window.dispatchEvent(new CustomEvent('new-notification'));
        });
    </script>
</body>

</html>
