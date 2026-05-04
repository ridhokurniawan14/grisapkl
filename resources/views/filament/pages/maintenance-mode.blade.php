<x-filament-panels::page>
    <style>
        /* CSS Murni Anti-Badai JIT Compiler */
        .maint-container {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .maint-box {
            padding: 1.5rem;
            border-radius: 0.75rem;
            border-left-width: 4px;
            border-left-style: solid;
        }

        .maint-title {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: bold;
            font-size: 1.15rem;
            margin-bottom: 0.75rem;
        }

        .maint-desc {
            font-size: 0.95rem;
            line-height: 1.6;
            opacity: 0.9;
            margin-bottom: 1.25rem;
        }

        .maint-url-box {
            padding: 1.25rem;
            border-radius: 0.5rem;
            border-width: 1px;
            border-style: solid;
            margin-top: 1rem;
        }

        .maint-url-title {
            font-weight: 600;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .maint-url-code {
            display: block;
            font-family: monospace;
            font-size: 1.15rem;
            font-weight: bold;
            padding: 0.75rem 1rem;
            border-radius: 0.375rem;
            user-select: all;
        }

        .maint-url-note {
            font-size: 0.85rem;
            margin-top: 0.75rem;
            opacity: 0.8;
        }

        /* TEMA GELAP (Dark Mode) */
        .dark .maint-box.offline {
            background-color: rgba(239, 68, 68, 0.1);
            border-left-color: #ef4444;
        }

        .dark .maint-title.offline {
            color: #f87171;
        }

        .dark .maint-desc {
            color: #d1d5db;
        }

        .dark .maint-url-box {
            background-color: rgba(0, 0, 0, 0.2);
            border-color: rgba(239, 68, 68, 0.3);
        }

        .dark .maint-url-title {
            color: #fca5a5;
        }

        .dark .maint-url-code {
            background-color: #111827;
            color: #f9fafb;
            border: 1px solid rgba(239, 68, 68, 0.5);
        }

        .dark .maint-box.online {
            background-color: rgba(34, 197, 94, 0.1);
            border-left-color: #22c55e;
        }

        .dark .maint-title.online {
            color: #4ade80;
        }

        /* TEMA TERANG (Light Mode) */
        html:not(.dark) .maint-box.offline {
            background-color: #fef2f2;
            border-left-color: #ef4444;
        }

        html:not(.dark) .maint-title.offline {
            color: #b91c1c;
        }

        html:not(.dark) .maint-desc {
            color: #374151;
        }

        html:not(.dark) .maint-url-box {
            background-color: #fee2e2;
            border-color: #fca5a5;
        }

        html:not(.dark) .maint-url-title {
            color: #991b1b;
        }

        html:not(.dark) .maint-url-code {
            background-color: #ffffff;
            color: #111827;
            border: 1px solid #f87171;
        }

        html:not(.dark) .maint-box.online {
            background-color: #f0fdf4;
            border-left-color: #22c55e;
        }

        html:not(.dark) .maint-title.online {
            color: #166534;
        }
    </style>

    <x-filament::card>
        <div class="maint-container">
            <h2 style="font-size: 1.25rem; font-weight: 700;">Status Sistem Saat Ini:</h2>

            @if ($isDown)
                <div class="maint-box offline">
                    <div class="maint-title offline">
                        <svg style="width: 26px; height: 26px; flex-shrink: 0;" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z"
                                clip-rule="evenodd" />
                        </svg>
                        Offline (Maintenance Mode)
                    </div>

                    <div class="maint-desc">
                        Aplikasi sedang dikunci untuk publik. Anda tetap dapat mengakses halaman ini karena sedang
                        melewati jalur rahasia.
                    </div>

                    @if ($secret)
                        <div class="maint-url-box">
                            <div class="maint-url-title">🔥 URL Akses Rahasia Saat Ini:</div>
                            <code class="maint-url-code">{{ url('/' . $secret) }}</code>
                            <div class="maint-url-note">
                                *Mohon simpan/copy URL di atas! Jika Anda tanpa sengaja ter-logout, gunakan URL tersebut
                                di browser untuk menjebol halaman Maintenance dan masuk kembali.
                            </div>
                        </div>
                    @endif
                </div>
            @else
                <div class="maint-box online">
                    <div class="maint-title online">
                        <svg style="width: 26px; height: 26px; flex-shrink: 0;" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"
                                clip-rule="evenodd" />
                        </svg>
                        Online (Berjalan Normal)
                    </div>
                    <div class="maint-desc">
                        Aplikasi dapat diakses secara publik dan normal oleh seluruh entitas (Siswa, Guru Pembimbing,
                        DUDIKA, dll).
                    </div>
                </div>
            @endif
        </div>
    </x-filament::card>
</x-filament-panels::page>
