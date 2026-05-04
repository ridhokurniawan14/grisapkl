<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Sedang Diperbarui - Grisa PKL</title>
    <style>
        :root {
            --bg-body: #f8fafc;
            --bg-card: rgba(255, 255, 255, 0.85);
            --text-main: #0f172a;
            --text-desc: #475569;
            --border-color: rgba(255, 255, 255, 0.5);
            --accent: #f59e0b;
        }

        @media (prefers-color-scheme: dark) {
            :root {
                --bg-body: #020617;
                --bg-card: rgba(30, 41, 59, 0.75);
                --text-main: #f8fafc;
                --text-desc: #cbd5e1;
                --border-color: rgba(255, 255, 255, 0.1);
                --accent: #fbbf24;
            }
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            box-sizing: border-box;
            overflow: hidden;
            /* Mencegah scroll karena animasi */
        }

        /* BACKGROUND ANIMASI BLOB CAHAYA */
        .bg-shapes {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: -1;
        }

        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.5;
            animation: moveBlob 15s infinite alternate ease-in-out;
        }

        .blob-1 {
            background: #f59e0b;
            width: 400px;
            height: 400px;
            top: -100px;
            left: -100px;
        }

        .blob-2 {
            background: #ef4444;
            width: 350px;
            height: 350px;
            bottom: -50px;
            right: -50px;
            animation-delay: -5s;
        }

        .blob-3 {
            background: #3b82f6;
            width: 300px;
            height: 300px;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            animation-delay: -2s;
            opacity: 0.3;
        }

        @keyframes moveBlob {
            0% {
                transform: scale(1) translate(0, 0);
            }

            100% {
                transform: scale(1.2) translate(50px, 80px);
            }
        }

        /* GLASSMORPHISM CARD */
        .maintenance-card {
            background: var(--bg-card);
            backdrop-filter: blur(16px);
            /* Efek Kaca Blur */
            -webkit-backdrop-filter: blur(16px);
            padding: 45px 35px;
            border-radius: 24px;
            border: 1px solid var(--border-color);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            max-width: 480px;
            width: 100%;
            text-align: center;
            border-top: 5px solid var(--accent);
            animation: fadeUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
            transform: translateY(30px);
        }

        /* Animasi Area Illustration */
        .illustration-container {
            position: relative;
            width: 120px;
            height: 120px;
            margin: 0 auto 30px auto;
            color: var(--accent);
        }

        .gear-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0.2;
            animation: spinSlow 12s linear infinite;
        }

        .wrench-icon {
            position: absolute;
            top: 20px;
            left: 20px;
            width: 80px;
            height: 80px;
            animation: float 3s ease-in-out infinite;
        }

        h1 {
            font-size: 26px;
            margin: 0 0 15px 0;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        p {
            font-size: 15px;
            color: var(--text-desc);
            line-height: 1.6;
            margin: 0 0 30px 0;
        }

        .loading-dots::after {
            content: '.';
            animation: dots 2s steps(5, end) infinite;
        }

        .footer {
            margin-top: 10px;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
            font-size: 13px;
            color: var(--text-desc);
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        @keyframes fadeUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes spinSlow {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px) rotate(0deg);
            }

            50% {
                transform: translateY(-10px) rotate(5deg);
            }
        }

        @keyframes dots {

            0%,
            20% {
                color: transparent;
                text-shadow: .25em 0 0 transparent, .5em 0 0 transparent;
            }

            40% {
                color: var(--accent);
                text-shadow: .25em 0 0 transparent, .5em 0 0 transparent;
            }

            60% {
                text-shadow: .25em 0 0 var(--accent), .5em 0 0 transparent;
            }

            80%,
            100% {
                text-shadow: .25em 0 0 var(--accent), .5em 0 0 var(--accent);
            }
        }

        @media (max-width: 480px) {
            .maintenance-card {
                padding: 35px 25px;
            }

            h1 {
                font-size: 22px;
            }
        }
    </style>
</head>

<body>

    <!-- Animasi Background Melayang -->
    <div class="bg-shapes">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="blob blob-3"></div>
    </div>

    <div class="maintenance-card">
        <div class="illustration-container">
            <!-- Gear Berputar -->
            <svg class="gear-bg" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M19.14,12.94c0.04-0.3,0.06-0.61,0.06-0.94c0-0.32-0.02-0.64-0.06-0.94l2.03-1.58c0.18-0.14,0.23-0.41,0.12-0.61 l-1.92-3.32c-0.12-0.22-0.37-0.29-0.59-0.22l-2.39,0.96c-0.5-0.38-1.03-0.7-1.62-0.94L14.4,2.81c-0.04-0.24-0.24-0.41-0.48-0.41 h-3.84c-0.24,0-0.43,0.17-0.47,0.41L9.25,5.35C8.66,5.59,8.12,5.92,7.63,6.29L5.24,5.33c-0.22-0.08-0.47,0-0.59,0.22L2.73,8.87 C2.62,9.08,2.66,9.34,2.86,9.48l2.03,1.58C4.84,11.36,4.8,11.69,4.8,12s0.02,0.64,0.06,0.94l-2.03,1.58 c-0.18,0.14-0.23,0.41-0.12,0.61l1.92,3.32c0.12,0.22,0.37,0.29,0.59,0.22l2.39-0.96c0.5,0.38,1.03,0.7,1.62,0.94l0.36,2.54 c0.05,0.24,0.24,0.41,0.48,0.41h3.84c0.24,0,0.43-0.17,0.47-0.41l0.36-2.54c0.59-0.24,1.13-0.56,1.62-0.94l2.39,0.96 c0.22,0.08,0.47,0,0.59-0.22l1.92-3.32c0.12-0.22,0.07-0.49-0.12-0.61L19.14,12.94z M12,15.6c-1.98,0-3.6-1.62-3.6-3.6 s1.62-3.6,3.6-3.6s3.6,1.62,3.6,3.6S13.98,15.6,12,15.6z" />
            </svg>
            <!-- Wrench Mengambang -->
            <svg class="wrench-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                </path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
        </div>

        <h1>Sistem Dalam Perbaikan</h1>

        <p>
            Mohon maaf, aplikasi <b>Grisa PKL</b> saat ini sedang dalam proses sinkronisasi dan peningkatan server<span
                class="loading-dots"></span><br><br>
            Silakan kembali beberapa saat lagi. Kami sedang bekerja keras agar sistem berjalan lebih optimal!
        </p>

        <div class="footer">
            &copy; {{ date('Y') }} SMK PGRI 1 GIRI BANYUWANGI
        </div>
    </div>

</body>

</html>
