<?php

namespace App\Filament\Resources\PklPlacements\Pages;

use App\Filament\Exports\PklPlacementExporter; // <-- Import-nya sudah kembali ke habitat aslinya bro
use App\Filament\Resources\PklPlacements\PklPlacementResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel; // Wajib import Facade Excel

class ListPklPlacements extends ListRecords
{
    protected static string $resource = PklPlacementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('download_excel')
                ->label('Download Excel')
                ->icon('heroicon-m-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    // Ambil query data sesuai filter yang dipilih Humas
                    $query = $this->getFilteredTableQuery();

                    return Excel::download(
                        new PklPlacementExporter($query),
                        'Data-Penempatan-PKL-' . now()->format('d-m-Y') . '.xlsx'
                    );
                }),

            Actions\CreateAction::make()
                ->label('Buat Penempatan')
                ->icon('heroicon-m-plus'),
        ];
    }
}
