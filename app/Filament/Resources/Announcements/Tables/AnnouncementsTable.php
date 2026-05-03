<?php

namespace App\Filament\Resources\Announcements\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AnnouncementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('target_audience')
                    ->label('Target Sasaran')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Umum' => 'gray',
                        'Siswa' => 'info',
                        'Guru' => 'success',
                        'Dudika' => 'warning',
                        default => 'gray',
                    }),

                IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->action(function ($record, $column) {
                        $name = $column->getName();
                        $record->update([
                            $name => !$record->$name
                        ]);
                    }),

                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    // ==============================================================
                    // MANTRA SAKTI: VIEW ACTION DENGAN INFOLIST (POP-UP CANTIK)
                    // ==============================================================
                    ViewAction::make()
                        ->label('Lihat')
                        ->modalHeading('Detail Pengumuman')
                        ->modalWidth('2xl') // Bikin modalnya agak lebar biar enak dibaca
                        ->modalSubmitAction(false) // Hilangkan tombol submit karena cuma lihat
                        ->modalCancelActionLabel('Tutup')
                        ->infolist([
                            Section::make()
                                ->schema([
                                    TextEntry::make('title')
                                        ->label('Judul Pengumuman')
                                        ->weight('bold')
                                        ->size('lg')
                                        ->columnSpanFull(),

                                    TextEntry::make('target_audience')
                                        ->label('Ditujukan Kepada')
                                        ->badge()
                                        ->color(fn(string $state): string => match ($state) {
                                            'Umum' => 'gray',
                                            'Siswa' => 'info',
                                            'Guru' => 'success',
                                            'Dudika' => 'warning',
                                            default => 'gray',
                                        }),

                                    TextEntry::make('is_active')
                                        ->label('Status')
                                        ->badge()
                                        ->color(fn(bool $state): string => $state ? 'success' : 'danger')
                                        ->formatStateUsing(fn(bool $state): string => $state ? 'Aktif Tayang' : 'Disembunyikan'),

                                    // html() wajib dipakai agar tag RichEditor dirender jadi tampilan web asli, bukan kode
                                    TextEntry::make('content')
                                        ->label('Isi Pengumuman')
                                        ->html()
                                        ->columnSpanFull(),
                                ])->columns(2)
                        ]),

                    EditAction::make()->label('Ubah'),
                    DeleteAction::make()->label('Hapus'),
                ])->button()->outlined()->label('Aksi'),
            ]);
    }
}
