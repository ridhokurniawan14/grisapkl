<?php

namespace App\Filament\Resources\AssessmentElements\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class AssessmentElementInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // =========================================================
                // CARD 1: INFORMASI UTAMA (DENGAN TANGGAL)
                // =========================================================
                Section::make('Informasi Utama Elemen')
                    ->icon('heroicon-m-document-text')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('name')
                                ->label('Nama Elemen Utama')
                                ->weight('bold')
                                ->size('lg')
                                ->columnSpanFull(),

                            TextEntry::make('tp_name')
                                ->label('Tujuan Pembelajaran (TP)')
                                ->color('gray')
                                ->columnSpanFull(),

                            TextEntry::make('created_at')
                                ->label('Dibuat Pada')
                                ->dateTime('d M Y, H:i')
                                ->icon('heroicon-m-calendar'),

                            TextEntry::make('updated_at')
                                ->label('Terakhir Diupdate')
                                ->dateTime('d M Y, H:i')
                                ->icon('heroicon-m-clock'),
                        ]),
                    ]),

                // =========================================================
                // CARD 2: NESTED REPEATER (DENGAN BADGE KEREN)
                // =========================================================
                Section::make('Daftar Indikator per Skema')
                    ->icon('heroicon-m-list-bullet')
                    ->columnSpanFull()
                    ->description('Daftar indikator yang sudah dikelompokkan. Klik judul skema untuk melihat detailnya.')
                    ->schema([
                        RepeatableEntry::make('grouped_indicators')
                            ->hiddenLabel()
                            ->state(function (Model $record) {
                                $indicators = $record->assessmentIndicators()->with('assessmentScheme.major')->get();
                                $grouped = $indicators->groupBy('assessment_scheme_id');

                                $result = [];
                                foreach ($grouped as $schemeId => $inds) {
                                    $scheme = $inds->first()->assessmentScheme;
                                    $majorName = $scheme->major ? $scheme->major->name : 'Umum';

                                    $result[] = [
                                        'scheme_title' => "Skema: {$majorName} - {$scheme->name}",
                                        // MANTRA BARU: Siapkan data mentah untuk Badge!
                                        'major_name' => $majorName,
                                        'scheme_name' => $scheme->name,
                                        // Data indikator
                                        'indicators_list' => $inds->map(function ($ind) {
                                            return [
                                                'name' => $ind->name,
                                                'is_active' => $ind->is_active,
                                            ];
                                        })->toArray(),
                                    ];
                                }
                                return $result;
                            })
                            ->schema([
                                // CARD UNTUK SETIAP SKEMA
                                Section::make(fn(Get $get): string => 'Detail Skema (' . ($get('scheme_name') ?? 'Unknown') . ')')
                                    ->collapsible()
                                    ->collapsed()
                                    ->icon('heroicon-m-tag')
                                    ->schema([

                                        // KEMBALIKAN BADGE JURUSAN & SKEMA DI SINI!
                                        Grid::make(2)->schema([
                                            TextEntry::make('major_name')
                                                ->label('Jurusan')
                                                ->badge()
                                                ->color('info'),

                                            TextEntry::make('scheme_name')
                                                ->label('Skema Penilaian')
                                                ->weight('bold')
                                                ->color('primary'),
                                        ])->extraAttributes(['class' => 'mb-4']),

                                        // REPEATER DALAM: Tampil daftar indikator
                                        RepeatableEntry::make('indicators_list')
                                            ->hiddenLabel()
                                            ->contained(false)
                                            ->schema([
                                                Grid::make(12)->schema([
                                                    TextEntry::make('name')
                                                        ->hiddenLabel()
                                                        ->icon('heroicon-m-check-circle')
                                                        ->iconColor('primary')
                                                        ->columnSpan(['default' => 12, 'md' => 10]),

                                                    IconEntry::make('is_active')
                                                        ->hiddenLabel()
                                                        ->boolean()
                                                        ->columnSpan(['default' => 12, 'md' => 2]),
                                                ])
                                            ])
                                            ->columns(1)
                                    ])
                            ])
                            ->columns(1)
                    ]),
            ]);
    }
}
