<?php

namespace App\Filament\Resources\AssessmentSchemes\Schemas;

use App\Models\Major;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AssessmentSchemeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Informasi Skema Penilaian')
                    ->description('Tentukan nama skema penilaian berdasarkan kategori pekerjaan di DUDIKA.')
                    ->columnSpanFull()
                    ->schema([
                        Select::make('major_id')
                            ->label('Jurusan / Kompetensi Keahlian')
                            ->options(Major::pluck('name', 'id'))
                            ->searchable()
                            ->required()
                            ->columnSpanFull(),

                        TextInput::make('name')
                            ->label('Nama Skema')
                            ->placeholder('Contoh: Teknisi Jaringan (ISP) atau Service Laptop')
                            ->required()
                            ->columnSpanFull(),

                        Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->default(true)
                            ->onColor('success')
                            ->offColor('danger'),
                    ]),
            ]);
    }
}
