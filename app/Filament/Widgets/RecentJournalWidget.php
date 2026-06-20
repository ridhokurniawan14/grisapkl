<?php

namespace App\Filament\Widgets;

use App\Models\AcademicYear;
use App\Models\Journal;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentJournalWidget extends BaseWidget
{
    protected static ?string $heading    = 'Jurnal Masuk Terbaru';
    protected static ?int    $sort       = 4;
    protected int|string|array $columnSpan = 1;

    public function table(Table $table): Table
    {
        $activeYear = AcademicYear::where('is_active', true)->first();

        return $table
            ->poll('5s')
            ->query(
                Journal::query()
                    ->when(
                        $activeYear,
                        fn(Builder $q) =>
                        $q->whereHas(
                            'pklPlacement',
                            fn($q2) =>
                            $q2->where('academic_year_id', $activeYear->id)
                        )
                    )
                    // 1. PERBAIKAN: Urutkan dari tanggal & jam terbaru
                    ->orderBy('date', 'desc')
                    ->orderBy('time', 'desc')
                    // 2. PERBAIKAN: Munculkan 10 data terbaru
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('pklPlacement.student.name')
                    ->label('Siswa')
                    ->searchable()
                    ->limit(20)
                    ->weight('semibold'),

                // 3. PERBAIKAN: Tambahkan jam di bawah teks tanggal pakai description()
                Tables\Columns\TextColumn::make('date')
                    ->label('Waktu Absen')
                    ->date('d M Y')
                    ->description(fn(Journal $record): string => $record->time ? 'Jam: ' . \Carbon\Carbon::parse($record->time)->format('H:i') . ' WIB' : 'Jam: -')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('attend_status')
                    ->label('Status')
                    ->colors([
                        'success' => 'Hadir',
                        'warning' => fn($state) => in_array($state, ['Sakit', 'Izin']),
                        'danger'  => fn($state) => in_array($state, ['Alpha', 'Tanpa Keterangan']),
                    ]),
            ])
            ->paginated(false)
            ->striped();
    }
}
