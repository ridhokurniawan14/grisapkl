<?php

namespace App\Filament\Resources\StudentClasses\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class StudentClassForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('major_id')
                    ->relationship('major', 'name')
                    ->label('Konsentrasi Keahlian')
                    ->helperText('Pilih konsentrasi keahlian yang menaungi kelas ini.')
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('name')
                    ->label('Nama Kelas')
                    ->placeholder('Contoh: XII TKJ 1')
                    ->helperText('Gunakan format penulisan nama kelas yang standar (Angka Romawi - Konsentrasi Keahlian - Nomor).')
                    ->required()
                    ->maxLength(255),
            ]);
    }
}
