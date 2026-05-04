<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Akun Pengguna')
                ->description('Kelola data utama dan hak akses kredensial pengguna.')
                ->columnSpanFull()
                ->schema([
                    TextInput::make('name')
                        ->label('Nama Lengkap')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('email')
                        ->label('Alamat Email')
                        ->email()
                        ->unique(ignoreRecord: true)
                        ->required()
                        ->maxLength(255),

                    TextInput::make('password')
                        ->label('Kata Sandi')
                        ->password()
                        ->revealable()
                        ->dehydrateStateUsing(fn($state) => Hash::make($state))
                        ->dehydrated(fn($state) => filled($state))
                        ->required(fn(string $context): bool => $context === 'create') // Hanya wajib saat Create
                        ->maxLength(255)
                        ->helperText('Kosongkan jika tidak ingin mengubah kata sandi saat mengedit data.'),

                    // MANTRA SAKTI: Integrasi otomatis dengan Filament Shield!
                    Select::make('roles')
                        ->label('Hak Akses (Role)')
                        ->relationship('roles', 'name')
                        ->multiple()
                        ->preload()
                        ->searchable()
                        ->required(),
                ])->columns(2),
        ]);
    }
}
