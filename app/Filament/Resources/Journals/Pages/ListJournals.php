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

            CreateAction::make()
                ->icon('heroicon-o-plus')
                ->label('Buat Jurnal'),
        ];
    }
}
