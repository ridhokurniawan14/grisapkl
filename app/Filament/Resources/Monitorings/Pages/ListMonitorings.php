<?php

namespace App\Filament\Resources\Monitorings\Pages;

use App\Exports\MonitoringExport;
use App\Filament\Resources\Monitorings\MonitoringResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListMonitorings extends ListRecords
{
    protected static string $resource = MonitoringResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('download_excel')
                ->label('Download Excel')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action(function () {
                    // Mengambil ID data yang tampil di tabel (sudah terfilter)
                    $ids = $this->getFilteredTableQuery()->pluck('id')->toArray();

                    if (empty($ids)) {
                        \Filament\Notifications\Notification::make()->title('Data Kosong!')->warning()->send();
                        return;
                    }

                    return Excel::download(new MonitoringExport($ids), 'Data_Monitoring_PKL.xlsx');
                }),

            Actions\CreateAction::make()
                ->icon('heroicon-o-plus')
                ->label('Buat Laporan'),
        ];
    }
}
