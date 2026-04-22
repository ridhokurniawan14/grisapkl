<?php

namespace App\Filament\Resources\AssessmentElements\Pages;

use App\Filament\Resources\AssessmentElements\AssessmentElementResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAssessmentElement extends ViewRecord
{
    protected static string $resource = AssessmentElementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
