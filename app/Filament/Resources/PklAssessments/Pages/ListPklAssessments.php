<?php

namespace App\Filament\Resources\PklAssessments\Pages;

use App\Filament\Resources\PklAssessments\PklAssessmentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPklAssessments extends ListRecords
{
    protected static string $resource = PklAssessmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
