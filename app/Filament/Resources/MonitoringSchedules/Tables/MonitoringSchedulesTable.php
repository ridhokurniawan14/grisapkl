<?php

namespace App\Filament\Resources\MonitoringSchedules\Tables;

use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Collection;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class MonitoringSchedulesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('start_date', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Jadwal')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('start_date')
                    ->date('d M Y')
                    ->label('Tgl Buka')
                    ->badge()
                    ->color('success'),

                TextColumn::make('end_date')
                    ->date('d M Y')
                    ->label('Tgl Tutup')
                    ->badge()
                    ->color('danger'),

                // ==========================================
                // REVISI 3: TOGGLE COLUMN MEWAH DENGAN ICON
                // ==========================================
                ToggleColumn::make('is_active')
                    ->label('Aktif')
                    ->onColor('success')
                    ->offColor('danger')
                    ->onIcon('heroicon-m-check') // Icon Centang saat nyala
                    ->offIcon('heroicon-m-x-mark') // Icon Silang saat mati
                    ->tooltip('Geser untuk membuka/menutup akses jadwal monitoring'),

                // ==========================================
                // REVISI 4: KOLOM SUNNAH (Toggleable Hidden)
                // ==========================================
                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d M Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Diperbarui Pada')
                    ->dateTime('d M Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),

                    // ==========================================
                    // REVISI 2: BULK AKTIF & NON-AKTIF MASSAL
                    // ==========================================
                    BulkAction::make('activate_schedules')
                        ->label('Aktifkan Terpilih')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $records->each->update(['is_active' => true]);
                            Notification::make()->title('Jadwal terpilih telah diaktifkan')->success()->send();
                        }),

                    BulkAction::make('deactivate_schedules')
                        ->label('Non-Aktifkan Terpilih')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $records->each->update(['is_active' => false]);
                            Notification::make()->title('Jadwal terpilih telah dinonaktifkan')->danger()->send();
                        }),
                ]),
            ]);
    }
}
