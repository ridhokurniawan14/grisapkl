<?php

namespace App\Filament\Resources\Journals\Tables;

use App\Models\Major;
use App\Models\Student;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Grid;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Enums\FiltersLayout;

class JournalsTable
{
    // ============================================================
    // STATIC BRIDGE: Jembatan nilai filter → Summarizer
    // ============================================================
    public static ?string $filterStart      = null;
    public static ?string $filterEnd        = null;
    public static ?string $filterStudentId  = null;

    public static function configure(Table $table): Table
    {
        return $table
            ->poll('5s')
            ->deferLoading()
            ->defaultSort('date', 'desc')
            ->modifyQueryUsing(function (Builder $query, Table $table) {
                $filters = $table->getLivewire()->tableFilters ?? [];

                $hasMajorFilter   = !empty($filters['major_id']['value']);
                $hasStudentFilter = !empty($filters['student_id']['value']);

                static::$filterStart     = $filters['date_range']['start'] ?? null;
                static::$filterEnd       = $filters['date_range']['end'] ?? null;
                static::$filterStudentId = $filters['student_id']['value'] ?? null;

                if (!$hasMajorFilter && !$hasStudentFilter) {
                    $query->whereRaw('1 = 0');
                }
            })
            ->summaries(
                pageCondition: false,   // hilangkan "Halaman ini"
                allTableCondition: true // tetap tampilkan "Semua Jurnal Siswa"
            )
            ->emptyStateHeading('Silakan Cari Data Terlebih Dahulu')
            ->emptyStateDescription('Pilih Jurusan atau Cari Nama Siswa, lalu klik tombol Cari Data.')
            ->emptyStateIcon('heroicon-o-magnifying-glass-circle')
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
                        'Izin'  => 'warning',
                        'Sakit' => 'danger',
                        default => 'gray',
                    })
                    ->summarize([
                        Summarizer::make('rekap_kehadiran')
                            ->label('Rekap Kehadiran')
                            ->visible(fn(): bool => !empty(static::$filterStudentId))
                            ->using(function (\Illuminate\Database\Query\Builder $query): string {
                                if (empty(static::$filterStudentId)) {
                                    return '';
                                }

                                // ============================================================
                                // SKIP "Halaman Ini" — hanya tampilkan summary "Semua Data"
                                // Filament menambahkan whereIn('{table}.id', [...]) saat
                                // merender baris rekap per halaman (pagination context)
                                // ============================================================
                                $isPageContext = collect($query->wheres ?? [])->contains(
                                    fn($w) => ($w['type'] ?? '') === 'In'
                                        && str_contains($w['column'] ?? '', '.id')
                                );

                                if ($isPageContext) {
                                    return ''; // Baris "Halaman Ini" → dikosongkan = tidak dirender Filament
                                }

                                // ============================================================
                                // "Semua Data" context — hitung rekap lengkap
                                // ============================================================
                                $hadir = (clone $query)->where('attend_status', 'Hadir')->count();
                                $izin  = (clone $query)->where('attend_status', 'Izin')->count();
                                $sakit = (clone $query)->where('attend_status', 'Sakit')->count();
                                $libur = (clone $query)->where('attend_status', 'Libur')->count();

                                $start = static::$filterStart;
                                $end   = static::$filterEnd;

                                if ($start && $end) {
                                    $totalDays = Carbon::parse($start)->diffInDays(Carbon::parse($end)) + 1;

                                    $daysWithRecord = (clone $query)
                                        ->selectRaw('DATE(date) as tgl')
                                        ->groupBy('tgl')
                                        ->get()
                                        ->count();

                                    $alpha = max(0, $totalDays - $daysWithRecord);

                                    return "Hadir: {$hadir} | Izin: {$izin} | Sakit: {$sakit} | Alpha: {$alpha} | Libur: {$libur}";
                                }

                                return "Hadir: {$hadir} | Izin: {$izin} | Sakit: {$sakit} | Libur: {$libur}";
                            }),
                    ]),

                ImageColumn::make('photo_path')
                    ->label('Foto Kegiatan')
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

                \Filament\Tables\Filters\Filter::make('date_range')
                    ->form([
                        Grid::make(2)->schema([
                            DatePicker::make('start')->label('Dari Tanggal'),
                            DatePicker::make('end')->label('Sampai Tanggal'),
                        ])
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['start'], fn(Builder $q, $date) => $q->whereDate('date', '>=', $date))
                            ->when($data['end'],   fn(Builder $q, $date) => $q->whereDate('date', '<=', $date));
                    })
            ])
            ->filtersLayout(FiltersLayout::AboveContent)
            ->filtersFormColumns(3)
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
                    EditAction::make()->label('Validasi / Ubah'),
                    DeleteAction::make()->label('Hapus'),
                ])->button()->outlined()->label('Aksi'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Hapus Terpilih'),
                ]),
            ]);
    }
}
