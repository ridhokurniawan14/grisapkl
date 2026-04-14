<?php

namespace App\Filament\Resources\Dudikas\Pages;

use App\Filament\Resources\Dudikas\DudikaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDudikas extends ListRecords
{
    protected static string $resource = DudikaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
