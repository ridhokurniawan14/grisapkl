<!DOCTYPE html>
<html class="light" lang="id">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>{{ $title ?? 'Grisa PKL' }}</title>
    <!-- Masukkan CDN Tailwind dan Script Config persis seperti di guest.blade.php tadi di sini bro, biar nggak kepanjangan saya skip teksnya -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />
    <style>
        body {
            background-color: #fcf8ff;
            color: #1b1b24;
            -webkit-tap-highlight-color: transparent;
        }
    </style>
</head>

<body class="antialiased min-h-screen flex flex-col font-['Lexend'] text-[14px]">

    <!-- TopAppBar -->
    <header
        class="bg-white/80 backdrop-blur-md fixed top-0 w-full z-50 border-b border-slate-100 shadow-sm flex justify-between items-center px-4 h-16">
        <div class="flex items-center justify-center w-10 h-10 rounded-full bg-slate-200 overflow-hidden flex-shrink-0">
            <!-- Nanti src gambar ganti dengan foto profil siswa -->
            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'User') }}&background=4f46e5&color=fff"
                class="w-full h-full object-cover" />
        </div>
        <h1 class="text-indigo-600 font-semibold tracking-tight text-lg font-extrabold flex-grow text-center">PKL
            Connect</h1>
        <button
            class="w-10 h-10 flex items-center justify-center rounded-full text-indigo-600 hover:bg-slate-50 transition-all duration-200 active:scale-95 flex-shrink-0">
            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 0;">notifications</span>
        </button>
    </header>

    <!-- Main Content -->
    <main class="flex-grow pt-24 pb-32 px-4 flex flex-col w-full max-w-md mx-auto">
        {{ $slot }}
    </main>

    <!-- BottomNavBar -->
    <nav
        class="bg-white fixed bottom-0 w-full z-50 rounded-t-2xl border-t border-slate-100 shadow-[0_-4px_12px_rgba(0,0,0,0.05)] left-0 flex justify-around items-center px-2 py-3 pb-safe">
        <!-- Beranda -->
        <button class="flex flex-col items-center justify-center text-slate-400 hover:text-indigo-500 w-16">
            <span class="material-symbols-outlined mb-1">home</span>
            <span class="text-[10px] font-medium">Beranda</span>
        </button>
        <!-- Absen (Active) -->
        <button
            class="flex flex-col items-center justify-center text-indigo-600 font-bold bg-indigo-50 rounded-xl px-3 py-1 -mt-2">
            <span class="material-symbols-outlined mb-1" style="font-variation-settings: 'FILL' 1;">fingerprint</span>
            <span class="text-[10px] font-medium">Absen</span>
        </button>
        <!-- Profil -->
        <button class="flex flex-col items-center justify-center text-slate-400 hover:text-indigo-500 w-16">
            <span class="material-symbols-outlined mb-1">person</span>
            <span class="text-[10px] font-medium">Profil</span>
        </button>
    </nav>

    @livewireScripts
</body>

</html>
