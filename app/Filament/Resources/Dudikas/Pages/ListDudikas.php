<?php

namespace App\Filament\Resources\Dudikas\Pages;

use App\Filament\Exports\DudikaExport;
use App\Filament\Imports\DudikaImporter;
use App\Filament\Resources\Dudikas\DudikaResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListDudikas extends ListRecords
{
    protected static string $resource = DudikaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // TOMBOL EKSPOR CUSTOM (LANGSUNG DOWNLOAD + DESAIN CANTIK)
            Action::make('download_excel')
                ->label('Download Excel')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action(fn() => Excel::download(new DudikaExport, 'Data_DUDIKA_SMK.xlsx')),

            ImportAction::make()
                ->importer(DudikaImporter::class)
                ->label('Import DUDIKA')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('warning'),

            CreateAction::make()
                ->label('Tambah DUDIKA')
                ->icon('heroicon-o-plus')
                ->color('info'),
        ];
    }
}
