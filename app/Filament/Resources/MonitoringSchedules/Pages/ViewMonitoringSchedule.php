<?php

namespace App\Filament\Resources\MonitoringSchedules\Pages;

use App\Filament\Resources\MonitoringSchedules\MonitoringScheduleResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewMonitoringSchedule extends ViewRecord
{
    protected static string $resource = MonitoringScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
