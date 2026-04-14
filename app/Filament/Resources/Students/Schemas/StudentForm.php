<?php

namespace App\Filament\Resources\Students\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class StudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                TextInput::make('student_class_id')
                    ->required()
                    ->numeric(),
                TextInput::make('nis')
                    ->required(),
                Select::make('gender')
                    ->options(['L' => 'L', 'P' => 'P'])
                    ->required(),
                TextInput::make('birth_place'),
                DatePicker::make('birth_date'),
                TextInput::make('religion'),
                Textarea::make('address')
                    ->columnSpanFull(),
                TextInput::make('father_name'),
                TextInput::make('mother_name'),
                TextInput::make('father_job'),
                TextInput::make('mother_job'),
                Textarea::make('parent_address')
                    ->columnSpanFull(),
                TextInput::make('parent_phone')
                    ->tel(),
            ]);
    }
}
