<?php

namespace App\Filament\Resources\PklAssessments\Pages;

use App\Filament\Resources\PklAssessments\PklAssessmentResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPklAssessments extends ListRecords
{
    protected static string $resource = PklAssessmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_excel')
                ->label('Download Excel')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action(function () {
                    return \Maatwebsite\Excel\Facades\Excel::download(
                        new \App\Exports\PklAssessmentsExport,
                        'Rekap_Nilai_PKL_' . date('Ymd') . '.xlsx'
                    );
                }),
            CreateAction::make(),
        ];
    }
}
