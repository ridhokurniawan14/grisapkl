<?php

namespace App\Filament\Resources\Journals\Pages;

use App\Filament\Exports\JournalExport;
use App\Filament\Resources\Journals\JournalResource;
use App\Models\Journal;
use App\Models\PklPlacement;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;

class ListJournals extends ListRecords
{
    protected static string $resource = JournalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // TOMBOL DOWNLOAD PDF
            Action::make('download_pdf')
                ->label('Cetak PDF')
                ->icon('heroicon-o-printer')
                ->color('danger')
                ->url(function () {
                    $ids = $this->getFilteredTableQuery()->pluck('id')->toArray();

                    $filters   = $this->tableFilters;
                    $start     = $filters['date_range']['start'] ?? null;
                    $end       = $filters['date_range']['end'] ?? null;
                    $studentId = $filters['student_id']['value'] ?? null;

                    if (empty($ids)) {
                        return '#';
                    }

                    return route('journal.pdf', [
                        'ids'        => implode(',', $ids),
                        'start'      => $start,
                        'end'        => $end,
                        'student_id' => $studentId,
                    ]);
                })
                ->openUrlInNewTab(),

            // TOMBOL EXCEL
            Action::make('download_excel')
                ->label('Download Excel')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action(function () {
                    $ids = $this->getFilteredTableQuery()->pluck('id')->toArray();

                    $filters = $this->tableFilters;
                    $start = $filters['date_range']['start'] ?? null;
                    $end = $filters['date_range']['end'] ?? null;
                    $studentId = $filters['student_id']['value'] ?? null;

                    if (empty($ids) && !$start) {
                        Notification::make()->title('Data Kosong! Silakan cari data dulu.')->warning()->send();
                        return;
                    }

                    return Excel::download(
                        new JournalExport($ids, $start, $end, $studentId),
                        'Data_Jurnal_PKL.xlsx'
                    );
                }),

            // TOMBOL REKAP BELUM UPLOAD
            Action::make('download_unuploaded')
                ->label('Rekap Belum Upload')
                ->icon('heroicon-o-exclamation-triangle')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Download Seluruh Data Belum Upload?')
                ->modalDescription('Sistem akan mengecek seluruh data jurnal siswa yang belum upload foto. Proses ini mungkin memakan waktu beberapa saat, silakan tunggu.')
                ->modalSubmitActionLabel('Ya, Download Sekarang')
                ->action(function () {
                    $query = \App\Models\Journal::query()
                        ->whereIn('attend_status', ['Hadir', 'Izin', 'Sakit'])
                        ->where(function ($q) {
                            $q->whereNull('photo_path')
                                ->orWhere('photo_path', '');
                        })
                        ->with(['pklPlacement.student', 'pklPlacement.dudika', 'pklPlacement.teacher']);

                    if ($query->count() === 0) {
                        Notification::make()
                            ->title('Bagus!')
                            ->body('Seluruh siswa dari semua jurusan sudah disiplin mengupload foto kegiatan.')
                            ->success()
                            ->send();
                        return;
                    }

                    return Excel::download(
                        new \App\Filament\Exports\UnuploadedJournalExport($query),
                        'Semua_Rekap_Belum_Upload_Foto.xlsx'
                    );
                }),

            // ====================================================
            // TOMBOL BARU: JURNAL MASAL
            // ====================================================
            Action::make('jurnal_masal')
                ->label('Buat Jurnal Masal')
                ->icon('heroicon-o-clipboard-document-check')
                ->color('primary')
                ->modalHeading('Buat Jurnal Masal (Kendala Server/Internet)')
                ->modalDescription('Fitur ini akan membuat jurnal otomatis untuk SEMUA siswa PKL di tanggal tertentu. Siswa yang sudah melakukan absen di tanggal tersebut akan dilewati (tidak diduplikasi).')
                ->form([
                    DatePicker::make('date')
                        ->label('Tanggal Jurnal')
                        ->required()
                        ->default(now()),
                    Select::make('attend_status')
                        ->label('Status Kehadiran')
                        ->options([
                            'Hadir' => 'Hadir',
                            'Sakit' => 'Sakit',
                            'Izin' => 'Izin',
                            'Libur' => 'Libur',
                        ])
                        ->required()
                        ->default('Hadir'),
                ])
                ->action(function (array $data) {
                    $date = $data['date'];
                    $status = $data['attend_status'];

                    // 1. Ambil semua ID penempatan PKL 
                    $placements = PklPlacement::pluck('id')->toArray();

                    // 2. Ambil ID penempatan PKL yang SUDAH ABSEN di tanggal tersebut
                    $existingJournals = Journal::whereDate('date', $date)
                        ->pluck('pkl_placement_id')
                        ->toArray();

                    $insertData = [];
                    $now = now();

                    // 3. Looping semua penempatan PKL
                    foreach ($placements as $placementId) {
                        if (!in_array($placementId, $existingJournals)) {
                            $insertData[] = [
                                'pkl_placement_id' => $placementId,
                                'date'             => $date,
                                'time'             => '07:00:00', // Default jam absen karena kolom "time" wajib (NOT NULL)
                                'attend_status'    => $status,
                                'activity'         => 'Jurnal dibuat otomatis oleh sistem/admin karena kendala internet atau server.', // Menggunakan kolom "activity"
                                'is_valid'         => 1, // Langsung set valid (1) karena dibuat oleh admin, "is_valid" wajib (NOT NULL)
                                'created_at'       => $now,
                                'updated_at'       => $now,
                            ];
                        }
                    }

                    // 4. Proses Batch Insert
                    if (!empty($insertData)) {
                        Journal::insert($insertData);

                        Notification::make()
                            ->title('Berhasil!')
                            ->body(count($insertData) . ' Jurnal berhasil ditambahkan untuk tanggal ' . $date)
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Tidak Ada Jurnal Baru')
                            ->body('Semua siswa sudah melakukan absensi di tanggal tersebut.')
                            ->warning()
                            ->send();
                    }
                }),

            CreateAction::make()
                ->icon('heroicon-o-plus')
                ->label('Buat Jurnal'),
        ];
    }
}
