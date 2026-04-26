<?php

namespace App\Filament\Resources\SuratPengantars\Tables;

use App\Models\Dudika;
use App\Models\Major;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SuratPengantarsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            // =========================================================
            // BASE QUERY: Filter Dudika yang punya siswa aktif tahun ini
            // =========================================================
            ->modifyQueryUsing(function (Builder $query) {
                return $query->whereHas('pklPlacements', function ($q) {
                    $q->whereHas('academicYear', function ($ay) {
                        $ay->where('is_active', true);
                    })->where('status', 'Aktif');
                });
            })
            ->columns([
                TextColumn::make('name')
                    ->label('Nama DUDIKA')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('address')
                    ->label('Alamat')
                    ->wrap()
                    ->searchable(),

                TextColumn::make('supervisor_name')
                    ->label('Pembimbing DUDIKA')
                    ->searchable(),

                // REVISI: Angka ini sekarang dinamis mengikuti Filter Jurusan
                TextColumn::make('pkl_placements_count')
                    ->label('Jumlah Siswa Aktif')
                    ->badge()
                    ->color('info')
                    ->state(function (Dudika $record, $livewire) {
                        // Ambil nilai filter major_id yang sedang dipilih user
                        $filters = $livewire->tableFilters ?? [];
                        $majorId = $filters['major_id']['value'] ?? null;

                        $query = $record->pklPlacements()
                            ->whereHas('academicYear', fn($ay) => $ay->where('is_active', true))
                            ->where('status', 'Aktif');

                        // Jika filter jurusan aktif, hitung hanya siswa dari jurusan tsb
                        if ($majorId) {
                            $query->whereHas('student.studentClass', fn($q) => $q->where('major_id', $majorId));
                        }

                        return $query->count();
                    }),
            ])
            ->filters([
                // =========================================================
                // MANTRA SAKTI: FILTER JURUSAN
                // =========================================================
                SelectFilter::make('major_id')
                    ->label('Filter Jurusan Siswa')
                    ->options(Major::pluck('name', 'id'))
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['value'])) {
                            $query->whereHas('pklPlacements', function ($q) use ($data) {
                                $q->whereHas('student.studentClass', function ($sq) use ($data) {
                                    $sq->where('major_id', $data['value']);
                                })
                                    ->whereHas('academicYear', fn($ay) => $ay->where('is_active', true))
                                    ->where('status', 'Aktif');
                            });
                        }
                    }),
            ])
            ->recordActions([
                Action::make('cetak_surat')
                    ->label('Cetak Pengantar')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->button()
                    ->url(fn($record) => route('cetak.surat-pengantar', $record->id))
                    ->openUrlInNewTab(),
            ])
            ->toolbarActions([]);
    }
}
