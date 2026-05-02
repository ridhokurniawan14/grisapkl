<?php

namespace App\Filament\Widgets;

use App\Models\AcademicYear;
use App\Models\Dudika;
use App\Models\Journal;
use App\Models\MonitoringSchedule;
use App\Models\PklPlacement;
use App\Models\Teacher;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class DashboardStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    protected ?string $heading = null;

    protected function getStats(): array
    {
        $activeYear = AcademicYear::where('is_active', true)->first();

        if (!$activeYear) {
            return [
                Stat::make('⚠️ Status Sistem', 'Tahun Ajaran Belum Diset')
                    ->description('Silakan aktifkan 1 Tahun Ajaran di Data Master')
                    ->color('danger'),
            ];
        }

        $yearId = $activeYear->id;
        $today  = Carbon::today()->toDateString();

        // --- Hitung Data ---
        $totalSiswa = PklPlacement::where('academic_year_id', $yearId)->count();

        $totalDudika = Dudika::whereHas('pklPlacements', function ($q) use ($yearId) {
            $q->where('academic_year_id', $yearId);
        })->count();

        $totalGuru = Teacher::whereHas('pklPlacements', function ($q) use ($yearId) {
            $q->where('academic_year_id', $yearId);
        })->count();

        // Kehadiran hari ini
        $hadirHariIni = Journal::where('date', $today)
            ->where('attend_status', 'Hadir')
            ->whereHas('pklPlacement', fn($q) => $q->where('academic_year_id', $yearId))
            ->count();

        $absenHariIni = Journal::where('date', $today)
            ->whereIn('attend_status', ['Alpha', 'Tanpa Keterangan', 'Sakit', 'Izin'])
            ->whereHas('pklPlacement', fn($q) => $q->where('academic_year_id', $yearId))
            ->count();

        $totalJurnalHariIni = $hadirHariIni + $absenHariIni;
        $persenHadir = $totalJurnalHariIni > 0
            ? round(($hadirHariIni / $totalJurnalHariIni) * 100)
            : 0;

        // Monitoring — tabel tidak punya academic_year_id, pakai is_active
        $totalMonitoring   = MonitoringSchedule::count();
        $monitoringAktif   = MonitoringSchedule::where('is_active', true)->count();

        // Trend kehadiran 7 hari (untuk sparkline)
        $trendHadir = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $trendHadir[] = Journal::where('date', $date)
                ->where('attend_status', 'Hadir')
                ->whereHas('pklPlacement', fn($q) => $q->where('academic_year_id', $yearId))
                ->count();
        }

        return [
            Stat::make('Total Siswa PKL', $totalSiswa)
                ->description('Tahun Ajaran ' . $activeYear->name)
                ->descriptionIcon('heroicon-m-academic-cap')
                ->icon('heroicon-o-user-group')
                ->color('primary'),

            Stat::make('Tempat PKL (DUDIKA)', $totalDudika)
                ->description('Industri/Perusahaan Aktif')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->icon('heroicon-o-building-office')
                ->color('warning'),

            Stat::make('Guru Pembimbing', $totalGuru)
                ->description('Terlibat dalam PKL aktif')
                ->descriptionIcon('heroicon-m-identification')
                ->icon('heroicon-o-briefcase')
                ->color('info'),

            Stat::make('Kehadiran Hari Ini', $persenHadir . '%')
                ->description($hadirHariIni . ' dari ' . $totalJurnalHariIni . ' siswa mengisi jurnal')
                ->descriptionIcon($persenHadir >= 80 ? 'heroicon-m-check-circle' : 'heroicon-m-exclamation-triangle')
                ->icon('heroicon-o-calendar-days')
                ->color($persenHadir >= 80 ? 'success' : ($persenHadir >= 50 ? 'warning' : 'danger'))
                ->chart($trendHadir),

            Stat::make('Jadwal Monitoring', $monitoringAktif . ' / ' . $totalMonitoring)
                ->description(
                    $totalMonitoring > 0
                        ? $monitoringAktif . ' jadwal sedang aktif berjalan'
                        : 'Belum ada jadwal monitoring'
                )
                ->descriptionIcon('heroicon-m-chart-bar')
                ->icon('heroicon-o-clipboard-document-check')
                ->color($monitoringAktif > 0 ? 'success' : 'warning'),

            Stat::make('Jurnal Hari Ini', $totalJurnalHariIni)
                ->description('Entri jurnal ' . Carbon::today()->isoFormat('D MMMM YYYY'))
                ->descriptionIcon('heroicon-m-pencil-square')
                ->icon('heroicon-o-book-open')
                ->color('success'),
        ];
    }
}
