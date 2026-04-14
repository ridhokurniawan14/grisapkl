<?php

namespace App\Filament\Resources\Monitorings;

use App\Filament\Resources\Monitorings\Pages\CreateMonitoring;
use App\Filament\Resources\Monitorings\Pages\EditMonitoring;
use App\Filament\Resources\Monitorings\Pages\ListMonitorings;
use App\Filament\Resources\Monitorings\Pages\ViewMonitoring;
use App\Filament\Resources\Monitorings\Schemas\MonitoringForm;
use App\Filament\Resources\Monitorings\Schemas\MonitoringInfolist;
use App\Filament\Resources\Monitorings\Tables\MonitoringsTable;
use App\Models\Monitoring;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MonitoringResource extends Resource
{
    protected static ?string $model = Monitoring::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return MonitoringForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MonitoringInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MonitoringsTable::configure($table);
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
            'index' => ListMonitorings::route('/'),
            'create' => CreateMonitoring::route('/create'),
            'view' => ViewMonitoring::route('/{record}'),
            'edit' => EditMonitoring::route('/{record}/edit'),
        ];
    }
}
