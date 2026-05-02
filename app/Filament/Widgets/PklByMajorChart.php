<?php

namespace App\Filament\Widgets;

use App\Models\AcademicYear;
use App\Models\PklPlacement;
use Filament\Widgets\ChartWidget;

class PklByMajorChart extends ChartWidget
{
    protected ?string $heading    = 'Sebaran PKL per Jurusan';
    protected ?string $description = 'Distribusi siswa PKL berdasarkan jurusan aktif';
    protected static ?int $sort   = 3;
    protected int|string|array $columnSpan = 1;
    protected ?string $maxHeight = '280px';

    protected function getData(): array
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        if (!$activeYear) {
            return ['datasets' => [], 'labels' => []];
        }

        // Ambil data siswa PKL grouped by jurusan via relasi
        $data = PklPlacement::where('academic_year_id', $activeYear->id)
            ->with('student.studentClass.major')
            ->get()
            ->groupBy(fn($placement) => optional(
                optional(optional($placement->student)->studentClass)->major
            )->name ?? 'Tidak Diketahui')
            ->map->count();

        $colors = [
            'rgba(99,  102, 241, 0.85)', // Indigo
            'rgba(16,  185, 129, 0.85)', // Emerald
            'rgba(245, 158,  11, 0.85)', // Amber
            'rgba(239,  68,  68, 0.85)', // Red
            'rgba(59,  130, 246, 0.85)', // Blue
            'rgba(168,  85, 247, 0.85)', // Purple
            'rgba(236,  72, 153, 0.85)', // Pink
        ];

        return [
            'datasets' => [
                [
                    'data'                 => $data->values()->toArray(),
                    'backgroundColor'      => array_slice($colors, 0, $data->count()),
                    'hoverBackgroundColor' => array_slice($colors, 0, $data->count()),
                    'borderWidth'          => 2,
                    'borderColor'          => 'transparent',
                    'hoverOffset'          => 8,
                ],
            ],
            'labels' => $data->keys()->toArray(),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display'  => true,
                    'position' => 'bottom',
                    'labels'   => [
                        'usePointStyle' => true,
                        'padding'       => 16,
                        'font'          => ['size' => 12],
                    ],
                ],
            ],
            'cutout' => '65%',
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
