<?php

namespace App\Filament\Resources\AssessmentSchemes\Pages;

use App\Filament\Resources\AssessmentSchemes\AssessmentSchemeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAssessmentSchemes extends ListRecords
{
    protected static string $resource = AssessmentSchemeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
