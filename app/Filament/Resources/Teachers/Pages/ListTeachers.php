<?php

namespace App\Filament\Resources\Teachers\Pages;

use App\Filament\Exports\TeacherExport; // <-- Importnya sekarang pakai \Filament\Exports
use App\Filament\Imports\TeacherImporter;
use App\Filament\Resources\Teachers\TeacherResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListTeachers extends ListRecords
{
    protected static string $resource = TeacherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('download_excel')
                ->label('Download Excel')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action(fn() => Excel::download(new TeacherExport, 'Data_Guru_SMK.xlsx')),

            ImportAction::make()
                ->importer(TeacherImporter::class)
                ->label('Import Guru')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('warning'),

            CreateAction::make()
                ->label('Tambah Guru')
                ->icon('heroicon-o-plus')
                ->color('info'),
        ];
    }
}
