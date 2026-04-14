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
                TextEntry::make('name'),
                TextEntry::make('address')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('head_name')
                    ->placeholder('-'),
                TextEntry::make('head_nip')
                    ->placeholder('-'),
                TextEntry::make('supervisor_name')
                    ->placeholder('-'),
                TextEntry::make('supervisor_nip')
                    ->placeholder('-'),
                TextEntry::make('supervisor_phone')
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
