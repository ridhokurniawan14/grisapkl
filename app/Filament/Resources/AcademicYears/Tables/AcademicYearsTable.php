<?php

namespace App\Filament\Resources\AcademicYears\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;   // Tambahkan ini
use Filament\Tables\Table;
use App\Models\AcademicYear;                // Tambahkan ini

class AcademicYearsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Tahun Ajaran')
                    ->searchable(),

                ToggleColumn::make('is_active')
                    ->label('Aktif?')
                    ->onColor('success')
                    ->offColor('danger')
                    ->onIcon('heroicon-m-check')
                    ->offIcon('heroicon-m-x-mark')
                    ->tooltip('Geser untuk mengaktifkan tahun ajaran ini (hanya boleh 1 yang aktif)')
                    ->beforeStateUpdated(function (AcademicYear $record, $state) {
                        // Kalau user mau mengaktifkan (state = true), matiin semua yang lain
                        if ($state === true) {
                            AcademicYear::where('id', '!=', $record->id)
                                ->update(['is_active' => false]);
                        }
                    }),

                TextColumn::make('created_at')
                    ->label('Dibuat pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Diubah pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
