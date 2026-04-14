<?php

namespace App\Filament\Resources\Students\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class StudentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user_id')
                    ->numeric(),
                TextEntry::make('student_class_id')
                    ->numeric(),
                TextEntry::make('nis'),
                TextEntry::make('gender')
                    ->badge(),
                TextEntry::make('birth_place')
                    ->placeholder('-'),
                TextEntry::make('birth_date')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('religion')
                    ->placeholder('-'),
                TextEntry::make('address')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('father_name')
                    ->placeholder('-'),
                TextEntry::make('mother_name')
                    ->placeholder('-'),
                TextEntry::make('father_job')
                    ->placeholder('-'),
                TextEntry::make('mother_job')
                    ->placeholder('-'),
                TextEntry::make('parent_address')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('parent_phone')
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
