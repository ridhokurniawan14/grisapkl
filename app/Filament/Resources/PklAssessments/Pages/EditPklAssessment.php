<?php

namespace App\Filament\Resources\PklAssessments\Pages;

use App\Filament\Resources\PklAssessments\PklAssessmentResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditPklAssessment extends EditRecord
{
    protected static string $resource = PklAssessmentResource::class;

    public array $scoresData = [];

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali')
                ->url($this->getResource()::getUrl('index'))
                ->icon('heroicon-m-arrow-left')
                ->color('gray'),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Saat form edit dibuka, ambil data skor dari database dan format jadi array 'scores[id]'
        $scores = $this->record->scores()->pluck('score', 'assessment_indicator_id')->toArray();
        $data['scores'] = $scores;

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->scoresData = $data['scores'] ?? [];
        unset($data['scores']);

        return $data;
    }

    protected function afterSave(): void
    {
        // Update data skor
        foreach ($this->scoresData as $indicatorId => $scoreValue) {
            $this->record->scores()->updateOrCreate(
                ['assessment_indicator_id' => $indicatorId], // Cari berdasarkan indikator
                ['score' => $scoreValue] // Update nilainya
            );
        }
    }
}
