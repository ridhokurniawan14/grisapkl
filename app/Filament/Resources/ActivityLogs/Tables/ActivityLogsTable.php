<?php

namespace App\Filament\Resources\ActivityLogs\Tables;

use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Spatie\Activitylog\Models\Activity;
use Filament\Notifications\Notification;

class ActivityLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->poll('5s')
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y, H:i:s')
                    ->sortable(),
                TextColumn::make('causer.name')
                    ->label('Pelaku (User)')
                    ->searchable(),
                TextColumn::make('description')
                    ->label('Aktivitas')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('subject_type')
                    ->label('Tabel / Model')
                    ->formatStateUsing(fn($state) => class_basename($state)),
            ])
            ->actions([
                ViewAction::make(),
            ])
            // ==========================================
            // MANTRA SAKTI: TOMBOL BERSIHKAN LOG
            // ==========================================
            ->headerActions([
                Action::make('bersihkan_log')
                    ->label('Bersihkan Log')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Riwayat Log?')
                    ->modalDescription('Tindakan ini akan menghapus semua log aktivitas dan hanya menyisakan 10 data terbaru. Apakah Anda yakin?')
                    ->modalSubmitActionLabel('Ya, Bersihkan!')
                    ->action(function () {
                        // Ambil 10 ID terbaru
                        $latestIds = Activity::latest('id')->limit(10)->pluck('id')->toArray();

                        // Hapus semua log yang ID-nya TIDAK ADA di 10 ID terbaru tersebut
                        Activity::whereNotIn('id', $latestIds)->delete();

                        Notification::make()
                            ->title('Log Berhasil Dibersihkan')
                            ->body('Hanya menyisakan 10 aktivitas terbaru.')
                            ->success()
                            ->send();
                    }),
            ]);
    }
}
