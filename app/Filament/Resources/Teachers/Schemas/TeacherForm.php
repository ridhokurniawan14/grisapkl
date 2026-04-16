<?php

namespace App\Filament\Resources\Teachers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Saade\FilamentAutograph\Forms\Components\SignaturePad;

class TeacherForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2) // Bagi dua kolom biar rapi    
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Lengkap Guru')
                            ->placeholder('Contoh: Budi Santoso, S.Pd., M.T.')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('title')
                            ->label('Gelar / Title')
                            ->placeholder('Contoh: S.Pd., M.T.')
                            ->helperText('Gelar akan muncul di belakang nama untuk keperluan TTD.'),
                    ]),
                TextInput::make('nip')
                    ->label('NIP (Nomor Induk Pegawai)')
                    ->placeholder('Kosongkan jika bukan PNS/PPPK')
                    ->maxLength(255),

                TextInput::make('phone')
                    ->label('No. HP / WhatsApp')
                    ->placeholder('Contoh: 081234567890')
                    ->tel()
                    ->maxLength(255),

                TextInput::make('subject')
                    ->label('Mata Pelajaran (Opsional)')
                    ->placeholder('Contoh: Produktif TKJ')
                    ->maxLength(255),

                SignaturePad::make('signature_path')
                    ->label('Tanda Tangan Digital')
                    ->dotSize(2.0)
                    ->lineMinWidth(1.0)
                    ->lineMaxWidth(2.5)
                    ->exportPenColor('#000000') // Tinta hitam
                    ->backgroundColor('#ffffff') // <-- Background putih paksa biar aman di dark mode
                    ->helperText('Silakan tanda tangan langsung pada kotak di atas.'),
            ]);
    }
}
