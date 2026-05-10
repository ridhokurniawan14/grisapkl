<!DOCTYPE html>
<html class="light" lang="id">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />
    <title>{{ $title ?? 'GrisaPKL' }}</title>

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
        <link rel="icon" href="/favicon.ico">
    @endif

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;600;700;800&display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "secondary-fixed": "#c9e6ff",
                        "surface-container-highest": "#e4e1ee",
                        "error": "#ba1a1a",
                        "tertiary-fixed": "#ffdbcc",
                        "on-surface-variant": "#464555",
                        "surface-bright": "#fcf8ff",
                        "on-tertiary": "#ffffff",
                        "error-container": "#ffdad6",
                        "on-error": "#ffffff",
                        "inverse-surface": "#302f39",
                        "on-primary": "#ffffff",
                        "background": "#fcf8ff",
                        "on-primary-fixed-variant": "#3323cc",
                        "primary-fixed": "#e2dfff",
                        "secondary": "#006591",
                        "surface-tint": "#4d44e3",
                        "inverse-primary": "#c3c0ff",
                        "on-primary-container": "#dad7ff",
                        "tertiary": "#7e3000",
                        "surface-container": "#f0ecf9",
                        "surface-variant": "#e4e1ee",
                        "on-error-container": "#93000a",
                        "primary-container": "#4f46e5",
                        "on-tertiary-container": "#ffd2be",
                        "on-surface": "#1b1b24",
                        "on-secondary-fixed": "#001e2f",
                        "tertiary-fixed-dim": "#ffb695",
                        "surface": "#fcf8ff",
                        "tertiary-container": "#a44100",
                        "on-primary-fixed": "#0f0069",
                        "surface-container-high": "#eae6f4",
                        "on-secondary-fixed-variant": "#004c6e",
                        "outline-variant": "#c7c4d8",
                        "surface-container-low": "#f5f2ff",
                        "primary-fixed-dim": "#c3c0ff",
                        "on-secondary": "#ffffff",
                        "inverse-on-surface": "#f3effc",
                        "secondary-container": "#39b8fd",
                        "on-tertiary-fixed-variant": "#7b2f00",
                        "on-background": "#1b1b24",
                        "primary": "#3525cd",
                        "secondary-fixed-dim": "#89ceff",
                        "on-tertiary-fixed": "#351000",
                        "outline": "#777587",
                        "surface-container-lowest": "#ffffff",
                        "surface-dim": "#dcd8e5",
                        "on-secondary-container": "#004666"
                    },
                    spacing: {
                        "margin-mobile": "16px",
                        "base": "4px",
                        "gutter": "12px",
                        "stack-md": "16px",
                        "stack-sm": "8px",
                        "touch-target": "48px",
                        "stack-lg": "24px"
                    },
                    fontFamily: {
                        "h1": ["Lexend"],
                        "h2": ["Lexend"],
                        "body-lg": ["Lexend"],
                        "label-md": ["Lexend"],
                        "body-md": ["Lexend"],
                        "button": ["Lexend"]
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
@stack('scripts')

<body
    class="antialiased flex flex-col font-body-md text-[14px] w-full min-h-[100dvh] bg-slate-100 sm:items-center sm:justify-center m-0 p-0">

    {{-- KANVAS PWA --}}
    <div id="app-canvas"
        class="relative w-full h-[100dvh] sm:w-[390px] sm:h-[800px] sm:max-h-[90dvh] bg-background flex flex-col overflow-hidden sm:rounded-[2.5rem] sm:shadow-2xl sm:border-[8px] sm:border-slate-800">

        @php
            $isProfile = request()->routeIs('siswa.profil') || request()->routeIs('pembimbing.profil');
            $isBeranda = request()->routeIs('siswa.beranda') || request()->routeIs('pembimbing.beranda');

            $headerBg = $isProfile
                ? 'bg-gradient-to-b from-[#1c10a0] via-[#2d1fc5] to-[#3525cd]'
                : 'bg-white/80 backdrop-blur-md border-b border-slate-200/60';
            $headerText = $isProfile ? 'text-white' : 'text-primary';
            $headerIcon = $isProfile ? 'text-white hover:bg-white/10' : 'text-primary hover:bg-slate-50';

            $user = auth()->user();
            $studentData = \App\Models\Student::where('user_id', $user->id ?? 0)->first();
            $avatarPath = $studentData->avatar ?? ($user->avatar ?? null);

            $name = $user->name ?? 'Siswa';
            $initials = collect(explode(' ', $name))->map(fn($s) => substr($s, 0, 1))->take(2)->join('');
            $initials = strtoupper($initials);
        @endphp

        <header
            class="{{ $headerBg }} absolute top-0 w-full z-50 flex justify-between items-center px-4 h-16 transition-colors duration-300">

            @if ($isBeranda)
                {{-- KHUSUS BERANDA: Tampilkan Nama Aplikasi --}}
                <div class="flex items-center">
                    <h1 class="text-xl font-extrabold text-[#3525cd] font-['Lexend'] tracking-tight">GrisaPKL</h1>
                </div>
            @elseif (!$isProfile)
                {{-- HALAMAN LAIN: Tampilkan Avatar & Sapaan --}}
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
                                $fullName = auth()->user()->name ?? 'Siswa';
                                $nameParts = explode(' ', trim($fullName));

                                $displayName = $nameParts[0] ?? 'Siswa';

                                // Kalau kata pertama <= 2 karakter, ambil kata kedua
                                if (strlen($displayName) <= 2 && isset($nameParts[1])) {
                                    $displayName .= ' ' . $nameParts[1];
                                }
                            @endphp

                            {{ ucfirst($displayName) }} </span>
                    </div>
                </div>
            @else
                {{-- HALAMAN PROFIL: Tampilkan Back Button Dinamis --}}
                @php
                    // Cek role user, kalau guru/pembimbing arahkan ke beranda guru, kalau siswa ke absen siswa
                    $backRoute =
                        auth()->user()->hasRole('guru') || auth()->user()->hasRole('pembimbing')
                            ? route('pembimbing.beranda')
                            : route('siswa.absen');
                @endphp
                <a href="{{ $backRoute }}"
                    class="w-10 h-10 flex items-center justify-center rounded-full text-white hover:bg-white/10 active:scale-95 transition-all flex-shrink-0"
                    title="Kembali">
                    <span class="material-symbols-outlined">arrow_back</span>
                </a>
            @endif

            <div class="flex-grow"></div>

            <button
                class="w-10 h-10 flex items-center justify-center rounded-full {{ $headerIcon }} transition-all active:scale-95 flex-shrink-0">
                <span class="material-symbols-outlined">notifications</span>
            </button>
        </header>

        @php
            // Hapus padding atas (pt-20) khusus untuk Profil dan Dudika agar Background bisa mentok ke atas
            $isProfileOrDudika = request()->routeIs('siswa.dudika');
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
                {{-- tambah satu blob di pojok kanan bawah biar makin balanced --}}
                <div
                    class="absolute -bottom-10 -right-10 w-60 h-60 bg-indigo-400/10 rounded-full blur-[70px] animate-pulse">
                </div>
            </div>
            {{ $slot }}
        </main>

        <!-- Bottom Navigation -->
        <x-bottom-nav />
    </div>

    @livewireScripts
</body>

</html>
