<?php

namespace App\Filament\Widgets;

use App\Models\PklPlacement;
use App\Models\Journal;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class AlphaStudentsWidget extends BaseWidget
{
    protected static ?string $heading = 'Siswa Bolos / Alpha (Belum Absen)';
    protected static ?int $sort = 6;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                PklPlacement::query()
                    ->where('status', 'Aktif')
                    ->whereHas('academicYear', fn($q) => $q->where('is_active', true))
            )
            ->modifyQueryUsing(function (Builder $query) {
                $date = ($this->tableFilters['tanggal']['date'] ?? null) ?: Carbon::today()->format('Y-m-d');

                $excludedIds = Journal::whereDate('date', $date)
                    ->whereIn('attend_status', ['Hadir', 'Izin', 'Sakit', 'Libur'])
                    ->pluck('pkl_placement_id')
                    ->toArray();

                $query->whereNotNull('start_date')
                    ->whereNotNull('end_date')
                    ->whereDate('start_date', '<=', $date)
                    ->whereDate('end_date', '>=', $date)
                    ->whereNotIn('id', $excludedIds);
            })
            ->description('Data otomatis menampilkan bolos HARI INI. Gunakan ikon Corong (Filter) untuk mengecek tanggal lain.')
            ->columns([
                Tables\Columns\TextColumn::make('student.name')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('dudika.name')
                    ->label('Tempat PKL (DUDIKA)')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('teacher.name')
                    ->label('Guru Pembimbing')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                Filter::make('tanggal')
                    ->form([
                        DatePicker::make('date')
                            ->label('Cek Tanggal Mundur')
                            ->native(false)
                            ->closeOnDateSelection()
                            ->default(Carbon::today()), // ← INI KUNCINYA!
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query; // tetap kosong, biar modifyQueryUsing yang jalan
                    })
                    ->indicateUsing(function (array $data): ?string {
                        // Tampilkan pill HANYA kalau bukan hari ini
                        if (!empty($data['date']) && $data['date'] !== Carbon::today()->format('Y-m-d')) {
                            return 'Cek Alpha: ' . Carbon::parse($data['date'])->isoFormat('dddd, D MMMM YYYY');
                        }
                        return null;
                    }),
            ])
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(5)
            ->striped();
    }
}
