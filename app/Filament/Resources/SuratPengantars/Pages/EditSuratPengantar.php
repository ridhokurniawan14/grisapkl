<?php

namespace App\Filament\Resources\SuratPengantars\Pages;

use App\Filament\Resources\SuratPengantars\SuratPengantarResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSuratPengantar extends EditRecord
{
    protected static string $resource = SuratPengantarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
