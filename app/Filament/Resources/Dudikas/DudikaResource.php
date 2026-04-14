<?php

namespace App\Filament\Resources\Dudikas;

use App\Filament\Resources\Dudikas\Pages\CreateDudika;
use App\Filament\Resources\Dudikas\Pages\EditDudika;
use App\Filament\Resources\Dudikas\Pages\ListDudikas;
use App\Filament\Resources\Dudikas\Pages\ViewDudika;
use App\Filament\Resources\Dudikas\Schemas\DudikaForm;
use App\Filament\Resources\Dudikas\Schemas\DudikaInfolist;
use App\Filament\Resources\Dudikas\Tables\DudikasTable;
use App\Models\Dudika;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DudikaResource extends Resource
{
    protected static ?string $model = Dudika::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return DudikaForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DudikaInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DudikasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDudikas::route('/'),
            'create' => CreateDudika::route('/create'),
            'view' => ViewDudika::route('/{record}'),
            'edit' => EditDudika::route('/{record}/edit'),
        ];
    }
}
