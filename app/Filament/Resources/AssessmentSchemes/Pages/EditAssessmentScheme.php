<?php

namespace App\Filament\Resources\AssessmentSchemes\Pages;

use App\Filament\Resources\AssessmentSchemes\AssessmentSchemeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAssessmentScheme extends EditRecord
{
    protected static string $resource = AssessmentSchemeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
