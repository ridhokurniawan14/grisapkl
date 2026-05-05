<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    {{-- MANTRA PWA: Cegah zoom paksa di iOS dengan maximum-scale=1.0 dan user-scalable=no --}}
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />

    <title>{{ $title ?? 'Grisa PKL' }}</title>

    @php
        $school = \App\Models\SchoolProfile::first();
        $dynamicFavicon = $school && $school->logo_path ? asset('storage/' . $school->logo_path) : null;
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

    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        .material-symbols-outlined.fill {
            font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        /* MANTRA ANTI-SCROLL: Paksa body persis seukuran layar, tidak lebih 1 pixel pun! */
        body {
            height: 100dvh;
            width: 100vw;
            margin: 0;
            padding: 0;
            overflow: hidden;
            /* Kunci scroll mutlak dari akar */
            -webkit-tap-highlight-color: transparent;
            /* Hilangkan efek blok biru saat tap di HP */
        }
    </style>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "surface-bright": "#fcf8ff",
                        "primary-fixed-dim": "#c3c0ff",
                        "on-secondary-fixed-variant": "#004c6e",
                        "inverse-primary": "#c3c0ff",
                        "surface-container-highest": "#e4e1ee",
                        "on-secondary-fixed": "#001e2f",
                        "on-tertiary": "#ffffff",
                        "primary-container": "#4f46e5",
                        "tertiary": "#7e3000",
                        "secondary": "#006591",
                        "on-surface-variant": "#464555",
                        "error-container": "#ffdad6",
                        "tertiary-fixed-dim": "#ffb695",
                        "on-primary": "#ffffff",
                        "error": "#ba1a1a",
                        "surface": "#fcf8ff",
                        "on-secondary-container": "#004666",
                        "on-primary-fixed": "#0f0069",
                        "on-tertiary-container": "#ffd2be",
                        "background": "#fcf8ff",
                        "tertiary-fixed": "#ffdbcc",
                        "on-error-container": "#93000a",
                        "secondary-fixed-dim": "#89ceff",
                        "on-tertiary-fixed-variant": "#7b2f00",
                        "on-tertiary-fixed": "#351000",
                        "on-secondary": "#ffffff",
                        "secondary-container": "#39b8fd",
                        "surface-container": "#f0ecf9",
                        "on-primary-fixed-variant": "#3323cc",
                        "inverse-on-surface": "#f3effc",
                        "surface-container-high": "#eae6f4",
                        "primary-fixed": "#e2dfff",
                        "surface-dim": "#dcd8e5",
                        "surface-variant": "#e4e1ee",
                        "on-primary-container": "#dad7ff",
                        "surface-tint": "#4d44e3",
                        "outline-variant": "#c7c4d8",
                        "surface-container-lowest": "#ffffff",
                        "primary": "#3525cd",
                        "inverse-surface": "#302f39",
                        "on-background": "#1b1b24",
                        "surface-container-low": "#f5f2ff",
                        "outline": "#777587",
                        "tertiary-container": "#a44100",
                        "on-error": "#ffffff",
                        "secondary-fixed": "#c9e6ff",
                        "on-surface": "#1b1b24"
                    },
                    fontFamily: {
                        "label-md": ["Lexend"],
                        "h2": ["Lexend"],
                        "body-md": ["Lexend"],
                        "body-lg": ["Lexend"],
                        "h1": ["Lexend"],
                        "button": ["Lexend"]
                    }
                }
            }
        }
    </script>
</head>

{{-- Hapus padding (p-4) agar kanvas benar-benar penuh dari ujung ke ujung --}}

<body
    class="bg-gradient-to-br from-surface-container-lowest via-surface-bright to-surface-container-high font-body-md text-on-surface antialiased">

    <!-- Decorative Elements -->
    <div
        class="absolute top-[-10%] left-[-10%] w-[50vw] h-[50vw] bg-primary opacity-[0.05] rounded-full blur-[100px] pointer-events-none">
    </div>
    <div
        class="absolute bottom-[-10%] right-[-10%] w-[40vw] h-[40vw] bg-secondary opacity-[0.05] rounded-full blur-[80px] pointer-events-none">
    </div>

    {{-- Tempat Komponen Livewire Dimuat --}}
    {{ $slot }}

    @livewireScripts
</body>

</html>
