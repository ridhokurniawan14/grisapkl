<?php

namespace App\Filament\Resources\Journals\Pages;

use App\Filament\Exports\JournalExport;
use App\Filament\Resources\Journals\JournalResource;
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
                ->openUrlInNewTab(), // ✅ buka tab baru, browser render PDF viewer langsung
            // TOMBOL EXCEL
            Action::make('download_excel')
                ->label('Download Excel')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action(function () {
                    $ids = $this->getFilteredTableQuery()->pluck('id')->toArray();

                    // MANTRA SAKTI FIX: Langsung ambil dari properti tableFilters
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

            // ====================================================
            // TOMBOL BARU: DOWNLOAD SEMUA SISWA BELUM UPLOAD FOTO
            // ====================================================
            Action::make('download_unuploaded')
                ->label('Rekap Belum Upload')
                ->icon('heroicon-o-exclamation-triangle')
                ->color('warning')
                ->requiresConfirmation() // Menampilkan modal konfirmasi
                ->modalHeading('Download Seluruh Data Belum Upload?')
                ->modalDescription('Sistem akan mengecek seluruh data jurnal siswa yang belum upload foto. Proses ini mungkin memakan waktu beberapa saat, silakan tunggu.')
                ->modalSubmitActionLabel('Ya, Download Sekarang') // Tombol ini otomatis akan loading (spinner) saat diklik
                ->action(function () {
                    // 1. Ambil SEMUA data dari database, hiraukan filter di tabel
                    $query = \App\Models\Journal::query()
                        ->whereIn('attend_status', ['Hadir', 'Izin', 'Sakit'])
                        ->where(function ($q) {
                            $q->whereNull('photo_path')
                                ->orWhere('photo_path', '');
                        })
                        ->with(['pklPlacement.student', 'pklPlacement.dudika', 'pklPlacement.teacher']);

                    // 2. Cek apakah ada datanya
                    if ($query->count() === 0) {
                        Notification::make()
                            ->title('Bagus!')
                            ->body('Seluruh siswa dari semua jurusan sudah disiplin mengupload foto kegiatan.')
                            ->success()
                            ->send();
                        return;
                    }

                    // 3. Download Excel
                    return Excel::download(
                        new \App\Filament\Exports\UnuploadedJournalExport($query),
                        'Semua_Rekap_Belum_Upload_Foto.xlsx'
                    );
                }),
            CreateAction::make()
                ->icon('heroicon-o-plus')
                ->label('Buat Jurnal'),
        ];
    }
}
