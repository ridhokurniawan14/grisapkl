<?php

namespace App\Filament\Resources\CetakLaporans\Tables;

use App\Models\Dudika;
use App\Models\Major;
use App\Models\PklAssessment;
use App\Models\SchoolProfile;
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

class CetakLaporansTable
{
    protected static function checkCompleteness($record)
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

    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                return $query->whereHas('academicYear', function ($ay) {
                    $ay->where('is_active', true);
                })->where('status', 'Aktif');
            })
            ->columns([
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
                    }),
            ])
            ->filters([
                SelectFilter::make('major_id')
                    ->label('Filter Jurusan')
                    ->options(Major::pluck('name', 'id'))
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['value'])) {
                            $query->whereHas('student.studentClass', fn($q) => $q->where('major_id', $data['value']));
                        }
                    }),
                SelectFilter::make('dudika_id')->label('Filter DUDIKA')->options(Dudika::pluck('name', 'id'))->searchable(),

                // =========================================================
                // TAMBAHAN: FILTER STATUS LAPORAN (Menerjemahkan PHP ke SQL)
                // =========================================================
                SelectFilter::make('status_laporan')
                    ->label('Filter Status Laporan')
                    ->options([
                        'belum_lengkap' => '🔴 Belum Lengkap',
                        'menunggu_validasi' => '🟡 Menunggu Validasi',
                        'tervalidasi' => '🟢 Tervalidasi',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (empty($data['value'])) return;

                        // Kumpulan kondisi query raksasa agar Filament bisa mencari data yang 'Lengkap' murni via SQL
                        $isCompleteCondition = function ($q) {
                            $q->whereNotNull('pkl_field')->where('pkl_field', '!=', '')
                                ->whereHas('student', function ($sq) {
                                    $sq->whereNotNull('nisn')->where('nisn', '!=', '')
                                        ->whereNotNull('phone')->where('phone', '!=', '')
                                        ->whereNotNull('birth_place')->where('birth_place', '!=', '')
                                        ->whereNotNull('birth_date')
                                        ->whereNotNull('religion')->where('religion', '!=', '')
                                        ->whereNotNull('address')->where('address', '!=', '')
                                        ->whereNotNull('father_name')->where('father_name', '!=', '')
                                        ->whereNotNull('mother_name')->where('mother_name', '!=', '')
                                        ->whereNotNull('father_job')->where('father_job', '!=', '')
                                        ->whereNotNull('mother_job')->where('mother_job', '!=', '')
                                        ->whereNotNull('parent_address')->where('parent_address', '!=', '')
                                        ->whereNotNull('parent_phone')->where('parent_phone', '!=', '');
                                })
                                ->whereHas('dudika', function ($dq) {
                                    $dq->whereNotNull('address')->where('address', '!=', '')
                                        ->whereNotNull('head_name')->where('head_name', '!=', '')
                                        ->whereNotNull('supervisor_name')->where('supervisor_name', '!=', '')
                                        ->whereNotNull('supervisor_phone')->where('supervisor_phone', '!=', '');
                                })
                                ->whereHas('teacher', function ($tq) {
                                    $tq->whereNotNull('phone')->where('phone', '!=', '')
                                        ->whereNotNull('signature_path')->where('signature_path', '!=', '');
                                })
                                ->whereIn('id', function ($subQ) {
                                    $subQ->select('pkl_placement_id')->from('pkl_assessments');
                                });
                        };

                        if ($data['value'] === 'belum_lengkap') {
                            $query->whereNot($isCompleteCondition);
                        } elseif ($data['value'] === 'menunggu_validasi') {
                            $query->where($isCompleteCondition)->whereNull('pengesah_ks_nama');
                        } elseif ($data['value'] === 'tervalidasi') {
                            $query->where($isCompleteCondition)->whereNotNull('pengesah_ks_nama');
                        }
                    }),
            ])
            ->recordActions([
                ActionGroup::make([

                    // TOMBOL CEK KEKURANGAN (SANGAT KEBAL BADAI CSS)
                    Action::make('cek_kekurangan')
                        ->label('Cek Kekurangan')
                        ->icon('heroicon-o-information-circle')
                        ->color('danger')
                        ->visible(fn($record) => !self::checkCompleteness($record)['is_complete'])
                        ->modalHeading(fn($record) => 'Detail Kekurangan Data: ' . $record->student->name . ' (' . $record->student->nis . ')')
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Tutup')
                        ->form(function ($record) {
                            $check = self::checkCompleteness($record);

                            $waIcon = '<svg width="18" height="18" style="margin-right: 8px;" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/></svg>';
                            $chevronIcon = '<svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" x-bind:style="open ? \'transform: rotate(180deg); transition: transform 0.3s;\' : \'transition: transform 0.3s;\'"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>';

                            $html = "<div style='display: flex; flex-direction: column; gap: 16px;'>";

                            // --- CARD 1: SISWA (MERAH) ---
                            if (!empty($check['student'])) {
                                $phone = preg_replace('/[^0-9]/', '', $record->student->phone ?? '');
                                if (str_starts_with($phone, '0')) $phone = '62' . substr($phone, 1);
                                $text = "Halo *{$record->student->name}*, mohon segera melengkapi data PKL kamu:\n\n- " . implode("\n- ", $check['student']) . "\n\nTerima kasih.";
                                $waUrl = "https://wa.me/{$phone}?text=" . urlencode($text);

                                $studentIcon = '<svg width="20" height="20" style="margin-right: 8px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5" /></svg>';

                                $html .= "
                                <div x-data='{ open: true }' style='border: 1px solid #ef4444; border-radius: 8px; overflow: hidden;'>
                                    <button type='button' @click='open = !open' style='width: 100%; display: flex; justify-content: space-between; align-items: center; padding: 12px 16px; background-color: rgba(239, 68, 68, 0.1); border: none; cursor: pointer; text-align: left;'>
                                        <div style='display: flex; align-items: center; font-weight: bold; color: #ef4444;'>
                                            {$studentIcon} Kekurangan Data Siswa
                                        </div>
                                        <div style='color: #ef4444;'>{$chevronIcon}</div>
                                    </button>
                                    <div x-show='open' x-collapse style='padding: 16px;'>
                                        <ul style='list-style-type: disc; margin-left: 20px; margin-bottom: 16px; color: #ef4444; font-size: 14px; line-height: 1.5;'>
                                            <li>" . implode("</li><li>", $check['student']) . "</li>
                                        </ul>";
                                if (!empty($phone)) {
                                    $html .= "<a href='{$waUrl}' target='_blank' style='display: inline-flex; align-items: center; background-color: #22c55e; color: white; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-size: 14px; font-weight: bold;'>{$waIcon} Ingatkan Siswa (WA)</a>";
                                } else {
                                    $html .= "<span style='font-size: 12px; color: #9ca3af; font-style: italic;'>*Nomor HP Siswa belum diisi</span>";
                                }
                                $html .= "</div></div>";
                            }

                            // --- CARD 2: DUDIKA (ORANYE) ---
                            if (!empty($check['dudika'])) {
                                $phone = preg_replace('/[^0-9]/', '', $record->dudika->supervisor_phone ?? '');
                                if (str_starts_with($phone, '0')) $phone = '62' . substr($phone, 1);
                                $text = "Halo Bapak/Ibu Pembimbing di *{$record->dudika->name}*, kami menginfokan ada data PKL siswa ({$record->student->name}) yang belum lengkap:\n\n- " . implode("\n- ", $check['dudika']) . "\n\nMohon bantuannya. Terima kasih.";
                                $waUrl = "https://wa.me/{$phone}?text=" . urlencode($text);

                                $dudikaIcon = '<svg width="20" height="20" style="margin-right: 8px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" /></svg>';

                                $html .= "
                                <div x-data='{ open: true }' style='border: 1px solid #f97316; border-radius: 8px; overflow: hidden;'>
                                    <button type='button' @click='open = !open' style='width: 100%; display: flex; justify-content: space-between; align-items: center; padding: 12px 16px; background-color: rgba(249, 115, 22, 0.1); border: none; cursor: pointer; text-align: left;'>
                                        <div style='display: flex; align-items: center; font-weight: bold; color: #f97316;'>
                                            {$dudikaIcon} Kekurangan Data DUDIKA
                                        </div>
                                        <div style='color: #f97316;'>{$chevronIcon}</div>
                                    </button>
                                    <div x-show='open' x-collapse style='padding: 16px;'>
                                        <ul style='list-style-type: disc; margin-left: 20px; margin-bottom: 16px; color: #f97316; font-size: 14px; line-height: 1.5;'>
                                            <li>" . implode("</li><li>", $check['dudika']) . "</li>
                                        </ul>";
                                if (!empty($phone)) {
                                    $html .= "<a href='{$waUrl}' target='_blank' style='display: inline-flex; align-items: center; background-color: #22c55e; color: white; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-size: 14px; font-weight: bold;'>{$waIcon} Hubungi DUDIKA (WA)</a>";
                                } else {
                                    $html .= "<span style='font-size: 12px; color: #9ca3af; font-style: italic;'>*Nomor HP Pembimbing belum diisi</span>";
                                }
                                $html .= "</div></div>";
                            }

                            // --- CARD 3: GURU (BIRU) ---
                            if (!empty($check['teacher'])) {
                                $teacherIcon = '<svg width="20" height="20" style="margin-right: 8px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>';

                                $html .= "
                                <div x-data='{ open: true }' style='border: 1px solid #3b82f6; border-radius: 8px; overflow: hidden;'>
                                    <button type='button' @click='open = !open' style='width: 100%; display: flex; justify-content: space-between; align-items: center; padding: 12px 16px; background-color: rgba(59, 130, 246, 0.1); border: none; cursor: pointer; text-align: left;'>
                                        <div style='display: flex; align-items: center; font-weight: bold; color: #3b82f6;'>
                                            {$teacherIcon} Kekurangan Data Guru Pembimbing ({$record->teacher->name})
                                        </div>
                                        <div style='color: #3b82f6;'>{$chevronIcon}</div>
                                    </button>
                                    <div x-show='open' x-collapse style='padding: 16px;'>
                                        <p style='color: #3b82f6; font-size: 14px; margin-top: 0; margin-bottom: 8px;'>Mohon lengkapi profil Guru di menu Profil:</p>
                                        <ul style='list-style-type: disc; margin-left: 20px; color: #3b82f6; font-size: 14px; line-height: 1.5; margin-bottom: 0;'>
                                            <li>" . implode("</li><li>", $check['teacher']) . "</li>
                                        </ul>
                                    </div>
                                </div>";
                            }

                            $html .= "</div>";

                            return [Placeholder::make('info')->hiddenLabel()->content(new HtmlString($html))];
                        }),

                    Action::make('validasi_laporan')
                        ->label('Validasi Laporan')
                        ->icon('heroicon-o-check-badge')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Validasi & Sahkan Laporan')
                        ->modalDescription('Apakah Anda yakin data siswa ini sudah lengkap dan benar? Laporan ini akan disahkan dan ditandatangani secara digital atas nama Kepala Sekolah.')
                        ->modalSubmitActionLabel('Ya, Sahkan Laporan')
                        ->visible(function ($record) {
                            return (self::checkCompleteness($record)['is_complete'] && empty($record->pengesah_ks_nama));
                        })
                        ->action(function ($record) {
                            $school = SchoolProfile::first();
                            if (!$school || empty($school->headmaster_name)) {
                                Notification::make()->title('Gagal! Nama Kepala Sekolah belum diatur.')->danger()->send();
                                return;
                            }
                            $record->update(['pengesah_ks_nama' => $school->headmaster_name, 'pengesah_ks_nip' => $school->headmaster_nip ?? '-']);
                            Notification::make()->title('Laporan berhasil divalidasi!')->success()->send();
                        }),

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

                    Action::make('cetak_laporan_lengkap')
                        ->url(fn($record) => route('cetak.laporan-siswa', $record->id))
                        ->label('Cetak Buku Laporan')
                        ->icon('heroicon-o-printer')
                        ->color('success')
                        ->visible(fn($record) => !empty($record->pengesah_ks_nama))
                        ->openUrlInNewTab(),

                ])->label('Aksi Laporan')->button()->outlined(),
            ])
            ->toolbarActions([
                // =========================================================
                // TAMBAHAN: BULK ACTION (AKSI MASSAL) DENGAN KECERDASAN BUATAN
                // =========================================================
                BulkActionGroup::make([
                    BulkAction::make('bulk_validasi')
                        ->label('Validasi Terpilih')
                        ->icon('heroicon-o-check-badge')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalDescription('Hanya data yang "Sudah Lengkap" yang akan divalidasi. Data yang masih kurang akan dilewati otomatis oleh sistem.')
                        ->action(function (Collection $records) {
                            $school = SchoolProfile::first();
                            if (!$school || empty($school->headmaster_name)) {
                                Notification::make()->title('Gagal! Nama Kepala Sekolah belum diatur.')->danger()->send();
                                return;
                            }

                            $validated = 0;
                            $skipped = 0;

                            foreach ($records as $record) {
                                $check = self::checkCompleteness($record);
                                // Hanya proses yang lengkap dan belum disahkan
                                if ($check['is_complete'] && empty($record->pengesah_ks_nama)) {
                                    $record->update([
                                        'pengesah_ks_nama' => $school->headmaster_name,
                                        'pengesah_ks_nip' => $school->headmaster_nip ?? '-',
                                    ]);
                                    $validated++;
                                } else {
                                    $skipped++;
                                }
                            }

                            if ($validated > 0) {
                                Notification::make()
                                    ->title("Berhasil memvalidasi {$validated} laporan!")
                                    ->body($skipped > 0 ? "{$skipped} laporan dilewati karena belum lengkap / sudah tervalidasi." : "")
                                    ->success()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('Tidak ada laporan yang divalidasi.')
                                    ->body('Pastikan siswa yang Anda centang statusnya sudah "Menunggu Validasi".')
                                    ->warning()
                                    ->send();
                            }
                        }),

                    BulkAction::make('bulk_batal_validasi')
                        ->label('Batal Validasi Terpilih')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            foreach ($records as $record) {
                                if (!empty($record->pengesah_ks_nama)) {
                                    $record->update(['pengesah_ks_nama' => null, 'pengesah_ks_nip' => null]);
                                }
                            }
                            Notification::make()->title('Validasi massal berhasil dibatalkan.')->success()->send();
                        }),
                ]),
            ]);
    }
}
