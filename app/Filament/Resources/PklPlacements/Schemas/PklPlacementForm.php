<?php

namespace App\Filament\Resources\PklPlacements\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PklPlacementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('student_id')
                    ->relationship('student', 'nis')
                    ->getOptionLabelFromRecordUsing(fn($record) => "{$record->nis} - " . ($record->user ? $record->user->name : 'No Name'))
                    ->label('Siswa')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('dudika_id')
                    ->relationship('dudika', 'name')
                    ->label('Tempat DUDIKA')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('teacher_id')
                    ->relationship('teacher', 'id')
                    ->getOptionLabelFromRecordUsing(fn($record) => $record->user ? $record->user->name : 'No Name')
                    ->label('Guru Pembimbing')
                    ->searchable()
                    ->preload()
                    ->required(),
                DatePicker::make('start_date')
                    ->required(),
                DatePicker::make('end_date')
                    ->required(),
                TextInput::make('pkl_field'),
                Select::make('status')
                    ->options(['Aktif' => 'Aktif', 'Ditarik' => 'Ditarik'])
                    ->default('Aktif')
                    ->required(),
                TextInput::make('pengesah_ks_nama'),
                TextInput::make('pengesah_ks_nip'),
            ]);
    }
}
