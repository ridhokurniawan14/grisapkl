<?php

namespace App\Filament\Resources\ActivityLogs\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ActivityLogInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Aktivitas')
                    ->schema([
                        TextEntry::make('causer.name')->label('Pelaku (User)'),
                        TextEntry::make('description')->label('Aksi')
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'created' => 'success',
                                'updated' => 'warning',
                                'deleted' => 'danger',
                                default   => 'gray',
                            }),
                        TextEntry::make('subject_type')->label('Model / Tabel')
                            ->formatStateUsing(fn($state) => class_basename($state)),
                        TextEntry::make('created_at')->label('Waktu Eksekusi')
                            ->dateTime('d M Y, H:i:s'),
                    ])->columns(2),

                Section::make('Detail Perubahan Data')
                    ->description('Perbandingan nilai sebelum dan sesudah diubah. (Hanya muncul pada aksi Update)')
                    ->schema([
                        KeyValueEntry::make('old_values')
                            ->label('Data Lama (Sebelum Diubah)')
                            ->getStateUsing(fn($record) => self::extractProperties($record, 'old')),

                        KeyValueEntry::make('new_values')
                            ->label('Data Baru (Sesudah Diubah)')
                            ->getStateUsing(fn($record) => self::extractProperties($record, 'attributes')),
                    ])->columns(2),
            ]);
    }

    private static function extractProperties($record, string $key): array
    {
        $raw = $record->attribute_changes; // ← pindah ke sini

        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            if (is_string($decoded)) {
                $decoded = json_decode($decoded, true); // handle double-encoded
            }
            $data = $decoded ?? [];
        } elseif ($raw instanceof \Illuminate\Support\Collection) {
            $data = $raw->toArray();
        } else {
            $data = (array) $raw;
        }

        $values = $data[$key] ?? [];

        return collect($values)
            ->map(fn($v) => is_array($v) || is_object($v) ? json_encode($v) : (string) $v)
            ->toArray();
    }
}
