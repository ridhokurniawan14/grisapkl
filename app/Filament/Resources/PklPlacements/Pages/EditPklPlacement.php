<?php

namespace App\Filament\Resources\PklPlacements\Pages;

use App\Filament\Resources\PklPlacements\PklPlacementResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPklPlacement extends EditRecord
{
    protected static string $resource = PklPlacementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
