<?php

namespace App\Filament\Resources\AcademicYears\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AcademicYearForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Tahun Ajaran') // Label atas
                    ->placeholder('Contoh: 2025/2026') // Tulisan abu-abu di dalam form
                    ->helperText('Masukkan rentang tahun ajaran yang berlaku saat ini.') // Catatan kecil di bawah form
                    ->required()
                    ->maxLength(255),

                Toggle::make('is_active')
                    ->label('Aktif')
                    ->unique(
                        modifyRuleUsing: fn($rule) => $rule->where('is_active', true),
                        ignoreRecord: true
                    )
                    ->helperText('Hanya boleh satu tahun ajaran yang aktif')
            ]);
    }
}
