<?php

namespace App\Filament\Resources\Monitorings\Pages;

use App\Filament\Resources\Monitorings\MonitoringResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditMonitoring extends EditRecord
{
    protected static string $resource = MonitoringResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
