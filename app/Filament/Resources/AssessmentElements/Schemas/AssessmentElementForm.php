<?php

namespace App\Filament\Resources\AssessmentElements\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use App\Models\Major;
use Filament\Schemas\Components\Section;

class AssessmentElementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                // =========================================================
                // CARD 1 (ATAS): DATA ELEMEN PENILAIAN
                // =========================================================
                Section::make('Data Elemen Penilaian')
                    ->columnSpanFull()
                    ->description('Input informasi utama elemen dan tujuan pembelajaran.')
                    ->icon('heroicon-m-document-text')
                    ->collapsible()
                    ->columns(2) // <-- ini kuncinya bro
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Elemen Utama')
                            ->placeholder('Contoh: Internalisasi dan Penerapan Soft Skills')
                            ->required(),

                        TextInput::make('tp_name')
                            ->label('Tujuan Pembelajaran (TP)')
                            ->placeholder('Contoh: Menerapkan soft skills yang dibutuhkan...')
                            ->required(),
                    ]),

                // =========================================================
                // CARD 2 (BAWAH): DAFTAR INDIKATOR PENILAIAN PER JURUSAN
                // =========================================================
                Section::make('Daftar Indikator Penilaian per Jurusan')
                    ->description('Kelola detail kriteria penilaian untuk setiap jurusan di sini.')
                    ->icon('heroicon-m-list-bullet')
                    ->columnSpanFull()
                    ->collapsible()
                    ->schema([
                        Repeater::make('assessmentIndicators')
                            ->relationship()
                            ->hiddenLabel()
                            ->addActionLabel('Tambah Indikator Baru')
                            ->cloneable()
                            ->schema([
                                Select::make('major_id')
                                    ->label('Pilih Jurusan')
                                    ->options(Major::pluck('name', 'id'))
                                    ->searchable()
                                    ->required()
                                    ->live() // Biar judul bar langsung berubah saat jurusan dipilih
                                    ->columnSpan(['default' => 12, 'md' => 4]),

                                TextInput::make('name')
                                    ->label('Detail Indikator / Kriteria Penilaian')
                                    ->placeholder('Contoh: Melaksanakan komunikasi di tempat kerja...')
                                    ->required()
                                    ->columnSpan(['default' => 12, 'md' => 6]),

                                Toggle::make('is_active')
                                    ->label('Aktif')
                                    ->default(true)
                                    ->onColor('success')
                                    ->inline(false)
                                    ->offColor('danger')
                                    ->columnSpan(['default' => 12, 'md' => 2]),
                            ])
                            ->columns(12)
                            // MANTRA DYNAMIC LABEL: Menampilkan Nama Jurusan di Bar Judul
                            ->itemLabel(
                                fn(array $state): ?string =>
                                isset($state['major_id'])
                                    ? "Indikator Jurusan " . (Major::find($state['major_id'])?->name ?? '...')
                                    : 'Indikator Baru'
                            )
                            ->collapsed() // Otomatis terlipat saat diedit biar tidak kepanjangan
                            ->collapsible()
                            ->reorderableWithButtons(),
                    ]), // <--- TUTUP SCHEMA CARD 2 DI SINI
            ]);
    }
}
