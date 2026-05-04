<x-filament-panels::page>
    <style>
        .db-container {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .db-info-card {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding: 1.5rem;
            border-radius: 0.75rem;
            border-left: 4px solid;
        }

        .db-icon-wrap {
            padding: 0.75rem;
            border-radius: 0.5rem;
            flex-shrink: 0;
        }

        .db-icon-wrap svg {
            width: 2rem;
            height: 2rem;
        }

        .db-heading {
            font-size: 1.15rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .db-desc {
            font-size: 0.9rem;
            line-height: 1.7;
        }

        .db-actions-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 1rem;
        }

        @media (max-width: 768px) {
            .db-actions-grid {
                grid-template-columns: 1fr;
            }
        }

        .db-action-card {
            padding: 1.25rem;
            border-radius: 0.75rem;
            border: 1px solid;
        }

        .db-action-title {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 700;
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }

        .db-action-title svg {
            width: 1.25rem;
            height: 1.25rem;
            flex-shrink: 0;
        }

        .db-action-desc {
            font-size: 0.85rem;
            line-height: 1.6;
        }

        .db-badge {
            display: inline-block;
            font-size: 0.7rem;
            font-weight: 700;
            padding: 0.15rem 0.5rem;
            border-radius: 999px;
            margin-left: 0.35rem;
            vertical-align: middle;
        }

        .db-warning-box {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            padding: 1rem 1.25rem;
            border-radius: 0.5rem;
            border: 1px solid;
            margin-top: 0.25rem;
        }

        .db-warning-box svg {
            width: 1.25rem;
            height: 1.25rem;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .db-warning-text {
            font-size: 0.82rem;
            line-height: 1.6;
        }

        .db-warning-text b {
            font-weight: 700;
        }

        /* STEP INDICATOR */
        .db-steps {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            margin-top: 0.85rem;
            flex-wrap: wrap;
        }

        .db-step {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            font-size: 0.78rem;
            font-weight: 600;
        }

        .db-step-num {
            width: 1.25rem;
            height: 1.25rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: 700;
            flex-shrink: 0;
        }

        .db-step-arrow {
            font-size: 0.75rem;
            opacity: 0.5;
        }

        /* ===== DARK MODE ===== */
        .dark .db-info-card {
            background-color: rgba(59, 130, 246, 0.08);
            border-left-color: #3b82f6;
        }

        .dark .db-icon-wrap {
            background-color: rgba(59, 130, 246, 0.15);
        }

        .dark .db-icon-wrap svg {
            color: #60a5fa;
        }

        .dark .db-heading {
            color: #e2e8f0;
        }

        .dark .db-desc {
            color: #9ca3af;
        }

        .dark .db-action-card.backup {
            background-color: rgba(34, 197, 94, 0.07);
            border-color: rgba(34, 197, 94, 0.3);
        }

        .dark .db-action-title.backup {
            color: #4ade80;
        }

        .dark .db-action-desc.backup {
            color: #9ca3af;
        }

        .dark .db-action-card.restore {
            background-color: rgba(245, 158, 11, 0.07);
            border-color: rgba(245, 158, 11, 0.3);
        }

        .dark .db-action-title.restore {
            color: #fbbf24;
        }

        .dark .db-action-desc.restore {
            color: #9ca3af;
        }

        .dark .db-badge.restore {
            background-color: rgba(245, 158, 11, 0.2);
            color: #fbbf24;
        }

        .dark .db-step-num.restore {
            background-color: rgba(245, 158, 11, 0.2);
            color: #fbbf24;
        }

        .dark .db-step.restore {
            color: #d1d5db;
        }

        .dark .db-action-card.reset {
            background-color: rgba(239, 68, 68, 0.07);
            border-color: rgba(239, 68, 68, 0.3);
        }

        .dark .db-action-title.reset {
            color: #f87171;
        }

        .dark .db-action-desc.reset {
            color: #9ca3af;
        }

        .dark .db-warning-box {
            background-color: rgba(245, 158, 11, 0.08);
            border-color: rgba(245, 158, 11, 0.3);
        }

        .dark .db-warning-box svg {
            color: #fbbf24;
        }

        .dark .db-warning-text {
            color: #d1d5db;
        }

        /* ===== LIGHT MODE ===== */
        html:not(.dark) .db-info-card {
            background-color: #eff6ff;
            border-left-color: #3b82f6;
        }

        html:not(.dark) .db-icon-wrap {
            background-color: #dbeafe;
        }

        html:not(.dark) .db-icon-wrap svg {
            color: #2563eb;
        }

        html:not(.dark) .db-heading {
            color: #1e293b;
        }

        html:not(.dark) .db-desc {
            color: #475569;
        }

        html:not(.dark) .db-action-card.backup {
            background-color: #f0fdf4;
            border-color: #86efac;
        }

        html:not(.dark) .db-action-title.backup {
            color: #166534;
        }

        html:not(.dark) .db-action-desc.backup {
            color: #4b5563;
        }

        html:not(.dark) .db-action-card.restore {
            background-color: #fffbeb;
            border-color: #fcd34d;
        }

        html:not(.dark) .db-action-title.restore {
            color: #92400e;
        }

        html:not(.dark) .db-action-desc.restore {
            color: #4b5563;
        }

        html:not(.dark) .db-badge.restore {
            background-color: #fef3c7;
            color: #92400e;
        }

        html:not(.dark) .db-step-num.restore {
            background-color: #fef3c7;
            color: #92400e;
        }

        html:not(.dark) .db-step.restore {
            color: #374151;
        }

        html:not(.dark) .db-action-card.reset {
            background-color: #fef2f2;
            border-color: #fca5a5;
        }

        html:not(.dark) .db-action-title.reset {
            color: #b91c1c;
        }

        html:not(.dark) .db-action-desc.reset {
            color: #4b5563;
        }

        html:not(.dark) .db-warning-box {
            background-color: #fffbeb;
            border-color: #fcd34d;
        }

        html:not(.dark) .db-warning-box svg {
            color: #d97706;
        }

        html:not(.dark) .db-warning-text {
            color: #374151;
        }
    </style>

    <x-filament::card>
        <div class="db-container">

            {{-- INFO HEADER --}}
            <div class="db-info-card">
                <div class="db-icon-wrap">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                    </svg>
                </div>
                <div>
                    <div class="db-heading">Pusat Kendali Database — God Mode</div>
                    <div class="db-desc">
                        Halaman ini memiliki akses langsung ke inti database aplikasi <b>Grisa PKL</b>.
                        Terdapat 3 operasi utama: <b>Backup</b> untuk mengamankan data,
                        <b>Restore</b> untuk memulihkan dari backup, dan <b>Factory Reset</b>
                        untuk membersihkan data di awal tahun ajaran baru.
                    </div>
                </div>
            </div>

            {{-- GRID 3 FITUR --}}
            <div class="db-actions-grid">

                {{-- CARD BACKUP --}}
                <div class="db-action-card backup">
                    <div class="db-action-title backup">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Download Backup
                    </div>
                    <div class="db-action-desc backup">
                        Mengunduh seluruh isi database ke file <code>.sql</code>.
                        Lakukan ini <b>sebelum setiap update besar</b> atau akhir semester.
                        File otomatis terhapus dari server setelah diunduh.
                    </div>
                </div>

                {{-- CARD RESTORE --}}
                <div class="db-action-card restore">
                    <div class="db-action-title restore">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l4-4m0 0l4 4m-4-4v12" />
                        </svg>
                        Restore Backup
                        <span class="db-badge restore">Baru</span>
                    </div>
                    <div class="db-action-desc restore">
                        Memulihkan database dari file <code>.sql</code> hasil backup sebelumnya.
                        Data saat ini akan <b>ditimpa sepenuhnya</b> oleh isi file yang diupload.
                    </div>
                    <div class="db-steps">
                        <div class="db-step restore">
                            <div class="db-step-num restore">1</div>
                            <span>Klik tombol</span>
                        </div>
                        <span class="db-step-arrow">→</span>
                        <div class="db-step restore">
                            <div class="db-step-num restore">2</div>
                            <span>Upload .sql</span>
                        </div>
                        <span class="db-step-arrow">→</span>
                        <div class="db-step restore">
                            <div class="db-step-num restore">3</div>
                            <span>Konfirmasi</span>
                        </div>
                    </div>
                </div>

                {{-- CARD RESET --}}
                <div class="db-action-card reset">
                    <div class="db-action-title reset">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Factory Reset
                    </div>
                    <div class="db-action-desc reset">
                        Menghapus <b>seluruh data</b> Siswa, DUDIKA, Penempatan, Kunjungan, dan Pengumuman.
                        Hanya <b>Super Admin</b>, Profil Sekolah, dan Role yang dipertahankan.
                    </div>
                </div>

            </div>

            {{-- WARNING BOX --}}
            <div class="db-warning-box">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z"
                        clip-rule="evenodd" />
                </svg>
                <div class="db-warning-text">
                    <b>URUTAN AMAN YANG DIREKOMENDASIKAN:</b>
                    Selalu <b>Backup</b> terlebih dahulu → kemudian boleh <b>Restore</b> atau <b>Factory Reset</b>.
                    Operasi Restore dan Reset bersifat <b>PERMANEN dan tidak dapat dibatalkan</b>.
                    Gunakan tombol-tombol di header <b>(pojok kanan atas)</b> untuk mengeksekusi aksi.
                </div>
            </div>

        </div>
    </x-filament::card>
</x-filament-panels::page>
