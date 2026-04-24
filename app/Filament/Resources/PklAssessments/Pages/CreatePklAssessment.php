<?php

namespace App\Filament\Resources\PklAssessments\Pages;

use App\Filament\Resources\PklAssessments\PklAssessmentResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePklAssessment extends CreateRecord
{
    protected static string $resource = PklAssessmentResource::class;

    public array $scoresData = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Tangkap data array 'scores' lalu hapus dari data utama agar tabel pkl_assessments tidak error
        $this->scoresData = $data['scores'] ?? [];
        unset($data['scores']);

        return $data;
    }

    protected function afterCreate(): void
    {
        // Masukkan nilai satu per satu ke tabel pkl_assessment_scores
        foreach ($this->scoresData as $indicatorId => $scoreValue) {
            $this->record->scores()->create([
                'assessment_indicator_id' => $indicatorId,
                'score' => $scoreValue,
            ]);
        }
    }
}
