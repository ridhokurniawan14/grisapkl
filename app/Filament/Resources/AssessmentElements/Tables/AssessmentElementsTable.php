<?php

namespace App\Filament\Resources\AssessmentElements\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AssessmentElementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Elemen')
                    ->searchable()
                    ->weight('bold')
                    ->wrap(),

                TextColumn::make('tp_name')
                    ->label('Tujuan Pembelajaran')
                    ->searchable()
                    ->wrap()
                    ->color('gray'),

                // Menghitung otomatis jumlah indikator yang ada di dalam elemen ini
                TextColumn::make('assessment_indicators_count')
                    ->counts('assessmentIndicators')
                    ->label('Jumlah Indikator')
                    ->badge()
                    ->color('info'),
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
                ]),
            ]);
    }
}
