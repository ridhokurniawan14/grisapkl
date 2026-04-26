<?php

namespace App\Filament\Resources\CetakLaporans\Pages;

use App\Filament\Resources\CetakLaporans\CetakLaporanResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCetakLaporan extends EditRecord
{
    protected static string $resource = CetakLaporanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
