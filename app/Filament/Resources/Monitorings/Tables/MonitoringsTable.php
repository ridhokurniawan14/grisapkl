<?php

namespace App\Filament\Resources\Monitorings\Tables;

use App\Models\Monitoring;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class MonitoringsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->poll('5s')
            ->defaultSort('date', 'desc')

            ->modifyQueryUsing(function (Builder $query) {
                $query
                    ->whereHas('pklPlacement.academicYear', function ($q) {
                        $q->where('is_active', true);
                    })
                    ->whereIn('id', function ($sub) {
                        $sub->selectRaw('MIN(monitorings.id)')
                            ->from('monitorings')
                            ->join('pkl_placements', 'pkl_placements.id', '=', 'monitorings.pkl_placement_id')
                            ->groupBy(
                                'pkl_placements.dudika_id',
                                'monitorings.date',
                                'monitorings.monitoring_schedule_id'
                            );
                    });
            })

            ->columns([
                TextColumn::make('pklPlacement.teacher.name')
                    ->label('Guru Pembimbing')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('activity')
                    ->label('Deskripsi')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('pklPlacement.dudika.name')
                    ->label('Tempat PKL (DUDIKA)')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('students_count')
                    ->label('Siswa Tercakup')
                    ->state(function (Monitoring $record): string {
                        $count = Monitoring::where('date', $record->date)
                            ->where('monitoring_schedule_id', $record->monitoring_schedule_id)
                            ->whereHas('pklPlacement', fn($q) => $q->where(
                                'dudika_id',
                                $record->pklPlacement->dudika_id
                            ))
                            ->count();

                        return $count . ' Siswa';
                    })
                    ->badge()
                    ->color('success'),

                TextColumn::make('date')
                    ->label('Tanggal Kunjungan')
                    ->date('d M Y')
                    ->sortable(),

                ImageColumn::make('photo_path')
                    ->label('Foto Kunjungan')
                    ->circular()
                    ->disk('public')
                    ->stacked()
                    ->action(
                        Action::make('lihat_foto')
                            ->modalHeading('Foto Bukti Kunjungan')
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

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            ->filters([
                SelectFilter::make('dudika_id')
                    ->label('Filter DUDIKA')
                    ->options(\App\Models\Dudika::pluck('name', 'id'))
                    ->searchable()
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['value'])) {
                            $query->whereHas('pklPlacement', function ($q) use ($data) {
                                $q->where('dudika_id', $data['value']);
                            });
                        }
                    }),

                SelectFilter::make('teacher_id')
                    ->label('Filter Guru Pembimbing')
                    ->options(\App\Models\Teacher::pluck('name', 'id'))
                    ->searchable()
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['value'])) {
                            $query->whereHas('pklPlacement', function ($q) use ($data) {
                                $q->where('teacher_id', $data['value']);
                            });
                        }
                    }),
            ])
            ->headerActions([
                // TOMBOL 1: SURUH PEKERJA BELAKANG LAYAR
                Action::make('generate_rekap')
                    ->label('Generate Rekap (Proses)')
                    ->icon('heroicon-o-cog')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Mulai Generate Rekap?')
                    ->modalDescription('Proses ini akan merangkum semua foto kunjungan guru dan dikerjakan di latar belakang. Anda akan menerima notifikasi di lonceng jika sudah selesai.')
                    ->action(function () {
                        // Panggil Job
                        \App\Jobs\GenerateRekapMonitoringJob::dispatch(auth()->id());

                        Notification::make()
                            ->title('Proses Generate Dimulai!')
                            ->body('Silakan tunggu notifikasi di lonceng saat file siap.')
                            ->success()
                            ->send();
                    }),

                // TOMBOL 2: DOWNLOAD HASILNYA (DENGAN SUARA TING)
                Action::make('download_rekap')
                    ->label(function () {
                        $label = 'Download PDF Rekap';
                        $filePath = storage_path('app/public/laporan_pkl/Rekap_Monitoring_Guru.pdf');

                        // MANTRA SAKTI: Cek apakah file baru saja selesai dibuat (dalam 6 detik terakhir)
                        if (file_exists($filePath) && (time() - filemtime($filePath)) <= 6) {
                            // Putar suara kalau filenya fresh from the oven!
                            $label .= '<span x-data x-init="new Audio(\'https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3\').play().catch(e => console.log(\'Autoplay diblokir\'))"></span>';
                        }

                        return new \Illuminate\Support\HtmlString($label);
                    })
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(fn() => \Illuminate\Support\Facades\Storage::url('laporan_pkl/Rekap_Monitoring_Guru.pdf') . '?v=' . time())
                    ->openUrlInNewTab()
                    ->visible(fn() => \Illuminate\Support\Facades\Storage::disk('public')->exists('laporan_pkl/Rekap_Monitoring_Guru.pdf')),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()->label('Detail'),
                    EditAction::make()->label('Ubah'),

                    // ✅ Override DeleteAction — hapus semua sibling (1 DUDIKA = semua siswa)
                    DeleteAction::make()
                        ->label('Hapus')
                        ->modalHeading('Hapus Data Kunjungan')
                        ->modalDescription(function (Monitoring $record): string {
                            $count = Monitoring::where('date', $record->date)
                                ->where('monitoring_schedule_id', $record->monitoring_schedule_id)
                                ->whereHas('pklPlacement', fn($q) => $q->where(
                                    'dudika_id',
                                    $record->pklPlacement->dudika_id
                                ))
                                ->count();

                            $dudika = $record->pklPlacement->dudika->name;

                            return "Kunjungan ke \"{$dudika}\" akan dihapus beserta {$count} data siswa terkait. Tindakan ini tidak dapat dibatalkan.";
                        })
                        ->modalSubmitActionLabel('Ya, Hapus Semua')
                        ->using(function (Monitoring $record): void {
                            // ✅ Cari semua sibling records berdasarkan DUDIKA + tanggal + jadwal
                            $siblings = Monitoring::where('date', $record->date)
                                ->where('monitoring_schedule_id', $record->monitoring_schedule_id)
                                ->whereHas('pklPlacement', fn($q) => $q->where(
                                    'dudika_id',
                                    $record->pklPlacement->dudika_id
                                ))
                                ->get();

                            // Hapus satu per satu agar model event (foto cleanup) tetap jalan
                            $siblings->each(fn($m) => $m->delete());
                        })
                        ->successNotification(
                            Notification::make()
                                ->title('Kunjungan berhasil dihapus')
                                ->body('Semua data siswa dalam kunjungan ini telah dihapus.')
                                ->success()
                        ),

                ])->button()->outlined()->label('Aksi'),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    // ✅ Override DeleteBulkAction — expand ke semua sibling dulu
                    DeleteBulkAction::make()
                        ->label('Hapus Terpilih')
                        ->modalHeading('Hapus Kunjungan Terpilih')
                        ->modalDescription('Semua data siswa dalam kunjungan yang dipilih akan dihapus permanen.')
                        ->modalSubmitActionLabel('Ya, Hapus Semua')
                        ->using(function (Collection $records): void {
                            // Kumpulkan semua sibling ID dari setiap record yang dicentang
                            $allSiblingIds = collect();

                            foreach ($records as $record) {
                                $siblingIds = Monitoring::where('date', $record->date)
                                    ->where('monitoring_schedule_id', $record->monitoring_schedule_id)
                                    ->whereHas('pklPlacement', fn($q) => $q->where(
                                        'dudika_id',
                                        $record->pklPlacement->dudika_id
                                    ))
                                    ->pluck('id');

                                $allSiblingIds = $allSiblingIds->merge($siblingIds);
                            }

                            // Hapus unique IDs, satu per satu agar model event foto cleanup jalan
                            Monitoring::whereIn('id', $allSiblingIds->unique())
                                ->get()
                                ->each(fn($m) => $m->delete());
                        })
                        ->successNotification(
                            Notification::make()
                                ->title('Kunjungan terpilih berhasil dihapus')
                                ->body('Semua data siswa dalam kunjungan terpilih telah dihapus.')
                                ->success()
                        ),
                ]),
            ]);
    }
}
