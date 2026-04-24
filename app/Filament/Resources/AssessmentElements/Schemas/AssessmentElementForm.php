<?php

namespace App\Filament\Resources\AssessmentElements\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use App\Models\AssessmentScheme; // Import Model Skema
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Components\Section;

class AssessmentElementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            // =========================================================
            // CARD 1 (ATAS): DATA ELEMEN PENILAIAN
            // =========================================================
            Section::make('Data Elemen Penilaian')
                ->description('Input informasi utama elemen dan tujuan pembelajaran.')
                ->icon('heroicon-m-document-text')
                ->collapsible()
                ->columnSpanFull()
                ->schema([
                    TextInput::make('name')
                        ->label('Nama Elemen Utama')
                        ->placeholder('Contoh: Internalisasi dan Penerapan Soft Skills')
                        ->required()
                        ->columnSpanFull(),

                    TextInput::make('tp_name')
                        ->label('Tujuan Pembelajaran (TP)')
                        ->placeholder('Contoh: Menerapkan soft skills yang dibutuhkan...')
                        ->required()
                        ->columnSpanFull(),
                ]),

            // =========================================================
            // CARD 2 (BAWAH): DAFTAR INDIKATOR BERDASARKAN SKEMA
            // =========================================================
            Section::make('Daftar Indikator Penilaian per Skema')
                ->description('Pilih skema satu kali, lalu tambahkan banyak indikator di dalamnya.')
                ->icon('heroicon-m-list-bullet')
                ->collapsible()
                ->columnSpanFull()
                ->schema([
                    // REPEATER LUAR: Untuk memilih Skema
                    Repeater::make('scheme_groups') // <--- PERHATIKAN: Tidak pakai ->relationship() lagi
                        ->label('')
                        ->addActionLabel('Tambah Kelompok Skema')
                        ->schema([
                            Select::make('assessment_scheme_id')
                                ->label('Pilih Skema Penilaian')
                                ->options(function () {
                                    return AssessmentScheme::with('major')->get()->mapWithKeys(function ($scheme) {
                                        return [$scheme->id => "({$scheme->major->name}) - {$scheme->name}"];
                                    });
                                })
                                ->searchable()
                                ->required()
                                ->live()
                                ->columnSpanFull(),

                            // REPEATER DALAM: Hanya untuk teks Indikator (Kotak Hijau di gambarmu)
                            Repeater::make('indicators')
                                ->label('Detail Indikator untuk Skema di atas')
                                ->addActionLabel('Tambah Indikator')
                                ->cloneable() // Tombol copas yang kamu minta ada di sini bro!
                                ->schema([
                                    Hidden::make('id'),
                                    TextInput::make('name')
                                        ->label('Detail Indikator / Kriteria Penilaian')
                                        ->placeholder('Contoh: Melaksanakan komunikasi di tempat kerja...')
                                        ->required()
                                        ->columnSpan(['default' => 12, 'md' => 10]),

                                    Toggle::make('is_active')
                                        ->label('Aktif')
                                        ->default(true)
                                        ->inline(false)
                                        ->onColor('success')
                                        ->offColor('danger')
                                        ->columnSpan(['default' => 12, 'md' => 2]),
                                ])
                                ->columns(12)
                                ->columnSpanFull()
                                ->itemLabel(fn(array $state): ?string => $state['name'] ?? 'Indikator Baru')
                                ->collapsed(),
                        ])
                        ->itemLabel(
                            fn(array $state): ?string =>
                            isset($state['assessment_scheme_id'])
                                ? "Kelompok Skema: " . (AssessmentScheme::find($state['assessment_scheme_id'])?->name ?? '...')
                                : 'Kelompok Skema Baru'
                        )
                        ->collapsed()
                        ->collapsible(),
                ]),
        ]);
    }
}
