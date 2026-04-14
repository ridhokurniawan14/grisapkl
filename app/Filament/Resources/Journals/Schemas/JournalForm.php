<?php

namespace App\Filament\Resources\Journals\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class JournalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('pkl_placement_id')
                    ->required()
                    ->numeric(),
                DatePicker::make('date')
                    ->required(),
                TimePicker::make('time')
                    ->required(),
                Select::make('attend_status')
                    ->options(['Hadir' => 'Hadir', 'Sakit' => 'Sakit', 'Izin' => 'Izin'])
                    ->default('Hadir')
                    ->required(),
                Textarea::make('activity')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('photo_path'),
                Toggle::make('is_valid')
                    ->required(),
                TextInput::make('revision_note'),
            ]);
    }
}
