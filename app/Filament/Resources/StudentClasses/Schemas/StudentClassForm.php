<?php

namespace App\Filament\Resources\StudentClasses\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class StudentClassForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('major_id')
                    ->required()
                    ->numeric(),
                TextInput::make('name')
                    ->required(),
            ]);
    }
}
