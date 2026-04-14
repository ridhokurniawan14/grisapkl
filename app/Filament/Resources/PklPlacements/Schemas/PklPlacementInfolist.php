<?php

namespace App\Filament\Resources\PklPlacements\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PklPlacementInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('student_id')
                    ->numeric(),
                TextEntry::make('dudika_id')
                    ->numeric(),
                TextEntry::make('teacher_id')
                    ->numeric(),
                TextEntry::make('start_date')
                    ->date(),
                TextEntry::make('end_date')
                    ->date(),
                TextEntry::make('pkl_field')
                    ->placeholder('-'),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('pengesah_ks_nama')
                    ->placeholder('-'),
                TextEntry::make('pengesah_ks_nip')
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
