<?php

namespace App\Filament\Resources\Dudikas\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class DudikaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Textarea::make('address')
                    ->columnSpanFull(),
                TextInput::make('head_name'),
                TextInput::make('head_nip'),
                TextInput::make('supervisor_name'),
                TextInput::make('supervisor_nip'),
                TextInput::make('supervisor_phone')
                    ->tel(),
            ]);
    }
}
