<?php

namespace App\Filament\Resources\Monitorings\Tables;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MonitoringsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->poll('5s')
            ->defaultSort('date', 'desc')

            // MANTRA SAKTI: Default filter untuk tahun ajaran aktif (Asumsi menggunakan tahun berjalan)
            ->modifyQueryUsing(function (Builder $query) {
                // Memfilter data monitoring yang siswanya sedang berada di Tahun Ajaran yang statusnya 'Aktif'
                $query->whereHas('pklPlacement.academicYear', function ($q) {
                    $q->where('is_active', true);
                });
            })

            ->columns([
                TextColumn::make('pklPlacement.teacher.name')
                    ->label('Guru Pembimbing')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('pklPlacement.dudika.name')
                    ->label('Tempat PKL')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('date')
                    ->label('Tanggal Kunjungan')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('time')
                    ->label('Waktu')
                    ->time('H:i')
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

            // FILTER CORONG (Default UI Filament)
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
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()->label('Detail'),
                    EditAction::make()->label('Ubah'),
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
