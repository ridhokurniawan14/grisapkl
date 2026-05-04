<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Pengguna')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Email disalin!'),

                // Tampilkan Role dengan Badge Keren
                TextColumn::make('roles.name')
                    ->label('Hak Akses')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'super_admin' => 'danger',
                        'admin' => 'warning',
                        'humas' => 'info',
                        'guru' => 'success',
                        'dudika' => 'primary',
                        'siswa' => 'gray',
                        default => 'gray',
                    })
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('Tgl. Didaftarkan')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('roles')
                    ->label('Filter Hak Akses')
                    ->relationship('roles', 'name')
                    ->preload(),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()->label('Ubah'),
                    DeleteAction::make()->label('Hapus'),
                ])->button()->outlined()->label('Aksi'),
            ]);
    }
}
