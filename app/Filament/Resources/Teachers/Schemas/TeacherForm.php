<?php

namespace App\Filament\Resources\Teachers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TeacherForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                TextInput::make('nip'),
                TextInput::make('phone')
                    ->tel(),
                TextInput::make('subject'),
                TextInput::make('signature_path'),
            ]);
    }
}
