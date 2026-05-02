<?php

namespace App\Filament\Resources\CetakLaporans\Tables;

use App\Models\Dudika;
use App\Models\Major;
use App\Models\PklAssessment;
use App\Models\SchoolProfile;
use App\Jobs\GenerateLaporanPdfJob;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Placeholder;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;

class CetakLaporansTable
{
    public static function checkCompleteness($record)
    {
        $student = $record->student;
        $dudika = $record->dudika;
        $teacher = $record->teacher;

        $missingStudent = [];
        $missingDudika = [];
        $missingTeacher = [];

        // 1. Cek Siswa
        if (empty($student->nisn)) $missingStudent[] = 'NISN';
        if (empty($student->phone)) $missingStudent[] = 'No. HP Siswa';
        if (empty($student->birth_place)) $missingStudent[] = 'Tempat Lahir';
        if (empty($student->birth_date)) $missingStudent[] = 'Tanggal Lahir';
        if (empty($student->religion)) $missingStudent[] = 'Agama';
        if (empty($student->address)) $missingStudent[] = 'Alamat Lengkap Siswa';
        if (empty($student->father_name)) $missingStudent[] = 'Nama Ayah';
        if (empty($student->mother_name)) $missingStudent[] = 'Nama Ibu';
        if (empty($student->father_job)) $missingStudent[] = 'Pekerjaan Ayah';
        if (empty($student->mother_job)) $missingStudent[] = 'Pekerjaan Ibu';
        if (empty($student->parent_address)) $missingStudent[] = 'Alamat Orang Tua';
        if (empty($student->parent_phone)) $missingStudent[] = 'No. HP Orang Tua / Wali';
        if (empty($record->pkl_field)) $missingStudent[] = 'Bidang Pekerjaan (Input di menu Penempatan)';

        // 2. Cek DUDIKA
        if (empty($dudika->address)) $missingDudika[] = 'Alamat DUDIKA';
        if (empty($dudika->head_name)) $missingDudika[] = 'Nama Pimpinan DUDIKA';
        if (empty($dudika->supervisor_name)) $missingDudika[] = 'Nama Pembimbing DUDIKA';
        if (empty($dudika->supervisor_phone)) $missingDudika[] = 'No. HP Pembimbing DUDIKA';
        if (!PklAssessment::where('pkl_placement_id', $record->id)->exists()) {
            $missingDudika[] = 'Nilai PKL belum diinput oleh pihak DUDIKA';
        }

        // 3. Cek Guru
        if (empty($teacher->phone)) $missingTeacher[] = 'No. HP Guru Pembimbing';
        if (empty($teacher->signature_path)) $missingTeacher[] = 'Tanda Tangan Guru Pembimbing';

        return [
            'is_complete' => empty($missingStudent) && empty($missingDudika) && empty($missingTeacher),
            'student' => $missingStudent,
            'dudika' => $missingDudika,
            'teacher' => $missingTeacher,
        ];
    }

    // FUNGSI UTAMA (Dipecah agar tidak panjang)
    public static function configure(Table $table): Table
    {
        return $table
            ->poll('5s') // Auto-refresh 5 detik untuk ngecek kalau PDF sudah jadi
            ->modifyQueryUsing(function (Builder $query) {
                return $query->whereHas('academicYear', function ($ay) {
                    $ay->where('is_active', true);
                })->where('status', 'Aktif');
            })
            ->columns(self::getColumns())
            ->filters(self::getFilters())
            ->recordActions(self::getRecordActions())
            ->toolbarActions(self::getBulkActions());
    }
    private static function getColumns(): array
    {
        return [
            TextColumn::make('student.nis')->label('NIS')->searchable()->sortable(),
            TextColumn::make('student.name')->label('Nama Siswa')->searchable()->weight('bold')->sortable(),
            TextColumn::make('student.studentClass.name')->label('Kelas')->searchable()->weight('bold')->sortable()->badge()->color('gray'),
            TextColumn::make('dudika.name')->label('Tempat PKL')->wrap()->searchable(),
            TextColumn::make('status_kelengkapan')
                ->label('Status Laporan')
                ->badge()
                ->state(function ($record) {
                    $check = self::checkCompleteness($record);
                    if (!$check['is_complete']) return 'Belum Lengkap';
                    if (empty($record->pengesah_ks_nama)) return 'Menunggu Validasi';
                    return 'Tervalidasi';
                })
                ->color(fn(string $state): string => match ($state) {
                    'Belum Lengkap' => 'danger',
                    'Menunggu Validasi' => 'warning',
                    'Tervalidasi' => 'success',
                })
                // ==========================================================
                // MANTRA SAKTI: TRIK NINJA AUDIO AUTOPLAY (NOTIFIKASI SUARA)
                // ==========================================================
                ->description(function ($record) {
                    // Cek jika file baru saja selesai dibuat dalam 6 detik terakhir
                    if ($record->file_laporan_path && $record->file_laporan_path !== 'processing' && $record->updated_at->diffInSeconds(now()) <= 6) {
                        // Gunakan AlpineJS (bawaan Filament) untuk memanggil Audio JS Object
                        return new HtmlString(
                            "<span x-data x-init=\"new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3').play().catch(e => console.log('Autoplay diblokir browser'))\"></span>"
                        );
                    }
                    return null;
                }),
        ];
    }

    private static function getFilters(): array
    {
        return [
            SelectFilter::make('major_id')
                ->label('Filter Jurusan')
                ->options(Major::pluck('name', 'id'))
                ->query(function (Builder $query, array $data) {
                    if (!empty($data['value'])) {
                        $query->whereHas('student.studentClass', fn($q) => $q->where('major_id', $data['value']));
                    }
                }),
            SelectFilter::make('dudika_id')->label('Filter DUDIKA')->options(Dudika::pluck('name', 'id'))->searchable(),
            SelectFilter::make('status_laporan')
                ->label('Filter Status Laporan')
                ->options([
                    'belum_lengkap' => '🔴 Belum Lengkap',
                    'menunggu_validasi' => '🟡 Menunggu Validasi',
                    'tervalidasi' => '🟢 Tervalidasi',
                ])
                ->query(function (Builder $query, array $data) {
                    if (empty($data['value'])) return;

                    $isCompleteCondition = function ($q) {
                        $q->whereNotNull('pkl_field')->where('pkl_field', '!=', '')
                            ->whereHas('student', function ($sq) {
                                $sq->whereNotNull('nisn')->whereNotNull('phone')->whereNotNull('birth_place')
                                    ->whereNotNull('birth_date')->whereNotNull('religion')->whereNotNull('address')
                                    ->whereNotNull('father_name')->whereNotNull('mother_name')->whereNotNull('father_job')
                                    ->whereNotNull('mother_job')->whereNotNull('parent_address')->whereNotNull('parent_phone');
                            })
                            ->whereHas('dudika', function ($dq) {
                                $dq->whereNotNull('address')->whereNotNull('head_name')
                                    ->whereNotNull('supervisor_name')->whereNotNull('supervisor_phone');
                            })
                            ->whereHas('teacher', function ($tq) {
                                $tq->whereNotNull('phone')->whereNotNull('signature_path');
                            })
                            ->whereIn('id', fn($subQ) => $subQ->select('pkl_placement_id')->from('pkl_assessments'));
                    };

                    if ($data['value'] === 'belum_lengkap') {
                        $query->whereNot($isCompleteCondition);
                    } elseif ($data['value'] === 'menunggu_validasi') {
                        $query->where($isCompleteCondition)->whereNull('pengesah_ks_nama');
                    } elseif ($data['value'] === 'tervalidasi') {
                        $query->where($isCompleteCondition)->whereNotNull('pengesah_ks_nama');
                    }
                }),
        ];
    }
    private static function getRecordActions(): array
    {
        return [
            ActionGroup::make([
                // 1. TOMBOL LOADING (MUNCUL SAAT PROSES GENERATE)
                Action::make('loading_generate')
                    ->label('Sedang Diproses...')
                    ->icon('heroicon-m-arrow-path') // Ikon muter
                    ->color('gray')
                    ->disabled() // Tidak bisa diklik
                    // Hanya muncul jika di database path-nya tertulis 'processing'
                    ->visible(fn($record) => $record->file_laporan_path === 'processing'),

                // 1. LIHAT LAPORAN (Muncul kalau tervalidasi & file sudah digenerate)
                Action::make('download_laporan')
                    ->label('Lihat Laporan (PDF)')
                    ->icon('heroicon-o-document-check')
                    ->color('success')
                    ->visible(fn($record) => !empty($record->pengesah_ks_nama) && $record->file_laporan_path !== null)
                    ->url(fn($record) => Storage::url($record->file_laporan_path))
                    ->openUrlInNewTab(),


                Action::make('generate_laporan')
                    ->label(fn($record) => ($record->file_laporan_path !== null && $record->file_laporan_path !== 'processing') ? 'Generate Ulang' : 'Generate Laporan')
                    ->icon('heroicon-o-cog')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn($record) => !empty($record->pengesah_ks_nama) && $record->file_laporan_path !== 'processing')
                    ->modalHeading('Generate Laporan Lengkap?')
                    ->modalDescription('Proses ini dikerjakan di latar belakang dan memakan waktu beberapa saat.')
                    ->action(function ($record) {

                        // ==============================================================
                        // MANTRA SAKTI: HAPUS FILE PDF LAMA DARI STORAGE (BIAR GAK PENUH)
                        // ==============================================================
                        if ($record->file_laporan_path && $record->file_laporan_path !== 'processing') {
                            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($record->file_laporan_path)) {
                                \Illuminate\Support\Facades\Storage::disk('public')->delete($record->file_laporan_path);
                            }
                        }

                        // Ubah jadi processing
                        $record->update(['file_laporan_path' => 'processing']);

                        // Lempar ke Job
                        GenerateLaporanPdfJob::dispatch($record->id, auth()->id());

                        \Filament\Notifications\Notification::make()
                            ->title('Mulai Diproses!')
                            ->body('Sistem sedang merakit PDF. Silakan tunggu...')
                            ->success()
                            ->send();
                    }),

                // 3. CEK KEKURANGAN (Tersembunyi kalau sudah lengkap)
                Action::make('cek_kekurangan')
                    ->label('Cek Kekurangan')
                    ->icon('heroicon-o-information-circle')
                    ->color('danger')
                    ->visible(fn($record) => !self::checkCompleteness($record)['is_complete'])
                    ->modalHeading(fn($record) => 'Detail Kekurangan: ' . $record->student->name)
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->form(function ($record) {
                        return [Placeholder::make('info')->hiddenLabel()->content(new HtmlString(self::buildHtmlKekurangan($record)))];
                    }),

                // 4. VALIDASI
                Action::make('validasi_laporan')
                    ->label('Validasi Laporan')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn($record) => self::checkCompleteness($record)['is_complete'] && empty($record->pengesah_ks_nama))
                    ->action(function ($record) {
                        $school = SchoolProfile::first();
                        if (!$school || empty($school->headmaster_name)) return Notification::make()->title('Gagal! Nama Kepsek kosong.')->danger()->send();
                        $record->update(['pengesah_ks_nama' => $school->headmaster_name, 'pengesah_ks_nip' => $school->headmaster_nip ?? '-']);
                        Notification::make()->title('Laporan berhasil divalidasi!')->success()->send();
                    }),

                // 5. BATAL VALIDASI
                Action::make('batal_validasi')
                    ->label('Batal Validasi')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn($record) => !empty($record->pengesah_ks_nama))
                    ->action(function ($record) {
                        $record->update(['pengesah_ks_nama' => null, 'pengesah_ks_nip' => null]);
                        Notification::make()->title('Validasi dibatalkan.')->success()->send();
                    }),

            ])->label('Aksi Laporan')->button()->outlined(),
        ];
    }

    private static function getBulkActions(): array
    {
        return [
            BulkActionGroup::make([
                BulkAction::make('bulk_validasi')
                    ->label('Validasi Terpilih')
                    ->icon('heroicon-o-check-badge')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function (Collection $records) {
                        $school = SchoolProfile::first();
                        if (!$school || empty($school->headmaster_name)) return Notification::make()->title('Nama Kepsek kosong.')->danger()->send();

                        $validated = 0;
                        foreach ($records as $record) {
                            $check = self::checkCompleteness($record);
                            if ($check['is_complete'] && empty($record->pengesah_ks_nama)) {
                                $record->update(['pengesah_ks_nama' => $school->headmaster_name, 'pengesah_ks_nip' => $school->headmaster_nip ?? '-']);
                                $validated++;
                            }
                        }
                        if ($validated > 0) {
                            Notification::make()->title("Berhasil memvalidasi {$validated} laporan!")->success()->send();
                        } else {
                            Notification::make()->title('Tidak ada yang divalidasi.')->warning()->send();
                        }
                    }),
                BulkAction::make('bulk_batal_validasi')
                    ->label('Batal Validasi Terpilih')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (Collection $records) {
                        foreach ($records as $record) {
                            if (!empty($record->pengesah_ks_nama)) $record->update(['pengesah_ks_nama' => null, 'pengesah_ks_nip' => null]);
                        }
                        Notification::make()->title('Validasi massal dibatalkan.')->success()->send();
                    }),
            ]),
        ];
    }

    // =========================================================
    // HELPER: Format nomor WA (0812... → 62812...)
    // =========================================================
    private static function formatWaNumber(?string $phone): ?string
    {
        if (empty($phone)) return null;
        $phone = preg_replace('/\D/', '', $phone);
        if (str_starts_with($phone, '0'))        $phone = '62' . substr($phone, 1);
        elseif (!str_starts_with($phone, '62'))  $phone = '62' . $phone;
        return $phone;
    }

    // =========================================================
    // HELPER: CSS modal — dark/light aware via .dark class
    // =========================================================
    private static function getModalCss(): string
    {
        return '
        <style>
        .kek-wrap{display:flex;flex-direction:column;gap:12px}

        /* Card wrapper */
        .kek-card{border-radius:10px;overflow:hidden;border-width:1.5px;border-style:solid;background:#fff;box-shadow:0 1px 3px rgba(0,0,0,.07)}
        .dark .kek-card{background:#1e293b;box-shadow:0 1px 4px rgba(0,0,0,.5)}

        /* Card header */
        .kek-head{padding:12px 16px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;cursor:pointer;-webkit-user-select:none;user-select:none;transition:filter .15s}
        .kek-head:hover{filter:brightness(.97)}
        .dark .kek-head:hover{filter:brightness(1.12)}
        .kek-head-left{display:flex;align-items:center;gap:10px;flex:1;min-width:0}
        .kek-head-right{display:flex;align-items:center;gap:8px;flex-shrink:0}

        /* Badge bulat */
        .kek-badge{border-radius:50%;width:28px;height:28px;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;flex-shrink:0;color:#fff}

        /* Title & subtitle */
        .kek-title{font-weight:700;font-size:14px;color:#111827}
        .dark .kek-title{color:#f1f5f9}
        .kek-sub{font-size:12px;color:#6b7280;margin-top:1px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:260px}
        .dark .kek-sub{color:#94a3b8}

        /* Chevron collapse */
        .kek-chevron{color:#9ca3af;transition:transform .25s ease;flex-shrink:0}
        .dark .kek-chevron{color:#64748b}

        /* Card body */
        .kek-body{padding:6px 16px 12px;border-top:1px solid rgba(0,0,0,.06)}
        .dark .kek-body{border-top-color:rgba(255,255,255,.06)}

        /* List item */
        .kek-item{display:flex;align-items:flex-start;gap:8px;padding:7px 0;border-bottom:1px solid rgba(0,0,0,.05)}
        .dark .kek-item{border-bottom-color:rgba(255,255,255,.05)}
        .kek-item:last-child{border-bottom:none}
        .kek-item-text{font-size:13px;color:#374151;line-height:1.5}
        .dark .kek-item-text{color:#cbd5e1}

        /* Note / tip box */
        .kek-note{font-size:11.5px;padding:8px 10px;border-radius:6px;margin-top:8px;display:flex;align-items:flex-start;gap:6px;line-height:1.5;background:#fef3c7;border:1px solid #fcd34d;color:#92400e}
        .dark .kek-note{background:rgba(245,158,11,.1);border-color:rgba(245,158,11,.3);color:#fbbf24}

        /* Footer */
        .kek-footer{display:flex;align-items:center;gap:8px;padding:10px 14px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px}
        .dark .kek-footer{background:#0f172a;border-color:#1e293b}
        .kek-footer-text{font-size:12px;color:#64748b}
        .dark .kek-footer-text{color:#94a3b8}

        /* Tombol WA */
        .kek-wa-btn{display:inline-flex;align-items:center;gap:6px;padding:7px 13px;background:#22c55e;border-radius:6px;font-size:12px;font-weight:600;color:#fff!important;text-decoration:none;white-space:nowrap;transition:background .15s}
        .kek-wa-btn:hover{background:#16a34a}
        .kek-wa-none{display:inline-flex;align-items:center;gap:5px;padding:5px 10px;background:#f1f5f9;border-radius:6px;font-size:11px;color:#94a3b8;white-space:nowrap}
        .dark .kek-wa-none{background:#334155;color:#64748b}

        /* Color themes: red, orange, blue */
        .kek-card-red   { border-color:#fca5a5 }
        .kek-head-red   { background:#fff1f2 }
        .dark .kek-head-red   { background:rgba(239,68,68,.09) }
        .kek-badge-red  { background:#ef4444 }
        .kek-icon-red   { color:#ef4444 }

        .kek-card-orange  { border-color:#fdba74 }
        .kek-head-orange  { background:#fff7ed }
        .dark .kek-head-orange  { background:rgba(249,115,22,.09) }
        .kek-badge-orange { background:#f97316 }
        .kek-icon-orange  { color:#f97316 }

        .kek-card-blue  { border-color:#93c5fd }
        .kek-head-blue  { background:#eff6ff }
        .dark .kek-head-blue  { background:rgba(59,130,246,.09) }
        .kek-badge-blue { background:#3b82f6 }
        .kek-icon-blue  { color:#3b82f6 }
        </style>';
    }

    // =========================================================
    // HELPER: WhatsApp icon SVG
    // =========================================================
    private static function waIcon(string $fill = 'currentColor', int $size = 14): string
    {
        return "<svg xmlns='http://www.w3.org/2000/svg' width='{$size}' height='{$size}' fill='{$fill}' viewBox='0 0 24 24'>
            <path d='M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z'/>
            <path d='M12 0C5.373 0 0 5.373 0 12c0 2.114.553 4.1 1.523 5.824L.057 23.885a.5.5 0 0 0 .615.612l6.218-1.635A11.945 11.945 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.885 0-3.655-.502-5.19-1.381l-.373-.214-3.865 1.017 1.046-3.756-.234-.386A9.935 9.935 0 0 1 2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z'/>
        </svg>";
    }

    // =========================================================
    // HELPER: Build tombol WA
    // =========================================================
    private static function buildWaButton(
        ?string $phone,
        string  $name,
        string  $role,
        array   $missingItems,
        bool    $useSalutation = false  // true = "Bapak/Ibu [nama]", false = langsung nama
    ): string {
        $waNumber = self::formatWaNumber($phone);

        if (!$waNumber) {
            return "<span class='kek-wa-none'>" . self::waIcon() . " No. HP tidak tersedia</span>";
        }

        $greeting    = $useSalutation ? "Bapak/Ibu {$name}" : $name;
        $missingText = implode("\n- ", $missingItems);
        $message     = urlencode(
            "Halo {$greeting},\n\n" .
                "Kami dari pihak sekolah ingin menginformasikan bahwa data PKL Anda sebagai {$role} " .
                "masih memiliki kekurangan berikut:\n\n- {$missingText}\n\n" .
                "Mohon segera dilengkapi. Terima kasih 🙏"
        );

        return "<a href='https://wa.me/{$waNumber}?text={$message}' target='_blank' class='kek-wa-btn'>"
            . self::waIcon('#fff')
            . " Hubungi via WhatsApp</a>";
    }

    // =========================================================
    // HELPER: Build card kekurangan (collapsible, dark-aware)
    // =========================================================
    private static function buildKekuranganCard(
        string  $title,
        string  $subtitle,
        array   $items,
        string  $theme,         // 'red' | 'orange' | 'blue'
        string  $waButton,
        bool    $defaultOpen = true,
        ?string $noteHtml    = null
    ): string {
        $count     = count($items);
        $openState = $defaultOpen ? 'true' : 'false';

        $listItems = '';
        foreach ($items as $item) {
            $listItems .= "
            <div class='kek-item'>
                <svg class='kek-icon-{$theme}' xmlns='http://www.w3.org/2000/svg' width='15' height='15'
                     fill='currentColor' style='flex-shrink:0;margin-top:2px' viewBox='0 0 20 20'>
                    <path fill-rule='evenodd' d='M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0
                    11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z' clip-rule='evenodd'/>
                </svg>
                <span class='kek-item-text'>{$item}</span>
            </div>";
        }

        $noteBlock = $noteHtml
            ? "<div class='kek-note'>{$noteHtml}</div>"
            : '';

        // Chevron SVG — Alpine mengatur rotasi via :style binding
        $chevron = "<svg class='kek-chevron' xmlns='http://www.w3.org/2000/svg' width='18' height='18'
                         fill='none' viewBox='0 0 24 24' stroke='currentColor' stroke-width='2'
                         :style=\"{ transform: open ? 'rotate(180deg)' : 'rotate(0deg)' }\">
                        <path stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/>
                    </svg>";

        return "
        <div class='kek-card kek-card-{$theme}' x-data=\"{ open: {$openState} }\">
            <div class='kek-head kek-head-{$theme}' @click=\"open = !open\">
                <div class='kek-head-left'>
                    <div class='kek-badge kek-badge-{$theme}'>{$count}</div>
                    <div style='min-width:0'>
                        <div class='kek-title'>{$title}</div>
                        <div class='kek-sub'>{$subtitle}</div>
                    </div>
                </div>
                <div class='kek-head-right'>
                    {$waButton}
                    {$chevron}
                </div>
            </div>
            <div class='kek-body' x-show='open' x-transition>
                {$listItems}
                {$noteBlock}
            </div>
        </div>";
    }

    // =========================================================
    // HELPER UTAMA: Build seluruh HTML modal kekurangan
    // =========================================================
    private static function buildHtmlKekurangan($record): string
    {
        $check   = self::checkCompleteness($record);
        $student = $record->student;
        $dudika  = $record->dudika;
        $teacher = $record->teacher;

        $html = self::getModalCss() . "<div class='kek-wrap'>";

        // ── SISWA ──────────────────────────────────────────────
        if (!empty($check['student'])) {
            $waButton = self::buildWaButton(
                phone: $student->phone,
                name: $student->name,
                role: 'Siswa PKL',
                missingItems: $check['student'],
                useSalutation: false   // Siswa → langsung nama
            );

            // Petunjuk menu untuk siswa
            $isBidangPekerjaan = fn($item) => str_contains($item, 'Bidang Pekerjaan');
            $hasBiodata = collect($check['student'])->contains(fn($i) => !$isBidangPekerjaan($i));
            $hasBidang  = collect($check['student'])->contains(fn($i) => $isBidangPekerjaan($i));

            $warnIcon = "<svg xmlns='http://www.w3.org/2000/svg' width='14' height='14' fill='currentColor'
                              style='flex-shrink:0;margin-top:2px' viewBox='0 0 20 20'>
                            <path fill-rule='evenodd' d='M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58
                            9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11
                            13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z'
                            clip-rule='evenodd'/>
                         </svg>";

            $tips = [];
            if ($hasBiodata) $tips[] = '📋 Biodata siswa dapat dilengkapi di <strong>menu Biodata Siswa</strong>';
            if ($hasBidang)  $tips[] = '📝 Bidang Pekerjaan dapat diisi di <strong>menu Penempatan PKL</strong>';

            $noteHtml = !empty($tips)
                ? $warnIcon . '<div>' . implode('<br>', $tips) . '</div>'
                : null;

            $html .= self::buildKekuranganCard(
                title: 'Data Siswa',
                subtitle: $student->name . ' — NIS: ' . ($student->nis ?? '-'),
                items: $check['student'],
                theme: 'red',
                waButton: $waButton,
                defaultOpen: true,
                noteHtml: $noteHtml,
            );
        }

        // ── DUDIKA ─────────────────────────────────────────────
        if (!empty($check['dudika'])) {
            $supervisorName = $dudika->supervisor_name ?? $dudika->name;
            $waButton = self::buildWaButton(
                phone: $dudika->supervisor_phone,
                name: $supervisorName,
                role: 'Pembimbing DUDIKA',
                missingItems: $check['dudika'],
                useSalutation: true    // DUDIKA → "Bapak/Ibu [nama]"
            );
            $html .= self::buildKekuranganCard(
                title: 'Data DUDIKA',
                subtitle: $dudika->name . ($dudika->supervisor_name ? ' — Pembimbing: ' . $dudika->supervisor_name : ''),
                items: $check['dudika'],
                theme: 'orange',
                waButton: $waButton,
                defaultOpen: true,
            );
        }

        // ── GURU PEMBIMBING ────────────────────────────────────
        if (!empty($check['teacher'])) {
            $waButton = self::buildWaButton(
                phone: $teacher->phone,
                name: $teacher->name,
                role: 'Guru Pembimbing',
                missingItems: $check['teacher'],
                useSalutation: true    // Guru → "Bapak/Ibu [nama]"
            );
            $html .= self::buildKekuranganCard(
                title: 'Data Guru Pembimbing',
                subtitle: $teacher->name,
                items: $check['teacher'],
                theme: 'blue',
                waButton: $waButton,
                defaultOpen: true,
            );
        }

        // ── FOOTER ─────────────────────────────────────────────
        $total = count($check['student']) + count($check['dudika']) + count($check['teacher']);
        $html .= "
        <div class='kek-footer'>
            <svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' fill='#64748b' viewBox='0 0 20 20'>
                <path fill-rule='evenodd' d='M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2
                0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z' clip-rule='evenodd'/>
            </svg>
            <span class='kek-footer-text'>Total <strong>{$total} item</strong> perlu dilengkapi sebelum laporan bisa divalidasi.</span>
        </div>";

        $html .= "</div>";
        return $html;
    }
}
