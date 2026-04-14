<?php

namespace App\Filament\Resources\Monitorings\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class MonitoringForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('teacher_id')
                    ->required()
                    ->numeric(),
                TextInput::make('dudika_id')
                    ->required()
                    ->numeric(),
                DatePicker::make('date')
                    ->required(),
                TimePicker::make('time')
                    ->required(),
                Textarea::make('activity')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('photo_path'),
            ]);
    }
}
