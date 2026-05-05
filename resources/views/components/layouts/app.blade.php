<!DOCTYPE html>
<html class="light" lang="id">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />
    <title>{{ $title ?? 'GrisaPKL' }}</title>

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

<body
    class="antialiased flex flex-col font-body-md text-[14px] w-full min-h-[100dvh] bg-slate-100 sm:items-center sm:justify-center m-0 p-0">

    {{-- KANVAS PWA: Full layar di HP, Berbentuk Mockup di Desktop --}}
    <div
        class="relative w-full h-[100dvh] sm:w-[390px] sm:h-[800px] sm:max-h-[90dvh] bg-background flex flex-col overflow-hidden sm:rounded-[2.5rem] sm:shadow-2xl sm:border-[8px] sm:border-slate-800">

        <!-- TopAppBar -->
        <header
            class="bg-white/80 backdrop-blur-md absolute top-0 w-full z-50 border-b border-slate-100 shadow-sm flex justify-between items-center px-4 h-16">
            <!-- Avatar Siswa -->
            <div
                class="flex items-center justify-center w-10 h-10 rounded-full bg-surface-variant overflow-hidden flex-shrink-0">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'Siswa') }}&background=4f46e5&color=fff"
                    class="w-full h-full object-cover" />
            </div>
            <h1 class="text-primary font-semibold tracking-tight text-lg flex-grow text-center">GrisaPKL</h1>
            <!-- Notifikasi -->
            <button
                class="w-10 h-10 flex items-center justify-center rounded-full text-primary hover:bg-slate-50 transition-all active:scale-95 flex-shrink-0">
                <span class="material-symbols-outlined">notifications</span>
            </button>
        </header>

        <!-- Area Konten Berubah-ubah (Disuntik oleh Livewire) -->
        <main class="flex-grow pt-20 pb-24 px-4 overflow-y-auto w-full">
            {{ $slot }}
        </main>

        <!-- Panggil Komponen BottomNavBar Dinamis -->
        <x-bottom-nav />
    </div>

    @livewireScripts
</body>

</html>
