<?php

namespace App\Filament\Resources\Dudikas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DudikasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama DUDIKA')
                    ->searchable(),
                TextColumn::make('supervisor_name')
                    ->label('Pembimbing')
                    ->searchable(),
                TextColumn::make('supervisor_phone')
                    ->label('No. HP Pembimbing')
                    ->searchable(),

                // Kolom di bawah ini kita hide default biar tabel nggak penuh
                TextColumn::make('head_name')
                    ->label('Pimpinan')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('head_nip')
                    ->label('NIP Pimpinan')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('supervisor_nip')
                    ->label('NIP Pembimbing')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
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
