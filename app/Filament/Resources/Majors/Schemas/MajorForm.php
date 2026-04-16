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
                TextInput::make('program_keahlian')
                    ->label('Program Keahlian')
                    ->placeholder('Contoh: Teknik Jaringan Komputer dan Telekomunikasi')
                    ->helperText('Masukkan nama lengkap Program Keahlian secara mendetail.')
                    ->required()
                    ->maxLength(255),

                TextInput::make('name')
                    ->label('Konsentrasi Keahlian')
                    ->placeholder('Contoh: Teknik Komputer dan Jaringan')
                    ->helperText('Masukkan nama lengkap Konsentrasi Keahlian secara mendetail.')
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
