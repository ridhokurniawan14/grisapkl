<?php

namespace App\Filament\Resources\Teachers\Tables;

use App\Models\Teacher;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class TeachersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->poll('5s')
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Lengkap')
                    ->description(fn(Teacher $record): string => $record->title ?? '-') // Tampilkan gelar di bawah nama
                    ->searchable(),
                TextColumn::make('nip')
                    ->label('NIP')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('phone')
                    ->label('No. HP')
                    ->searchable(),
                TextColumn::make('subject')
                    ->label('Mata Pelajaran')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                ImageColumn::make('signature_path')
                    ->label('Tanda Tangan')
                    ->circular(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()->label('Detail'),
                    EditAction::make()->label('Ubah'),
                    DeleteAction::make()->label('Hapus'),

                    Action::make('reset_password')
                        ->label('Reset Password')
                        ->icon('heroicon-o-key')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(function (Teacher $record) {
                            if ($record->user) {
                                $fullPhone = preg_replace('/[^0-9]/', '', $record->phone ?? '12345678');
                                $record->user->update(['password' => bcrypt($fullPhone)]);
                                Notification::make()->title('Password direset ke 5 digit terakhir HP')->success()->send();
                            }
                        }),
                ])->label('Aksi')->button()->outlined(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->before(function (Collection $records) {
                            foreach ($records as $record) {
                                if ($record->user_id) {
                                    $user = User::find($record->user_id);
                                    if ($user) $user->delete();
                                }
                            }
                        }),
                    BulkAction::make('bulk_reset_password')
                        ->label('Reset Password Terpilih')
                        ->icon('heroicon-o-key')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                if ($record->user) {
                                    $lastFive = substr(preg_replace('/[^0-9]/', '', $record->phone ?? '12345'), -5);
                                    $record->user->update(['password' => bcrypt($lastFive)]);
                                }
                            });
                            Notification::make()->title('Password massal berhasil direset')->success()->send();
                        }),
                ]),
            ]);
    }
}
