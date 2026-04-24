<?php

namespace App\Filament\Resources\AssessmentElements\Pages;

use App\Filament\Resources\AssessmentElements\AssessmentElementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAssessmentElement extends EditRecord
{
    protected static string $resource = AssessmentElementResource::class;

    public array $schemeGroupsData = [];

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    // 1. SAAT HALAMAN DIBUKA: Tarik data dari database dan susun sesuai format form Nested Repeater
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $element = $this->record;

        // Tarik semua indikator milik elemen ini
        $indicators = $element->assessmentIndicators;

        $schemeGroups = [];
        // Kelompokkan berdasarkan Skema
        $grouped = $indicators->groupBy('assessment_scheme_id');

        foreach ($grouped as $schemeId => $inds) {
            $schemeGroups[] = [
                'assessment_scheme_id' => $schemeId,
                'indicators' => $inds->map(function ($ind) {
                    return [
                        'id' => $ind->id, // Tahan ID aslinya di sini
                        'name' => $ind->name,
                        'is_active' => $ind->is_active,
                    ];
                })->toArray(),
            ];
        }

        $data['scheme_groups'] = $schemeGroups;
        return $data;
    }

    // 2. SAAT TOMBOL SIMPAN DIKLIK: Ambil data repeater dan buang agar tidak error saat update elemen
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->schemeGroupsData = $data['scheme_groups'] ?? [];
        unset($data['scheme_groups']);

        return $data;
    }

    // 3. SETELAH ELEMEN TER-UPDATE: Sinkronisasi data Indikator
    protected function afterSave(): void
    {
        $element = $this->record;
        $submittedIndicatorIds = []; // Mencatat ID apa saja yang masih ada di form

        foreach ($this->schemeGroupsData as $group) {
            $schemeId = $group['assessment_scheme_id'] ?? null;
            $indicators = $group['indicators'] ?? [];

            if ($schemeId) {
                foreach ($indicators as $indData) {
                    // UpdateOrCreate: Jika ada ID-nya maka di-update, jika tidak ada berarti buat baru
                    $indicator = $element->assessmentIndicators()->updateOrCreate(
                        ['id' => $indData['id'] ?? null], // Kunci pencarian
                        [
                            'assessment_scheme_id' => $schemeId,
                            'name' => $indData['name'],
                            'is_active' => $indData['is_active'] ?? true,
                        ]
                    );
                    $submittedIndicatorIds[] = $indicator->id;
                }
            }
        }

        // 4. BERSIH-BERSIH: Hapus indikator di database jika Humas menghapusnya dari tampilan Form (Tombol Tong Sampah)
        $element->assessmentIndicators()
            ->whereNotIn('id', $submittedIndicatorIds)
            ->delete();
    }
}
