<?php

namespace App\Filament\Resources\PklAssessments\Schemas;

use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;

class PklAssessmentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Penilaian')
                ->icon('heroicon-m-document-text')
                ->columnSpanFull() // Pasti full lebar
                ->schema([
                    Grid::make(2)->schema([
                        TextEntry::make('pklPlacement.student.name')
                            ->label('Nama Siswa')
                            ->weight('bold')
                            ->size('lg'),
                        TextEntry::make('pklPlacement.dudika.name')
                            ->label('Tempat PKL'),
                        TextEntry::make('pklPlacement.assessmentScheme.name')
                            ->label('Skema Penilaian')
                            ->badge()
                            ->color('info'),
                        TextEntry::make('rata_rata')
                            ->label('Nilai Rata-rata Total')
                            ->badge()
                            ->color('success')
                            ->state(function ($record) {
                                $avg = $record->scores->avg('score');
                                return $avg ? number_format($avg, 2) : '0';
                            }),
                        TextEntry::make('attendance_notes')
                            ->label('Catatan Kehadiran')
                            ->columnSpanFull(),
                        TextEntry::make('assessment_notes')
                            ->label('Catatan Kualitatif')
                            ->columnSpanFull(),
                    ])
                ]),

            // =========================================================
            // REVISI: GENERATE COLLAPSIBLE BAR DINAMIS (ANTI ERROR)
            // =========================================================
            Section::make('Rincian Nilai per Indikator')
                ->icon('heroicon-m-star')
                ->columnSpanFull() // Full lebar sesuai request
                ->schema(function ($record) {
                    // 1. Tarik semua nilai beserta relasi indikator dan elemennya
                    $scores = $record->scores()->with('assessmentIndicator.assessmentElement')->get();

                    // 2. Kelompokkan berdasarkan Nama Elemen
                    $grouped = $scores->groupBy(function ($item) {
                        return $item->assessmentIndicator->assessmentElement->name ?? 'Elemen Penilaian';
                    });

                    $sections = [];

                    // 3. Loop setiap kelompok elemen untuk dibuatkan Bar/Section
                    foreach ($grouped as $elementName => $items) {

                        // Hitung rata-rata per elemen secara langsung di PHP
                        $avg = number_format($items->avg('score'), 2);

                        $indicatorEntries = [];

                        // Loop setiap indikator di dalam elemen ini
                        foreach ($items as $score) {
                            $indicatorEntries[] = Grid::make(12)->schema([
                                \Filament\Infolists\Components\TextEntry::make('ind_name_' . $score->id)
                                    ->hiddenLabel()
                                    ->default($score->assessmentIndicator->name)
                                    ->icon('heroicon-m-check-circle')
                                    ->iconColor('primary')
                                    ->columnSpan(['default' => 12, 'md' => 10]),

                                \Filament\Infolists\Components\TextEntry::make('ind_score_' . $score->id)
                                    ->hiddenLabel()
                                    ->default($score->score)
                                    ->badge()
                                    ->color('success')
                                    ->size('lg')
                                    ->columnSpan(['default' => 12, 'md' => 2]),
                            ])->extraAttributes(['class' => 'border-b border-gray-100 py-2']); // Tambahan pemanis garis bawah tipis
                        }

                        // 4. Bungkus menjadi Section (Bar) yang bisa di-collapse
                        $sections[] = Section::make($elementName)
                            ->description('Rata-rata Nilai Elemen: ' . $avg)
                            ->icon('heroicon-m-bookmark-square')
                            ->collapsible()
                            ->collapsed() // Bikin tertutup default biar kayak "Tab Bar"
                            ->compact() // Desain lebih rapat/minimalis
                            ->schema($indicatorEntries);
                    }

                    return $sections;
                }),
        ]);
    }
}
