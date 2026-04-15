<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Resources\Students\StudentResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Imports\StudentImporter;
use App\Models\StudentClass;
use Filament\Forms\Components\Select;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make()
                ->importer(StudentImporter::class)
                ->label('Import Data Siswa')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success'),

            CreateAction::make(),
        ];
    }
}
