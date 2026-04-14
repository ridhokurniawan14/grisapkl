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
                TextInput::make('student_id')
                    ->required()
                    ->numeric(),
                TextInput::make('dudika_id')
                    ->required()
                    ->numeric(),
                TextInput::make('teacher_id')
                    ->required()
                    ->numeric(),
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
