<?php

namespace App\Filament\Resources\Dudikas\Pages;

use App\Filament\Imports\DudikaImporter;
use App\Filament\Resources\Dudikas\DudikaResource;
use Filament\Actions;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;

class ListDudikas extends ListRecords
{
    protected static string $resource = DudikaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make()
                ->importer(DudikaImporter::class)
                ->label('Import DUDIKA')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success'),
            Actions\CreateAction::make(),
        ];
    }
}
