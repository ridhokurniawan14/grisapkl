<?php

namespace App\Filament\Widgets;

use App\Models\AcademicYear;
use App\Models\Journal;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class AttendanceChart extends ChartWidget
{
    protected ?string $heading = 'Rekap Kehadiran — 14 Hari Terakhir';
    protected ?string $description = 'Perbandingan kehadiran vs ketidakhadiran siswa PKL';
    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = 'full';
    protected ?string $maxHeight = '280px';

    // Filter: bisa dipilih user di UI
    public ?string $filter = '14';

    protected function getFilters(): ?array
    {
        return [
            '7'  => '7 Hari Terakhir',
            '14' => '14 Hari Terakhir',
            '30' => '30 Hari Terakhir',
        ];
    }

    protected function getData(): array
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        if (!$activeYear) {
            return ['datasets' => [], 'labels' => []];
        }

        $days = (int) $this->filter;
        $labels     = [];
        $dataHadir  = [];
        $dataIzin   = [];
        $dataAlpha  = [];

        for ($i = ($days - 1); $i >= 0; $i--) {
            $date     = Carbon::now()->subDays($i)->format('Y-m-d');
            $labels[] = Carbon::parse($date)->isoFormat('D MMM');

            $baseQuery = fn() => Journal::where('date', $date)
                ->whereHas('pklPlacement', fn($q) => $q->where('academic_year_id', $activeYear->id));

            $dataHadir[] = (clone $baseQuery())->where('attend_status', 'Hadir')->count();
            $dataIzin[]  = (clone $baseQuery())->whereIn('attend_status', ['Sakit', 'Izin'])->count();
            $dataAlpha[] = (clone $baseQuery())->whereIn('attend_status', ['Alpha', 'Tanpa Keterangan'])->count();
        }

        return [
            'datasets' => [
                [
                    'label'           => 'Hadir',
                    'data'            => $dataHadir,
                    'backgroundColor' => 'rgba(16, 185, 129, 0.85)',
                    'borderColor'     => '#10b981',
                    'borderWidth'     => 0,
                    'borderRadius'    => 6,
                    'borderSkipped'   => false,
                ],
                [
                    'label'           => 'Izin / Sakit',
                    'data'            => $dataIzin,
                    'backgroundColor' => 'rgba(245, 158, 11, 0.85)',
                    'borderColor'     => '#f59e0b',
                    'borderWidth'     => 0,
                    'borderRadius'    => 6,
                    'borderSkipped'   => false,
                ],
                [
                    'label'           => 'Alpha / Bolos',
                    'data'            => $dataAlpha,
                    'backgroundColor' => 'rgba(239, 68, 68, 0.85)',
                    'borderColor'     => '#ef4444',
                    'borderWidth'     => 0,
                    'borderRadius'    => 6,
                    'borderSkipped'   => false,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display'  => true,
                    'position' => 'top',
                    'labels'   => ['usePointStyle' => true, 'padding' => 20],
                ],
                'tooltip' => [
                    'mode'      => 'index',
                    'intersect' => false,
                ],
            ],
            'scales' => [
                'x' => [
                    'stacked' => false,
                    'grid'    => ['display' => false],
                ],
                'y' => [
                    'stacked'    => false,
                    'beginAtZero' => true,
                    'grid'       => ['color' => 'rgba(156, 163, 175, 0.1)'],
                    'ticks'      => ['precision' => 0],
                ],
            ],
            'interaction' => [
                'mode'      => 'index',
                'intersect' => false,
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
