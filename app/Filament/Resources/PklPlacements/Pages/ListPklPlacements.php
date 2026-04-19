<?php

namespace App\Filament\Resources\PklPlacements\Pages;

use App\Filament\Exports\PklPlacementExporter;
use App\Filament\Resources\PklPlacements\PklPlacementResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ExportAction;
use Filament\Resources\Pages\ListRecords;

class ListPklPlacements extends ListRecords
{
    protected static string $resource = PklPlacementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // 1. Tombol Download Data (Sekarang ada di sebelah kiri)
            ExportAction::make()
                ->label('Download Data')
                ->icon('heroicon-m-arrow-down-tray')
                ->color('success')
                ->exporter(PklPlacementExporter::class),

            // 2. Tombol Buat Penempatan PKL (Ditambah Icon Plus)
            CreateAction::make()
                ->label('Buat Penempatan')
                ->icon('heroicon-m-plus'),
        ];
    }
}
