<?php

namespace App\Filament\Widgets;

use App\Models\Journal;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use Filament\Actions\Action;

class IzinSakitWidget extends BaseWidget
{
    protected static ?string $heading = 'Siswa Izin & Sakit';
    protected static ?int $sort = 6;

    // MANTRA SAKTI 1: Buat lebar widget menjadi penuh (Full Width)
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Journal::query()
                    ->whereIn('attend_status', ['Izin', 'Sakit'])
                    ->whereHas('pklPlacement', function ($q) {
                        $q->where('status', 'Aktif')
                            ->whereHas('academicYear', fn($ay) => $ay->where('is_active', true));
                    })
            )
            ->modifyQueryUsing(function (Builder $query) {
                // Kunci filter tanggal ke Hari Ini secara default
                $date = $this->tableFilters['tanggal']['date'] ?? Carbon::today()->format('Y-m-d');
                $query->whereDate('date', $date);
            })
            ->description('Menampilkan siswa Izin & Sakit HARI INI. Klik pada foto untuk memperbesar gambar surat/bukti.')
            ->columns([
                Tables\Columns\TextColumn::make('pklPlacement.student.name')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\BadgeColumn::make('attend_status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'Izin',
                        'danger'  => 'Sakit',
                    ]),

                Tables\Columns\TextColumn::make('pklPlacement.dudika.name')
                    ->label('Tempat PKL')
                    ->searchable()
                    ->limit(30)
                    ->wrap(),

                Tables\Columns\TextColumn::make('activity')
                    ->label('Keterangan')
                    ->limit(40)
                    ->wrap(),

                // MANTRA SAKTI 2: Kolom Foto dengan Fitur Klik -> Modal Zoom
                Tables\Columns\ImageColumn::make('photo_path')
                    ->label('Foto Surat / Bukti')
                    ->disk('public')
                    ->circular()
                    ->action(
                        Action::make('lihat_foto_bukti')
                            ->modalHeading('Foto Surat Keterangan / Bukti')
                            ->modalWidth('3xl')
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
                                        'style' => 'max-height: 70vh; object-fit: contain; margin: 0 auto;'
                                    ])
                            ])
                    ),
            ])
            ->filters([
                Filter::make('tanggal')
                    ->form([
                        DatePicker::make('date')
                            ->label('Cek Tanggal Mundur')
                            ->native(false)
                            ->closeOnDateSelection(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query;
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (!empty($data['date'])) {
                            return 'Cek Data: ' . Carbon::parse($data['date'])->isoFormat('dddd, D MMMM YYYY');
                        }
                        return null;
                    }),
            ])
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(5)
            ->striped();
    }
}
