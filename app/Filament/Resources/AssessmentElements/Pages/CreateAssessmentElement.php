<?php

namespace App\Filament\Resources\AssessmentElements\Pages;

use App\Filament\Resources\AssessmentElements\AssessmentElementResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAssessmentElement extends CreateRecord
{
    protected static string $resource = AssessmentElementResource::class;

    // 1. Siapkan wadah sementara untuk menampung data repeater
    public array $schemeGroupsData = [];

    // 2. Cegah error database dengan mengambil data repeater lalu membuangnya sebelum proses insert
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->schemeGroupsData = $data['scheme_groups'] ?? [];
        unset($data['scheme_groups']); // Buang agar tidak masuk ke query INSERT elemen

        return $data;
    }

    // 3. Setelah Elemen Penilaian sukses dibuat, baru kita masukkan indikator-indikatornya
    protected function afterCreate(): void
    {
        $element = $this->record;

        foreach ($this->schemeGroupsData as $group) {
            $schemeId = $group['assessment_scheme_id'] ?? null;
            $indicators = $group['indicators'] ?? [];

            if ($schemeId) {
                foreach ($indicators as $indData) {
                    $element->assessmentIndicators()->create([
                        'assessment_scheme_id' => $schemeId,
                        'name' => $indData['name'],
                        'is_active' => $indData['is_active'] ?? true,
                    ]);
                }
            }
        }
    }
}
