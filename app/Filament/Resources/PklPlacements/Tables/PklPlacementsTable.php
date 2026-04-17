<?php

namespace App\Filament\Resources\PklPlacements\Tables;

use App\Models\AcademicYear;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PklPlacementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->poll('5s')
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('rowIndex')
                    ->label('No')
                    ->rowIndex(),
                TextColumn::make('academicYear.name')
                    ->label('Tahun Ajaran')
                    ->badge()
                    ->color('success')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('student.name')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('dudika.name')
                    ->label('Tempat DUDIKA')
                    ->searchable()
                    ->sortable()
                    ->wrap(), // Teks membungkus ke bawah jika panjang
                TextColumn::make('teacher.name')
                    ->label('Pembimbing')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('start_date')
                    ->label('Mulai PKL')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('end_date')
                    ->label('Selesai PKL')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Aktif' => 'success',
                        'Ditarik' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                // DEFAULT FILTER HANYA MENAMPILKAN TAHUN AJARAN AKTIF
                SelectFilter::make('academic_year_id')
                    ->label('Filter Tahun Ajaran')
                    ->relationship('academicYear', 'name')
                    ->default(fn() => AcademicYear::where('is_active', true)->value('id')),

                SelectFilter::make('dudika_id')
                    ->label('Filter DUDIKA')
                    ->relationship('dudika', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make()->label('Detail'),
                EditAction::make()->label('Ubah'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Hapus Terpilih'),
                ]),
            ]);
    }
}
