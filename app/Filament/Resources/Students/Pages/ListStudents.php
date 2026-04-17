<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Exports\StudentExport;
use App\Filament\Imports\StudentImporter;
use App\Filament\Resources\Students\StudentResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('download_excel')
                ->label('Download Excel')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action(function () {
                    // AMBIL QUERY YANG SEDANG TER-FILTER DI TABEL
                    $filteredQuery = $this->getFilteredTableQuery();

                    // Pastikan relasi ikut terbawa agar tidak berat
                    $filteredQuery->with(['studentClass', 'academicYear']);

                    // Kirim query-nya ke StudentExport
                    return Excel::download(new StudentExport($filteredQuery), 'Data_Siswa_Filtered.xlsx');
                }),

            ImportAction::make()
                ->importer(StudentImporter::class)
                ->label('Import Siswa')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('warning'),

            CreateAction::make()
                ->label('Tambah Siswa')
                ->icon('heroicon-o-plus')
                ->color('info'),
        ];
    }
}
