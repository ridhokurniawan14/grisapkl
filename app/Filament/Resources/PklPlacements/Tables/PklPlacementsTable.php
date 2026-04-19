<?php

namespace App\Filament\Resources\PklPlacements\Tables;

use App\Models\AcademicYear;
use App\Models\Major;
use App\Filament\Exports\PklPlacementExporter;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Filament\Notifications\Notification;

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
                TextColumn::make('student.studentClass.name')
                    ->label('Kelas')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray'),
                TextColumn::make('dudika.name')
                    ->label('Tempat DUDIKA')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
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
                SelectFilter::make('academic_year_id')
                    ->label('Filter Tahun Ajaran')
                    ->relationship('academicYear', 'name')
                    ->default(fn() => AcademicYear::where('is_active', true)->value('id')),

                SelectFilter::make('major_id')
                    ->label('Filter Jurusan')
                    ->options(Major::pluck('name', 'id'))
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['value'])) {
                            $query->whereHas('student.studentClass', function ($q) use ($data) {
                                $q->where('major_id', $data['value']);
                            });
                        }
                    }),

                SelectFilter::make('dudika_id')
                    ->label('Filter DUDIKA')
                    ->relationship('dudika', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->headerActions([
                // TOMBOL EXPORT
                ExportAction::make()
                    ->label('Download Data')
                    ->icon('heroicon-m-arrow-down-tray')
                    ->color('success')
                    ->exporter(PklPlacementExporter::class)
            ])
            ->recordActions([
                // TOMBOL AKSI DIBIKIN BUTTON OUTLINED (SAMA KAYA DUDIKA)
                ActionGroup::make([
                    ViewAction::make()->label('Detail'),
                    EditAction::make()->label('Ubah'),
                    DeleteAction::make()->label('Hapus'),
                ])->label('Aksi')->button()->outlined(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Hapus Terpilih'),

                    BulkAction::make('ubah_status_ditarik')
                        ->label('Ubah Status: Ditarik')
                        ->icon('heroicon-m-arrow-path-rounded-square')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $record->update(['status' => 'Ditarik']);
                            });
                            Notification::make()
                                ->title('Status siswa terpilih berhasil diubah menjadi Ditarik')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }
}
