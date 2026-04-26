<?php

namespace App\Filament\Resources\SuratPengantars\Pages;

use App\Filament\Resources\SuratPengantars\SuratPengantarResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSuratPengantars extends ListRecords
{
    protected static string $resource = SuratPengantarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // CreateAction::make(),
        ];
    }
}
