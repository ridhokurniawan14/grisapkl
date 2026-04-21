<?php

namespace App\Filament\Resources\MonitoringSchedules\Pages;

use App\Filament\Resources\MonitoringSchedules\MonitoringScheduleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMonitoringSchedules extends ListRecords
{
    protected static string $resource = MonitoringScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Buat Jadwal Monitoring')
                ->icon('heroicon-o-plus')
                ->color('info'),
        ];
    }
}
