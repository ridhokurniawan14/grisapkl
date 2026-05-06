<?php

namespace App\Filament\Resources\Journals\Pages;

use App\Filament\Resources\Journals\JournalResource;
use Filament\Resources\Pages\CreateRecord;

class CreateJournal extends CreateRecord
{
    protected static string $resource = JournalResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if ($data['attend_status'] === 'Libur' && empty($data['activity'])) {
            $data['activity'] = 'Libur';
        }

        return $data;
    }
}
