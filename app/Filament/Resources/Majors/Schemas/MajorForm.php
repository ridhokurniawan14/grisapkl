<?php

namespace App\Filament\Resources\Majors\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MajorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Jurusan / Kompetensi Keahlian')
                    ->placeholder('Contoh: Teknik Komputer dan Jaringan')
                    ->helperText('Masukkan nama lengkap jurusan secara mendetail.')
                    ->required()
                    ->autocomplete('off')
                    ->maxLength(255),

                TextInput::make('abbreviation')
                    ->label('Singkatan Jurusan')
                    ->placeholder('Contoh: TKJ')
                    ->helperText('Singkatan ini berguna untuk mempermudah pencarian data.')
                    ->autocomplete('off')
                    ->maxLength(255),
            ]);
    }
}
