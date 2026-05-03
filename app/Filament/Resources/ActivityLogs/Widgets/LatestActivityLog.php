<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Spatie\Activitylog\Models\Activity;

class LatestActivityLog extends BaseWidget
{
    protected static ?int $sort = 5; // Taruh di bawah Data Master
    protected int | string | array $columnSpan = '1';
    protected static ?string $heading = '10 Aktivitas Log Terbaru (Live)';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                // Hanya ambil 10 data paling baru
                Activity::query()->latest()->limit(10)
            )
            // ==========================================
            // MANTRA SAKTI: AUTO REFRESH TIAP 5 DETIK!
            // ==========================================
            ->poll('5s')
            ->paginated(false) // Matikan paginasi karena cuma butuh 10 data
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y, H:i:s')
                    ->sortable(),
                Tables\Columns\TextColumn::make('causer.name')
                    ->label('Pelaku')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Aksi')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('subject_type')
                    ->label('Tabel / Model')
                    ->formatStateUsing(fn($state) => class_basename($state)),
            ]);
    }
}
