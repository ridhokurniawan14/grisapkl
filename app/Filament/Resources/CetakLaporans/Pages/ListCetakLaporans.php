<?php

namespace App\Filament\Resources\CetakLaporans\Pages;

use App\Filament\Resources\CetakLaporans\CetakLaporanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCetakLaporans extends ListRecords
{
    protected static string $resource = CetakLaporanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
