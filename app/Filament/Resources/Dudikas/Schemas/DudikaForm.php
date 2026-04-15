<?php

namespace App\Filament\Resources\Dudikas\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DudikaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Kita bungkus pakai Section biar UI-nya kotak-kotak rapi
                Section::make('Informasi Instansi/Perusahaan')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Instansi / DUDIKA')
                            ->placeholder('Contoh: PT. Mencari Cinta Sejati')
                            ->autocomplete('off')
                            ->required(),
                        Textarea::make('address')
                            ->label('Alamat Lengkap')
                            ->placeholder('Masukkan alamat lengkap beserta kota...')
                            ->autocomplete('off')
                            ->columnSpanFull(),
                    ])->columns(1),

                Section::make('Data Pimpinan')
                    ->schema([
                        TextInput::make('head_name')
                            ->label('Nama Pimpinan / Direktur')
                            ->placeholder('Contoh: Budi Santoso, S.T.')
                            ->autocomplete('off'),
                        TextInput::make('head_nip')
                            ->label('NIP / NIK Pimpinan')
                            ->placeholder('Opsional (jika ada)')
                            ->autocomplete('off'),
                    ])->columns(2),

                Section::make('Data Pembimbing Lapangan')
                    ->schema([
                        TextInput::make('supervisor_name')
                            ->label('Nama Pembimbing DUDIKA')
                            ->helperText('Orang yang akan membimbing dan menilai siswa di lokasi.')
                            ->autocomplete('off'),
                        TextInput::make('supervisor_nip')
                            ->label('NIP / NIK Pembimbing')
                            ->autocomplete('off'),
                        TextInput::make('supervisor_phone')
                            ->label('No. HP / WhatsApp Pembimbing')
                            ->tel()
                            ->placeholder('Contoh: 081234567890')
                            ->autocomplete('off'),
                    ])->columns(2),
            ]);
    }
}
