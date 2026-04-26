<?php

namespace App\Filament\Resources\PklAssessments\Tables;

use App\Exports\PklAssessmentsExport;
use App\Models\Major;
use App\Models\Student;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Enums\FiltersLayout;
use Maatwebsite\Excel\Facades\Excel;

class PklAssessmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->poll('5s')
            ->deferLoading()
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(function (Builder $query, Table $table) {
                $filters = $table->getLivewire()->tableFilters ?? [];

                $hasMajorFilter   = !empty($filters['major_id']['value']);
                $hasStudentFilter = !empty($filters['student_id']['value']);

                if (!$hasMajorFilter && !$hasStudentFilter) {
                    $query->whereRaw('1 = 0');
                }
            })
            ->emptyStateHeading('Silakan Cari Data Terlebih Dahulu')
            ->emptyStateDescription('Pilih Jurusan atau Cari Nama Siswa, lalu klik tombol Cari Data.')
            ->emptyStateIcon('heroicon-o-magnifying-glass-circle')
            ->columns([
                TextColumn::make('pklPlacement.student.name')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('pklPlacement.student.studentClass.name')
                    ->label('Kelas')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('pklPlacement.assessmentScheme.name')
                    ->label('Skema Penilaian')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                // REVISI 4: KOLOM RATA-RATA NILAI
                TextColumn::make('rata_rata')
                    ->label('Rata-rata Nilai')
                    ->badge()
                    ->color('success')
                    ->state(function (\Illuminate\Database\Eloquent\Model $record) {
                        // Menghitung rata-rata dari relasi scores
                        $avg = $record->scores->avg('score');
                        return $avg ? number_format($avg, 2) : '0';
                    }),

                TextColumn::make('created_at')
                    ->label('Tanggal Dinilai')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('major_id')
                    ->label('Filter Jurusan')
                    ->options(Major::pluck('name', 'id'))
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['value'])) {
                            $query->whereHas('pklPlacement.student.studentClass', function ($q) use ($data) {
                                $q->where('major_id', $data['value']);
                            });
                        }
                    }),

                SelectFilter::make('student_id')
                    ->label('Cari Nama Siswa')
                    ->options(function () {
                        // LOGIKA AMAN: Ambil ID Penempatan yang sudah dinilai, lalu cari ID Siswanya
                        $assessedPlacementIds = \App\Models\PklAssessment::pluck('pkl_placement_id')->toArray();
                        $assessedStudentIds = \App\Models\PklPlacement::whereIn('id', $assessedPlacementIds)
                            ->pluck('student_id')
                            ->toArray();

                        // Munculkan hanya nama siswa yang ID-nya ada di daftar tersebut
                        return Student::whereIn('id', $assessedStudentIds)->pluck('name', 'id');
                    })
                    ->searchable()
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['value'])) {
                            // Ini tetap pklPlacement (tanpa s) karena query ini berjalan di model PklAssessment
                            $query->whereHas('pklPlacement', function ($q) use ($data) {
                                $q->where('student_id', $data['value']);
                            });
                        }
                    }),
            ])
            ->filtersLayout(FiltersLayout::AboveContent)
            ->filtersFormColumns(2)
            ->deferFilters()
            ->filtersApplyAction(
                fn(Action $action) => $action
                    ->label('Cari Data')
                    ->icon('heroicon-m-magnifying-glass')
                    ->extraAttributes(['class' => 'ml-auto justify-end w-full sm:w-auto mt-4'])
                    ->button()
            )
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()->label('Detail'),
                    EditAction::make()->label('Ubah'),
                    DeleteAction::make()->label('Hapus'),
                ])->button()->outlined()->label('Aksi'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Hapus Terpilih'),
                ]),
            ]);
    }
}
