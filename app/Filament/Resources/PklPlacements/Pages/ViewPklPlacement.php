<?php

namespace App\Filament\Resources\PklPlacements\Pages;

use App\Filament\Resources\PklPlacements\PklPlacementResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPklPlacement extends ViewRecord
{
    protected static string $resource = PklPlacementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
