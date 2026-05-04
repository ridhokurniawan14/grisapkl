<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>{{ $title ?? 'Grisa PKL' }}</title>

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

        body {
            min-height: max(884px, 100dvh);
        }
    </style>

    <!-- Tailwind CSS CDN (Sesuai desainmu) -->
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

<body
    class="bg-gradient-to-br from-surface-container-lowest via-surface-bright to-surface-container-high min-h-screen flex items-center justify-center p-4 relative overflow-hidden font-body-md text-on-surface">
    <!-- Decorative Elements -->
    <div
        class="absolute top-[-10%] left-[-10%] w-[50vw] h-[50vw] bg-primary opacity-[0.05] rounded-full blur-[100px] pointer-events-none">
    </div>
    <div
        class="absolute bottom-[-10%] right-[-10%] w-[40vw] h-[40vw] bg-secondary opacity-[0.05] rounded-full blur-[80px] pointer-events-none">
    </div>

    {{ $slot }}

    @livewireScripts
</body>

</html>
