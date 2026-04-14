<?php

namespace App\Filament\Resources\PklPlacements;

use App\Filament\Resources\PklPlacements\Pages\CreatePklPlacement;
use App\Filament\Resources\PklPlacements\Pages\EditPklPlacement;
use App\Filament\Resources\PklPlacements\Pages\ListPklPlacements;
use App\Filament\Resources\PklPlacements\Pages\ViewPklPlacement;
use App\Filament\Resources\PklPlacements\Schemas\PklPlacementForm;
use App\Filament\Resources\PklPlacements\Schemas\PklPlacementInfolist;
use App\Filament\Resources\PklPlacements\Tables\PklPlacementsTable;
use App\Models\PklPlacement;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PklPlacementResource extends Resource
{
    protected static ?string $model = PklPlacement::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return PklPlacementForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PklPlacementInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PklPlacementsTable::configure($table);
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
            'index' => ListPklPlacements::route('/'),
            'create' => CreatePklPlacement::route('/create'),
            'view' => ViewPklPlacement::route('/{record}'),
            'edit' => EditPklPlacement::route('/{record}/edit'),
        ];
    }
}
