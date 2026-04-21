<?php

namespace App\Filament\Resources\Journals\Tables;

use App\Models\Major;
use App\Models\Student;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class JournalsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->deferLoading()
            ->defaultSort('date', 'desc')

            // =======================================================
            // MANTRA SAKTI: CEGAH LOAD DATA SEBELUM FILTER DIPILIH
            // =======================================================
            ->modifyQueryUsing(function (Builder $query, Table $table) {
                // Ambil properti filter langsung dari Livewire agar tidak error ArgumentCount
                $filters = $table->getLivewire()->tableFilters ?? [];

                // Cek apakah filter Jurusan atau Siswa sudah diisi?
                $hasMajorFilter = !empty($filters['major_id']['value']);
                $hasStudentFilter = !empty($filters['student_id']['value']);

                // Jika KEDUANYA KOSONG, pangkas query jadi 0 data
                if (!$hasMajorFilter && !$hasStudentFilter) {
                    $query->whereRaw('1 = 0');
                }
            })
            ->emptyStateHeading('Silakan Filter Data Terlebih Dahulu')
            ->emptyStateDescription('Data jurnal sangat banyak. Gunakan ikon Filter (Corong) di kanan atas untuk memilih Jurusan atau Nama Siswa.')
            ->emptyStateIcon('heroicon-o-funnel')
            // =======================================================

            ->columns([
                TextColumn::make('pklPlacement.student.name')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('pklPlacement.dudika.name')
                    ->label('Tempat PKL')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('time')
                    ->label('Waktu')
                    ->time('H:i')
                    ->sortable(),

                TextColumn::make('attend_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Hadir' => 'success',
                        'Izin' => 'warning',
                        'Sakit' => 'danger',
                        default => 'gray',
                    }),

                ImageColumn::make('photo_path')
                    ->label('Foto')
                    ->circular()
                    ->disk('public')
                    ->stacked()
                    ->action(
                        Action::make('lihat_foto')
                            ->modalHeading('Foto Bukti Kegiatan')
                            ->modalWidth('5xl')
                            ->modalSubmitAction(false)
                            ->modalCancelActionLabel('Tutup')
                            ->infolist([
                                \Filament\Infolists\Components\ImageEntry::make('photo_path')
                                    ->hiddenLabel()
                                    ->disk('public')
                                    ->width('100%')
                                    ->height('auto')
                                    ->extraImgAttributes([
                                        'class' => 'rounded-xl shadow-md w-full',
                                        'style' => 'max-height: 80vh; object-fit: contain; margin: 0 auto;'
                                    ])
                            ])
                    ),

                IconColumn::make('is_valid')
                    ->label('Valid')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-circle'),
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
                    ->options(Student::pluck('name', 'id'))
                    ->searchable()
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['value'])) {
                            $query->whereHas('pklPlacement', function ($q) use ($data) {
                                $q->where('student_id', $data['value']);
                            });
                        }
                    }),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()->label('Detail'),
                    EditAction::make()->label('Validasi / Ubah'),
                    DeleteAction::make()->label('Hapus'),
                ])->button()->outlined()->label('Aksi')->icon('heroicon-m-cog-6-tooth'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Hapus Terpilih'),
                ]),
            ]);
    }
}
