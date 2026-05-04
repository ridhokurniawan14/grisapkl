<?php

namespace App\Filament\Resources\ActivityLogs;

use App\Filament\Resources\ActivityLogs\Pages\CreateActivityLog;
use App\Filament\Resources\ActivityLogs\Pages\EditActivityLog;
use App\Filament\Resources\ActivityLogs\Pages\ListActivityLogs;
use App\Filament\Resources\ActivityLogs\Pages\ViewActivityLog;
use App\Filament\Resources\ActivityLogs\Schemas\ActivityLogForm;
use App\Filament\Resources\ActivityLogs\Schemas\ActivityLogInfolist;
use App\Filament\Resources\ActivityLogs\Tables\ActivityLogsTable;
use BackedEnum;
use Filament\Resources\Resource;
use Spatie\Activitylog\Models\Activity;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ActivityLogResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Clock;
    protected static ?string $modelLabel = 'Log Aktivitas';
    protected static ?string $navigationLabel = 'Log Aktivitas';
    protected static ?string $pluralModelLabel = 'Log Aktivitas';

    protected static string | \UnitEnum | null $navigationGroup = 'Sistem';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return ActivityLogForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ActivityLogInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ActivityLogsTable::configure($table);
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
            'index' => ListActivityLogs::route('/'),
            'create' => CreateActivityLog::route('/create'),
            'view' => ViewActivityLog::route('/{record}'),
            'edit' => EditActivityLog::route('/{record}/edit'),
        ];
    }
}
