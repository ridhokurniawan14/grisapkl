<?php

namespace App\Filament\Resources\Journals\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class JournalInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('pkl_placement_id')
                    ->numeric(),
                TextEntry::make('date')
                    ->date(),
                TextEntry::make('time')
                    ->time(),
                TextEntry::make('attend_status')
                    ->badge(),
                TextEntry::make('activity')
                    ->columnSpanFull(),
                TextEntry::make('photo_path')
                    ->placeholder('-'),
                IconEntry::make('is_valid')
                    ->boolean(),
                TextEntry::make('revision_note')
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
