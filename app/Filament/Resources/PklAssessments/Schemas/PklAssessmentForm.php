<?php

namespace App\Filament\Resources\PklAssessments\Schemas;

use App\Models\PklPlacement;
use App\Models\AssessmentIndicator;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class PklAssessmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            // =========================================================
            // CARD 1: DATA UTAMA & CATATAN
            // =========================================================
            Section::make('Data Penilaian & Catatan DUDIKA')
                ->icon('heroicon-m-clipboard-document-check')
                ->columnSpanFull()
                ->schema([
                    Select::make('pkl_placement_id')
                        ->label('Siswa PKL')
                        ->options(function (?\Illuminate\Database\Eloquent\Model $record) {
                            $query = PklPlacement::whereNotNull('assessment_scheme_id')->with('student');

                            if (!$record) {
                                $assessedIds = \App\Models\PklAssessment::pluck('pkl_placement_id')->toArray();
                                $query->whereNotIn('id', $assessedIds);
                            }

                            return $query->get()->mapWithKeys(function ($p) {
                                return [$p->id => $p->student->nis . ' - ' . $p->student->name . ' (' . $p->dudika->name . ')'];
                            });
                        })
                        ->searchable()
                        ->preload()
                        ->required()
                        ->live()
                        ->columnSpanFull(),

                    Grid::make(2)->schema([
                        Textarea::make('attendance_notes')
                            ->label('Catatan Kehadiran')
                            ->placeholder('Contoh: Siswa sangat rajin dan tepat waktu...')
                            ->rows(3),

                        Textarea::make('assessment_notes')
                            ->label('Catatan Penilaian (Kualitatif)')
                            ->placeholder('Contoh: Penguasaan materi jaringan sangat baik...')
                            ->rows(3),
                    ]),
                ]),

            // =========================================================
            // CARD 2: FORM NILAI DINAMIS (DENGAN TABS)
            // =========================================================
            Section::make('Input Nilai Indikator (85 - 100)')
                ->icon('heroicon-m-star')
                ->columnSpanFull()
                ->description('Pilih tab kategori elemen di bawah ini untuk mengisi nilai.')
                ->schema(function (Get $get) {
                    $placementId = $get('pkl_placement_id');

                    if (!$placementId) {
                        return [
                            Placeholder::make('info')
                                ->hiddenLabel()
                                ->content('Silakan pilih Siswa PKL terlebih dahulu untuk memunculkan form nilai.')
                        ];
                    }

                    $placement = PklPlacement::find($placementId);
                    if (!$placement || !$placement->assessment_scheme_id) return [];

                    $indicators = AssessmentIndicator::where('assessment_scheme_id', $placement->assessment_scheme_id)
                        ->where('is_active', true)
                        ->with('assessmentElement')
                        ->get()
                        ->groupBy('assessment_element_id');

                    $tabsArray = [];

                    // Loop setiap Elemen untuk dijadikan Tab
                    foreach ($indicators as $elementId => $inds) {
                        $element = $inds->first()->assessmentElement;

                        $indicatorInputs = [];
                        foreach ($inds as $ind) {
                            $indicatorInputs[] = TextInput::make("scores.{$ind->id}")
                                ->label($ind->name)
                                ->numeric()
                                // REVISI: Range Nilai Wajib 85 - 100
                                ->minValue(85)
                                ->maxValue(100)
                                ->placeholder('85 - 100') // Bantuan visual untuk user
                                ->required()
                                ->columnSpan(['default' => 12, 'md' => 6]);
                        }

                        // MANTRA SAKTI: Render HTML untuk Bintang Merah pada Judul Tab
                        $tabTitle = new HtmlString($element->name . ' <span class="text-danger-600 font-bold" title="Wajib Diisi">*</span>');

                        $tabsArray[] = Tab::make($element->name)
                            ->label($tabTitle) // Masukkan label HTML-nya ke sini
                            ->icon('heroicon-m-check-circle')
                            ->schema([
                                Grid::make(12)->schema($indicatorInputs)
                            ]);
                    }

                    return [
                        Tabs::make('Tabs Penilaian')
                            ->tabs($tabsArray)
                            ->contained(true)
                            ->columnSpanFull()
                    ];
                }),
        ]);
    }
}
