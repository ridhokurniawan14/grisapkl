<?php

namespace App\Filament\Resources\Dudikas\Pages;

use App\Filament\Resources\Dudikas\DudikaResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewDudika extends ViewRecord
{
    protected static string $resource = DudikaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
