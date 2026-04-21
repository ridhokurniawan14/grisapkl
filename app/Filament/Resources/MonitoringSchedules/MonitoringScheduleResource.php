<?php

namespace App\Filament\Resources\MonitoringSchedules;

use App\Filament\Resources\MonitoringSchedules\Pages\CreateMonitoringSchedule;
use App\Filament\Resources\MonitoringSchedules\Pages\EditMonitoringSchedule;
use App\Filament\Resources\MonitoringSchedules\Pages\ListMonitoringSchedules;
use App\Filament\Resources\MonitoringSchedules\Pages\ViewMonitoringSchedule;
use App\Filament\Resources\MonitoringSchedules\Schemas\MonitoringScheduleForm;
// use App\Filament\Resources\MonitoringSchedules\Schemas\MonitoringScheduleInfolist;
use App\Filament\Resources\MonitoringSchedules\Tables\MonitoringSchedulesTable;
use App\Models\MonitoringSchedule;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MonitoringScheduleResource extends Resource
{
    protected static ?string $model = MonitoringSchedule::class;

    // Ganti icon jadi kalender
    protected static string|BackedEnum|null $navigationIcon = Heroicon::CalendarDateRange;

    // Translasi & Grup Menu (Sesuai idemu, masuk Data Master)
    protected static ?string $navigationLabel = 'Jadwal Monitoring';
    protected static ?string $modelLabel = 'Jadwal Monitoring';
    protected static ?string $pluralModelLabel = 'Jadwal Monitoring';

    protected static string | \UnitEnum | null $navigationGroup = 'Data Master';
    protected static ?int $navigationSort = 8;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return MonitoringScheduleForm::configure($schema);
    }

    // public static function infolist(Schema $schema): Schema
    // {
    //     return MonitoringScheduleInfolist::configure($schema);
    // }

    public static function table(Table $table): Table
    {
        return MonitoringSchedulesTable::configure($table);
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
            'index' => ListMonitoringSchedules::route('/'),
            'create' => CreateMonitoringSchedule::route('/create'),
            // 'view' => ViewMonitoringSchedule::route('/{record}'),
            'edit' => EditMonitoringSchedule::route('/{record}/edit'),
        ];
    }
}
