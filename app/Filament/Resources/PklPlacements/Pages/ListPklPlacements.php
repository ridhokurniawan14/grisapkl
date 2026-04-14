<?php

namespace App\Filament\Resources\PklPlacements\Pages;

use App\Filament\Resources\PklPlacements\PklPlacementResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPklPlacements extends ListRecords
{
    protected static string $resource = PklPlacementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
