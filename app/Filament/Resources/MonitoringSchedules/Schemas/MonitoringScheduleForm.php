<?php

namespace App\Filament\Resources\MonitoringSchedules\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
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
                        ->label('Tanggal Buka (Mulai)'),

                    DatePicker::make('end_date')
                        ->required()
                        ->label('Tanggal Tutup (Batas Akhir)'),

                    Toggle::make('is_active')
                        ->default(true)
                        ->label('Status Aktif')
                        ->onColor('success')
                        ->offColor('danger')
                        ->helperText('Matikan jika ingin menutup paksa akses guru ke jadwal ini.')
                        ->columnSpanFull(),
                ])->columns(2)
        ]);
    }
}
