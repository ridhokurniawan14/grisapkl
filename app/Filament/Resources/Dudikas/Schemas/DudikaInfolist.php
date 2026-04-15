<?php

namespace App\Filament\Resources\Dudikas\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class DudikaInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->label('Nama Instansi / DUDIKA'),
                TextEntry::make('address')
                    ->label('Alamat Lengkap')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('head_name')
                    ->label('Nama Pimpinan / Direktur')
                    ->placeholder('-'),
                TextEntry::make('head_nip')
                    ->label('NIP / NIK Pimpinan')
                    ->placeholder('-'),
                TextEntry::make('supervisor_name')
                    ->label('Nama Pembimbing DUDIKA')
                    ->placeholder('-'),
                TextEntry::make('supervisor_nip')
                    ->label('NIP / NIK Pembimbing')
                    ->placeholder('-'),
                TextEntry::make('supervisor_phone')
                    ->label('No. HP / WhatsApp Pembimbing')
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
