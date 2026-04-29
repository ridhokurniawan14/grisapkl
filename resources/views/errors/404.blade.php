<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Halaman Tidak Ditemukan</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 flex items-center justify-center min-h-screen p-4">

    <div class="max-w-md w-full text-center">
        <svg class="mx-auto h-40 w-40 text-blue-500 mb-8 animate-bounce" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>

        <h1 class="text-7xl font-black text-gray-800 mb-2">404</h1>
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Waduh! Halaman Kesasar Bro...</h2>
        <p class="text-gray-500 mb-8">
            Sepertinya URL yang kamu masukkan salah, atau halamannya sudah dihapus dari sistem.
        </p>

        <a href="#" onclick="history.back(); return false;"
            class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-full shadow-lg transition-transform transform hover:-translate-y-1">
            Kembali ke Jalan yang Benar
        </a>
    </div>

</body>

</html>
