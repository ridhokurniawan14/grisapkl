<?php

namespace App\Filament\Resources\Dudikas\Pages;

use App\Filament\Resources\Dudikas\DudikaResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditDudika extends EditRecord
{
    protected static string $resource = DudikaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
