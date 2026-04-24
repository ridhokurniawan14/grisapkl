<?php

namespace App\Filament\Resources\PklAssessments\Schemas;

use App\Models\PklPlacement;
use App\Models\AssessmentIndicator;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

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
                ->schema([
                    Select::make('pkl_placement_id')
                        ->label('Siswa PKL')
                        ->options(function (?\Illuminate\Database\Eloquent\Model $record) {
                            // Ambil data penempatan yang SUDAH PUNYA SKEMA
                            $query = PklPlacement::whereNotNull('assessment_scheme_id')->with('student');

                            // Logika: Jangan tampilkan siswa yang sudah dinilai di form Create
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
                        ->live() // MANTRA SAKTI: Wajib live agar form nilai di bawahnya langsung tergambar!
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
            // CARD 2: FORM NILAI DINAMIS (AUTO-GENERATE)
            // =========================================================
            Section::make('Input Nilai Indikator (0 - 100)')
                ->icon('heroicon-m-star')
                ->description('Kolom penilaian di bawah ini otomatis menyesuaikan dengan Skema Penilaian siswa yang dipilih.')
                ->schema(function (Get $get) {
                    $placementId = $get('pkl_placement_id');

                    // Jika belum milih siswa, sembunyikan form nilainya
                    if (!$placementId) {
                        return [
                            \Filament\Forms\Components\Placeholder::make('info')
                                ->hiddenLabel()
                                ->content('Silakan pilih Siswa PKL terlebih dahulu untuk memunculkan form nilai.')
                        ];
                    }

                    // Cari skema yang dipakai siswa ini
                    $placement = PklPlacement::find($placementId);
                    if (!$placement || !$placement->assessment_scheme_id) return [];

                    // Ambil indikator berdasarkan skema siswa tersebut, dan kelompokkan berdasarkan Elemen
                    $indicators = AssessmentIndicator::where('assessment_scheme_id', $placement->assessment_scheme_id)
                        ->where('is_active', true)
                        ->with('assessmentElement')
                        ->get()
                        ->groupBy('assessment_element_id');

                    $schema = [];

                    // Loop setiap Elemen (Misal: Soft Skill, Hard Skill)
                    foreach ($indicators as $elementId => $inds) {
                        $element = $inds->first()->assessmentElement;

                        $indicatorInputs = [];
                        // Loop indikatornya (Misal: Jujur, Disiplin)
                        foreach ($inds as $ind) {
                            $indicatorInputs[] = TextInput::make("scores.{$ind->id}") // Simpan ke array 'scores[id]'
                                ->label($ind->name)
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(100)
                                ->required()
                                ->columnSpan(['default' => 12, 'md' => 6]);
                        }

                        // Bungkus inputan tadi ke dalam Fieldset berdasarkan nama Elemen
                        $schema[] = Fieldset::make($element->name)
                            ->schema($indicatorInputs)
                            ->columns(12);
                    }

                    return $schema;
                }),
        ]);
    }
}
