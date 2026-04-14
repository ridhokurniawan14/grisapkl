<?php

namespace App\Filament\Resources\Monitorings\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class MonitoringInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('teacher_id')
                    ->numeric(),
                TextEntry::make('dudika_id')
                    ->numeric(),
                TextEntry::make('date')
                    ->date(),
                TextEntry::make('time')
                    ->time(),
                TextEntry::make('activity')
                    ->columnSpanFull(),
                TextEntry::make('photo_path')
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
