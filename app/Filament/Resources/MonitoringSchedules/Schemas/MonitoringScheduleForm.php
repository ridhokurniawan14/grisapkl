<?php

namespace App\Filament\Resources\MonitoringSchedules\Schemas;

use App\Models\MonitoringSchedule;
use Closure;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class MonitoringScheduleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Pengaturan Jadwal Monitoring')
                ->columnSpanFull()
                ->schema([

                    TextInput::make('name')
                        ->required()
                        ->label('Nama Jadwal')
                        ->placeholder('Contoh: Monitoring Tahap 1 / Bulan 1')
                        ->columnSpanFull(),

                    DatePicker::make('start_date')
                        ->required()
                        ->label('Tanggal Buka (Mulai)')
                        ->live(),

                    DatePicker::make('end_date')
                        ->required()
                        ->label('Tanggal Tutup (Batas Akhir)')
                        ->afterOrEqual('start_date')
                        ->live()
                        ->rules([
                            fn(Get $get, ?MonitoringSchedule $record): Closure =>
                            function (string $attribute, $value, Closure $fail) use ($get, $record) {

                                $startDate = $get('start_date');
                                $endDate   = $get('end_date');

                                if (!$startDate || !$endDate) {
                                    return;
                                }

                                $query = MonitoringSchedule::query()

                                    // saat edit jangan cek dirinya sendiri
                                    ->when(
                                        $record,
                                        fn($q) => $q->where('id', '!=', $record->id)
                                    )

                                    // CEK APAKAH ADA IRISAN TANGGAL
                                    ->where(function ($q) use ($startDate, $endDate) {
                                        $q->where('start_date', '<=', $endDate)
                                            ->where('end_date', '>=', $startDate);
                                    });

                                if ($query->exists()) {
                                    $fail('Rentang tanggal bentrok dengan jadwal lain.');
                                }
                            }
                        ]),

                    Toggle::make('is_active')
                        ->default(true)
                        ->label('Status Aktif')
                        ->onColor('success')
                        ->offColor('danger')
                        ->helperText('Matikan jika ingin menutup paksa akses guru ke jadwal ini.')
                        ->columnSpanFull(),

                ])
                ->columns(2)
        ]);
    }
}
