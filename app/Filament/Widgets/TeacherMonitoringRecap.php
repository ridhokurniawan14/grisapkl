<?php

namespace App\Filament\Widgets;

use App\Models\Teacher;
use App\Models\PklPlacement;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;

class TeacherMonitoringRecap extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Rekapitulasi Monitoring Guru Pembimbing';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Teacher::query()
                    ->with(['pklPlacements.monitorings', 'pklPlacements.dudika'])
                    ->addSelect([
                        'teachers.*',

                        // Subquery unique_visits_count (tetap sama)
                        'unique_visits_count' => DB::table('monitorings as m')
                            ->selectRaw('COALESCE(SUM(per_dudika.cnt), 0)')
                            ->fromSub(
                                DB::table('monitorings as m2')
                                    ->selectRaw('COUNT(DISTINCT m2.date) as cnt')
                                    ->join('pkl_placements as pp', 'pp.id', '=', 'm2.pkl_placement_id')
                                    ->whereColumn('pp.teacher_id', 'teachers.id')
                                    ->groupBy('pp.dudika_id'),
                                'per_dudika'
                            ),

                        // FIX: Tambah subquery latest_monitoring_date untuk sorting
                        'latest_monitoring_date' => DB::table('monitorings as m')
                            ->selectRaw('MAX(m.date)')
                            ->join('pkl_placements as pp', 'pp.id', '=', 'm.pkl_placement_id')
                            ->whereColumn('pp.teacher_id', 'teachers.id'),
                    ])
            )
            ->defaultSort('latest_monitoring_date', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Guru Pembimbing')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('rincian_dudika')
                    ->label('Rincian Kunjungan per DUDIKA')
                    ->searchable(query: function (\Illuminate\Database\Eloquent\Builder $query, string $search): void {
                        // Search melalui join ke tabel aslinya
                        $query->whereHas('pklPlacements.dudika', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        });
                    })

                    ->state(function (Teacher $record) {
                        $pklPlacements = $record->pklPlacements;

                        $allMonitorings = collect();
                        foreach ($pklPlacements as $placement) {
                            foreach ($placement->monitorings as $monitoring) {
                                $allMonitorings->push([
                                    'dudika'    => $placement->dudika->name ?? 'DUDIKA Tidak Diketahui',
                                    'dudika_id' => $placement->dudika_id,
                                    'date'      => $monitoring->date,
                                ]);
                            }
                        }

                        if ($allMonitorings->isEmpty()) {
                            return new HtmlString(
                                '<span class="text-danger-500 italic">Belum ada kunjungan</span>'
                            );
                        }

                        // Grup per DUDIKA → hitung hari unik per DUDIKA
                        $grouped = $allMonitorings->groupBy('dudika_id');

                        $html = '<ul class="list-disc pl-4 text-sm">';
                        foreach ($grouped as $dudikaId => $items) {
                            $dudikaName   = $items->first()['dudika'];
                            // Kunjungan unik = hari berbeda di DUDIKA ini
                            $uniqueVisits = $items->unique('date')->count();

                            $html .= "<li><strong>{$dudikaName}</strong> : {$uniqueVisits}x kunjungan</li>";
                        }
                        $html .= '</ul>';

                        return new HtmlString($html);
                    }),

                Tables\Columns\TextColumn::make('unique_visits_count')
                    ->label('Total Keseluruhan')
                    ->badge()
                    ->color(fn($state): string => match (true) {
                        (int) $state === 0 => 'danger',
                        (int) $state <= 2  => 'warning',
                        default            => 'success',
                    })
                    ->sortable()
                    ->alignCenter(),
            ])
            ->paginated([5, 10, 25]);
    }
}
