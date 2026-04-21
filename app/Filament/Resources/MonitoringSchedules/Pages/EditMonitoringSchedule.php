<?php

namespace App\Filament\Resources\MonitoringSchedules\Pages;

use App\Filament\Resources\MonitoringSchedules\MonitoringScheduleResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditMonitoringSchedule extends EditRecord
{
    protected static string $resource = MonitoringScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
