<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class MasterDataStatus extends Widget
{
    protected string $view = 'filament.widgets.master-data-status';
    protected static ?int $sort = 5;
    protected int|string|array $columnSpan = 'full';

    public function getViewData(): array
    {
        $statuses = [
            [
                'label' => 'Profil Sekolah',
                'icon'  => 'heroicon-o-building-library',
                'done'  => \App\Models\SchoolProfile::count() > 0,
                'link'  => '/admin/school-profiles',
            ],
            [
                'label' => 'Tahun Ajaran',
                'icon'  => 'heroicon-o-calendar',
                'done'  => \App\Models\AcademicYear::count() > 0,
                'link'  => '/admin/academic-years',
            ],
            [
                'label' => 'Jurusan',
                'icon'  => 'heroicon-o-rectangle-stack',
                'done'  => \App\Models\Major::count() > 0,
                'link'  => '/admin/majors',
            ],
            [
                'label' => 'Kelas',
                'icon'  => 'heroicon-o-squares-2x2',
                'done'  => \App\Models\StudentClass::count() > 0,
                'link'  => '/admin/student-classes',
            ],
            [
                'label' => 'Guru Pembimbing',
                'icon'  => 'heroicon-o-academic-cap',
                'done'  => \App\Models\Teacher::count() > 0,
                'link'  => '/admin/teachers',
            ],
            [
                'label' => 'DUDIKA',
                'icon'  => 'heroicon-o-building-office-2',
                'done'  => \App\Models\Dudika::count() > 0,
                'link'  => '/admin/dudikas',
            ],
            [
                'label' => 'Siswa PKL',
                'icon'  => 'heroicon-o-user-group',
                'done'  => \App\Models\Student::count() > 0,
                'link'  => '/admin/students',
            ],
            [
                'label' => 'Jadwal Monitoring',
                'icon'  => 'heroicon-o-clipboard-document-list',
                'done'  => \App\Models\MonitoringSchedule::count() > 0,
                'link'  => '/admin/monitoring-schedules',
            ],
            [
                'label' => 'Skema Penilaian',
                'icon'  => 'heroicon-o-chart-bar',
                'done'  => \App\Models\AssessmentScheme::count() > 0,
                'link'  => '/admin/assessment-schemes',
            ],
            [
                'label' => 'Elemen Penilaian',
                'icon'  => 'heroicon-o-list-bullet',
                'done'  => \App\Models\AssessmentElement::count() > 0,
                'link'  => '/admin/assessment-elements',
            ],
        ];

        $totalDone = collect($statuses)->where('done', true)->count();
        $totalAll  = count($statuses);
        $percent   = (int) round(($totalDone / $totalAll) * 100);

        return [
            'statuses'  => $statuses,
            'totalDone' => $totalDone,
            'totalAll'  => $totalAll,
            'percent'   => $percent,
        ];
    }
}
