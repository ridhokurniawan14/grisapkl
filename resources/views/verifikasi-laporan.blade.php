<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Keaslian Laporan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=Orbitron:wght@400;700;900&display=swap"
        rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #000;
            min-height: 100vh;
            font-family: 'Share Tech Mono', monospace;
            color: #00ff41;
            overflow-x: hidden;
        }

        /* Matrix rain canvas */
        canvas#matrix {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            opacity: 0.18;
        }

        /* CRT scanline overlay */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: repeating-linear-gradient(0deg,
                    transparent,
                    transparent 2px,
                    rgba(0, 0, 0, 0.12) 2px,
                    rgba(0, 0, 0, 0.12) 4px);
            pointer-events: none;
            z-index: 1;
        }

        .terminal-wrap {
            position: relative;
            z-index: 2;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .terminal {
            width: 100%;
            max-width: 520px;
            border: 1px solid #00ff41;
            box-shadow:
                0 0 40px rgba(0, 255, 65, 0.15),
                0 0 80px rgba(0, 255, 65, 0.05),
                inset 0 0 40px rgba(0, 255, 65, 0.02);
            background: rgba(0, 8, 0, 0.92);
            backdrop-filter: blur(2px);
            position: relative;
        }

        .terminal::before {
            content: '';
            position: absolute;
            top: -1px;
            left: 15%;
            right: 15%;
            height: 1px;
            background: linear-gradient(90deg, transparent, #00ff41, transparent);
        }

        /* Top bar */
        .top-bar {
            background: rgba(0, 17, 0, 0.95);
            border-bottom: 1px solid #002800;
            padding: 8px 14px;
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .win-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }

        .win-dot.r {
            background: #ff3b30;
            box-shadow: 0 0 6px #ff3b30;
        }

        .win-dot.y {
            background: #ffcc00;
            box-shadow: 0 0 6px #ffcc00;
        }

        .win-dot.g {
            background: #00ff41;
            box-shadow: 0 0 6px #00ff41;
        }

        .top-bar-title {
            margin-left: auto;
            font-size: 10px;
            color: #00aa33;
            letter-spacing: 2px;
        }

        .cursor {
            animation: blink 1s step-end infinite;
        }

        @keyframes blink {
            50% {
                opacity: 0;
            }
        }

        /* Header */
        .terminal-header {
            background: linear-gradient(180deg, rgba(0, 26, 0, 0.95), rgba(0, 10, 0, 0.95));
            padding: 26px 20px 20px;
            text-align: center;
            border-bottom: 1px solid #002800;
        }

        /* Logo — no ring, just image/icon */
        .school-logo {
            width: 68px;
            height: 68px;
            object-fit: contain;
            display: block;
            margin: 0 auto 12px;
            filter: drop-shadow(0 0 10px rgba(0, 255, 65, 0.4));
        }

        .school-logo-fallback {
            width: 48px;
            height: 48px;
            display: block;
            margin: 0 auto 12px;
            stroke: #00ff41;
            stroke-width: 1.5;
            fill: none;
            filter: drop-shadow(0 0 8px rgba(0, 255, 65, 0.4));
        }

        .sys-label {
            font-size: 10px;
            color: #006622;
            letter-spacing: 4px;
            margin-bottom: 5px;
        }

        .sys-title {
            font-family: 'Orbitron', monospace;
            font-size: 17px;
            font-weight: 700;
            color: #00ff41;
            text-shadow: 0 0 15px rgba(0, 255, 65, 0.5);
            letter-spacing: 3px;
        }

        .school-name-tag {
            font-size: 11px;
            color: #00aa33;
            letter-spacing: 2px;
            margin-top: 5px;
        }

        /* Status bar */
        .status-bar {
            background: rgba(0, 8, 0, 0.9);
            border-bottom: 1px solid #002200;
            padding: 6px 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 10px;
            color: #006622;
            letter-spacing: 1px;
        }

        .status-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #00ff41;
            box-shadow: 0 0 8px #00ff41;
            animation: blink-d 1.5s ease-in-out infinite;
            flex-shrink: 0;
        }

        @keyframes blink-d {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.2;
            }
        }

        .status-node {
            margin-left: auto;
            opacity: 0.5;
        }

        /* Body */
        .terminal-body {
            padding: 22px;
        }

        /* CAPTCHA */
        .lock-section {
            text-align: center;
            margin-bottom: 22px;
        }

        .lock-icon-ring {
            width: 58px;
            height: 58px;
            border: 2px dashed #00aa33;
            margin: 0 auto 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: slow-spin 10s linear infinite;
            opacity: 0.8;
        }

        @keyframes slow-spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .lock-title {
            font-family: 'Orbitron', monospace;
            font-size: 13px;
            color: #00ff41;
            letter-spacing: 2px;
        }

        .lock-sub {
            font-size: 11px;
            color: #004400;
            margin-top: 4px;
            letter-spacing: 1px;
        }

        .error-terminal {
            border: 1px solid #ff3333;
            background: rgba(26, 0, 0, 0.85);
            color: #ff4444;
            padding: 10px 14px;
            font-size: 11px;
            margin-bottom: 16px;
            letter-spacing: 1px;
        }

        .error-terminal::before {
            content: '[ERR_403] ';
            color: #ff0000;
        }

        .input-label-term {
            font-size: 11px;
            color: #007722;
            letter-spacing: 2px;
            display: block;
            margin-bottom: 8px;
        }

        .input-cmd-wrap {
            display: flex;
            align-items: center;
            border: 1px solid #00ff41;
            background: rgba(0, 10, 0, 0.8);
            box-shadow: 0 0 10px rgba(0, 255, 65, 0.08);
        }

        .cmd-prompt {
            padding: 11px 13px;
            color: #00ff41;
            font-family: 'Share Tech Mono', monospace;
            font-size: 13px;
            border-right: 1px solid #002800;
            white-space: nowrap;
            user-select: none;
        }

        .cmd-input {
            background: transparent;
            border: none;
            outline: none;
            color: #00ff41;
            font-family: 'Share Tech Mono', monospace;
            font-size: 14px;
            padding: 11px 13px;
            width: 100%;
            caret-color: #00ff41;
        }

        .cmd-input::placeholder {
            color: #003300;
        }

        .cmd-input[type=number]::-webkit-inner-spin-button,
        .cmd-input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
        }

        .cmd-input[type=number] {
            -moz-appearance: textfield;
        }

        .btn-execute {
            width: 100%;
            background: rgba(0, 20, 0, 0.8);
            border: 1px solid #00ff41;
            color: #00ff41;
            font-family: 'Orbitron', monospace;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 3px;
            padding: 14px;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 16px;
        }

        .btn-execute:hover {
            background: rgba(0, 40, 0, 0.9);
            box-shadow: 0 0 25px rgba(0, 255, 65, 0.25);
            letter-spacing: 4px;
        }

        /* Verified header */
        .verified-header {
            text-align: center;
            margin-bottom: 18px;
        }

        .verified-icon {
            width: 62px;
            height: 62px;
            border: 2px solid #00ff41;
            border-radius: 50%;
            margin: 0 auto 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 28px rgba(0, 255, 65, 0.4);
            background: rgba(0, 17, 0, 0.9);
        }

        .verified-icon svg {
            width: 30px;
            height: 30px;
            stroke: #00ff41;
            stroke-width: 2;
            fill: none;
        }

        .verified-title {
            font-family: 'Orbitron', monospace;
            font-size: 14px;
            color: #00ff41;
            letter-spacing: 2px;
            text-shadow: 0 0 12px rgba(0, 255, 65, 0.5);
        }

        .verified-sub {
            font-size: 11px;
            color: #007722;
            margin-top: 4px;
            letter-spacing: 1px;
        }

        /* === 2-COLUMN DATA GRID === */
        .data-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .data-card {
            border: 1px solid #002a00;
            background: rgba(0, 10, 0, 0.75);
            padding: 12px 14px;
        }

        .data-card.full {
            grid-column: 1 / -1;
        }

        .data-key {
            font-size: 10px;
            color: #004400;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .data-val {
            font-size: 13px;
            color: #00ff41;
            line-height: 1.45;
        }

        .data-muted {
            font-size: 11px;
            color: #00aa33;
            margin-top: 2px;
        }

        /* Timestamp */
        .timestamp-card {
            border: 1px solid #00ff41 !important;
            background: rgba(0, 17, 0, 0.8) !important;
            text-align: center;
            box-shadow: 0 0 18px rgba(0, 255, 65, 0.1);
        }

        .timestamp-label {
            font-size: 10px;
            color: #007722;
            letter-spacing: 3px;
            margin-bottom: 5px;
        }

        .timestamp-val {
            font-family: 'Orbitron', monospace;
            font-size: 15px;
            color: #00ff41;
            text-shadow: 0 0 10px rgba(0, 255, 65, 0.4);
        }

        /* Footer */
        .terminal-footer {
            border-top: 1px solid #002200;
            background: rgba(0, 6, 0, 0.9);
            padding: 10px;
            text-align: center;
            font-size: 10px;
            color: #003300;
            letter-spacing: 2px;
        }

        .terminal-footer span {
            color: #004400;
        }

        /* ===== RESPONSIVE MOBILE ===== */
        @media (max-width: 480px) {
            .terminal-wrap {
                padding: 12px;
            }

            .terminal-body {
                padding: 16px;
            }

            .sys-title {
                font-size: 14px;
                letter-spacing: 2px;
            }

            .sys-label {
                font-size: 9px;
                letter-spacing: 3px;
            }

            /* 1 kolom di mobile */
            .data-grid {
                grid-template-columns: 1fr;
            }

            .data-card.full {
                grid-column: 1;
            }

            .top-bar-title {
                font-size: 9px;
                letter-spacing: 1px;
            }

            .cmd-prompt {
                font-size: 11px;
                padding: 10px 10px;
            }
        }
    </style>
</head>

<body>

    {{-- Matrix Rain Canvas --}}
    <canvas id="matrix"></canvas>

    <div class="terminal-wrap">
        <div class="terminal">

            {{-- Window Top Bar --}}
            <div class="top-bar">
                <div class="win-dot r"></div>
                <div class="win-dot y"></div>
                <div class="win-dot g"></div>
                <span class="top-bar-title">DOCVERIFY_SYS v2.4.1 <span class="cursor">█</span></span>
            </div>

            {{-- Header --}}
            <div class="terminal-header">
                @if ($school && $school->logo_path)
                    <img src="{{ asset('storage/' . $school->logo_path) }}" class="school-logo" alt="Logo Sekolah">
                @else
                    <svg class="school-logo-fallback" viewBox="0 0 24 24">
                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
                    </svg>
                @endif
                <div class="sys-label">// SISTEM VERIFIKASI DOKUMEN</div>
                <div class="sys-title">DOC_AUTH_PORTAL</div>
                <div class="school-name-tag">{{ $school->name ?? 'SMK PGRI 1 GIRI' }}</div>
            </div>

            {{-- Status Bar --}}
            <div class="status-bar">
                <div class="status-dot"></div>
                <span>SECURE_CONN_ESTABLISHED</span>
                <span class="status-node">NODE: PKL-DB</span>
            </div>

            {{-- Body --}}
            <div class="terminal-body">

                @if (!Session::has('verified_laporan_' . $placement->id))
                    {{-- ===== CAPTCHA SECTION ===== --}}
                    <div class="lock-section">
                        <div class="lock-icon-ring">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#00aa33"
                                stroke-width="2" stroke-linecap="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                        </div>
                        <div class="lock-title">ACCESS RESTRICTED</div>
                        <div class="lock-sub">// human verification required before db query</div>
                    </div>

                    @if (session('error'))
                        <div class="error-terminal">{{ session('error') }}</div>
                    @endif

                    <form method="POST" action="{{ url('/verifikasi/laporan/' . $hash) }}">
                        @csrf
                        <span class="input-label-term">// CAPTCHA_QUERY &gt;&gt; {{ $captcha_question }}</span>
                        <div class="input-cmd-wrap">
                            <span class="cmd-prompt">root@verif:~$</span>
                            <input class="cmd-input" type="number" name="captcha"
                                placeholder="masukkan_jawaban_angka..." required autofocus>
                        </div>
                        <button type="submit" class="btn-execute">
                            [ execute_verification() ]
                        </button>
                    </form>
                @else
                    {{-- ===== VERIFIED SECTION ===== --}}
                    <div class="verified-header">
                        <div class="verified-icon">
                            <svg viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="verified-title">RECORD FOUND :: VALID</div>
                        <div class="verified-sub">// dokumen terdaftar resmi dalam sistem</div>
                    </div>

                    <div class="data-grid">

                        {{-- Data Siswa --}}
                        <div class="data-card">
                            <div class="data-key">Nama Siswa</div>
                            <div class="data-val">{{ $placement->student->name }}</div>
                            <div class="data-muted">NIS: {{ $placement->student->nis }}</div>
                            <div class="data-muted">
                                Kelas: {{ $placement->student->studentClass->name ?? '-' }}
                            </div>
                        </div>

                        {{-- Tempat PKL --}}
                        <div class="data-card">
                            <div class="data-key">Tempat PKL</div>
                            <div class="data-val">{{ $placement->dudika->name }}</div>
                            <div class="data-muted">{{ $placement->dudika->address ?? '-' }}</div>
                        </div>

                        {{-- Guru Pembimbing --}}
                        <div class="data-card">
                            <div class="data-key">Guru Pembimbing</div>
                            <div class="data-val">
                                {{ $placement->teacher->name ?? '-' }},
                                {{ $placement->teacher->title ?? '-' }}
                            </div>
                        </div>

                        {{-- Disahkan Oleh --}}
                        <div class="data-card">
                            <div class="data-key">Disahkan Oleh</div>
                            <div class="data-val">
                                {{ $placement->pengesah_ks_nama ?? 'Belum Disahkan' }}
                            </div>
                        </div>

                        {{-- Timestamp — full width --}}
                        <div class="data-card full timestamp-card">
                            <div class="timestamp-label">TIMESTAMP VERIFIKASI DOKUMEN</div>
                            <div class="timestamp-val">
                                {{ \Carbon\Carbon::parse($placement->end_date)->isoFormat('D MMMM Y') }}
                            </div>
                        </div>

                    </div>
                @endif

            </div>

            {{-- Footer --}}
            <div class="terminal-footer">
                <span>Dicetak dari Sistem PKL Digital</span> &copy; {{ date('Y') }}
            </div>

        </div>
    </div>

    {{-- Matrix Rain Script --}}
    <script>
        const canvas = document.getElementById('matrix');
        const ctx = canvas.getContext('2d');

        function resize() {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        }
        resize();
        window.addEventListener('resize', () => {
            resize();
            cols = Math.floor(canvas.width / fontSize);
            drops = Array(cols).fill(1);
        });

        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789@#$%^&*()アイウエオカキクケコサシスセソタチツテト';
        const fontSize = 14;
        let cols = Math.floor(canvas.width / fontSize);
        let drops = Array(cols).fill(1);

        function drawMatrix() {
            ctx.fillStyle = 'rgba(0, 0, 0, 0.05)';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            ctx.font = fontSize + 'px "Share Tech Mono", monospace';

            for (let i = 0; i < drops.length; i++) {
                const char = chars[Math.floor(Math.random() * chars.length)];
                const bright = Math.random() > 0.92;
                ctx.fillStyle = bright ? '#afffbc' : '#00ff41';
                ctx.fillText(char, i * fontSize, drops[i] * fontSize);
                if (drops[i] * fontSize > canvas.height && Math.random() > 0.975) {
                    drops[i] = 0;
                }
                drops[i]++;
            }
        }

        setInterval(drawMatrix, 40);
    </script>

</body>

</html>
