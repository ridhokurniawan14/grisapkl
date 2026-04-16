<?php

namespace App\Filament\Resources\Teachers\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class TeacherInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->label('Nama Lengkap'),
                TextEntry::make('title')
                    ->label('Gelar')
                    ->placeholder('-'),
                TextEntry::make('nip')
                    ->label('NIP')
                    ->placeholder('-'),
                TextEntry::make('phone')
                    ->label('No. HP')
                    ->placeholder('-'),
                TextEntry::make('subject')
                    ->label('Mata Pelajaran')
                    ->placeholder('-'),

                // Merender TTD Base64 menjadi Gambar dengan background putih
                TextEntry::make('signature_path')
                    ->label('Tanda Tangan')
                    ->formatStateUsing(fn($state) => $state ? new HtmlString('<img src="' . $state . '" style="height: 100px; background-color: #ffffff; border-radius: 8px; padding: 5px; border: 1px solid #ccc;" />') : '-'),

                TextEntry::make('created_at')
                    ->label('Tanggal Terdaftar')
                    ->dateTime(),
            ]);
    }
}
