<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AttendanceChart;
use App\Filament\Widgets\DashboardStatsOverview;
use App\Filament\Widgets\MasterDataStatus;
use App\Filament\Widgets\PklByMajorChart;
use App\Filament\Widgets\RecentJournalWidget;
use BackedEnum;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\AccountWidget;

class Dashboard extends BaseDashboard
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHomeModern;
    protected static ?string $navigationLabel = 'Dasbor';
    protected static ?string $title           = 'Dasbor GrisaPKL';

    /**
     * Mengatur urutan dan layout widget di halaman dashboard.
     * Sort order dikontrol lewat $sort di masing-masing widget.
     */
    public function getWidgets(): array
    {
        return [
            AccountWidget::class,           // sort: default (atas)
            DashboardStatsOverview::class,  // sort: 1  — 6 stat cards
            AttendanceChart::class,         // sort: 2  — bar chart full width
            PklByMajorChart::class,         // sort: 3  — doughnut (1/2 width kiri)
            RecentJournalWidget::class,     // sort: 4  — table (1/2 width kanan)
            MasterDataStatus::class,        // sort: 5  — status grid full width
        ];
    }

    /**
     * Default column span untuk widget 2-kolom (PklByMajorChart & RecentJournalWidget)
     */
    public function getColumns(): int|array
    {
        return 2;
    }
}
