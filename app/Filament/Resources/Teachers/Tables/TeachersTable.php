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
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\HtmlString;

class TeachersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->poll('5s')
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Lengkap')
                    ->description(fn(Teacher $record): string => $record->title ?? '-')
                    ->searchable(),
                TextColumn::make('nip')
                    ->label('NIP')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true), // NIP disembunyikan default
                TextColumn::make('user.email')
                    ->label('Email / Username')
                    ->searchable()
                    ->icon('heroicon-m-envelope')
                    ->color('gray')
                    ->copyable()
                    ->copyMessage('Email berhasil disalin!')
                    ->copyMessageDuration(1500)
                    ->placeholder('Belum ada akun'),
                TextColumn::make('phone')
                    ->label('No. HP / WA')
                    ->searchable()
                    ->icon('heroicon-m-chat-bubble-oval-left-ellipsis') // Ikon chat
                    ->color('success') // Warna hijau khas WA
                    ->url(function ($state) {
                        if (blank($state)) return null;

                        // Bersihkan semua karakter non-angka (seperti + atau spasi)
                        $phone = preg_replace('/[^0-9]/', '', $state);

                        // Jika nomor diawali dengan 0, ubah jadi 62
                        if (str_starts_with($phone, '0')) {
                            $phone = '62' . substr($phone, 1);
                        }

                        return "https://wa.me/{$phone}";
                    })
                    ->openUrlInNewTab(), // Buka di tab baru agar tidak keluar dari web
                TextColumn::make('subject')
                    ->label('Mata Pelajaran')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // TTD Persegi Panjang dengan Background Putih
                TextColumn::make('signature_path')
                    ->label('Tanda Tangan')
                    ->formatStateUsing(fn($state) => $state ? new HtmlString('<img src="' . $state . '" style="height: 40px; background-color: #ffffff; border-radius: 4px; padding: 2px; border: 1px solid #ccc;" />') : '-')
                    ->html(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()->label('Detail'),
                    EditAction::make()->label('Ubah'),
                    DeleteAction::make()->label('Hapus'),

                    // Reset ke 5 Digit Terakhir
                    Action::make('reset_password')
                        ->label('Reset Password')
                        ->icon('heroicon-o-key')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(function (Teacher $record) {
                            if ($record->user) {
                                $phone = preg_replace('/[^0-9]/', '', $record->phone ?? '12345');
                                $lastFive = substr($phone, -5);
                                if (strlen($lastFive) < 5) $lastFive = '12345'; // Fallback

                                $record->user->update(['password' => bcrypt($lastFive)]);
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

                    // Bulk Reset ke 5 Digit Terakhir
                    BulkAction::make('bulk_reset_password')
                        ->label('Reset Password Terpilih')
                        ->icon('heroicon-o-key')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                if ($record->user) {
                                    $phone = preg_replace('/[^0-9]/', '', $record->phone ?? '12345');
                                    $lastFive = substr($phone, -5);
                                    if (strlen($lastFive) < 5) $lastFive = '12345';

                                    $record->user->update(['password' => bcrypt($lastFive)]);
                                }
                            });
                            Notification::make()->title('Password massal berhasil direset')->success()->send();
                        }),
                ]),
            ]);
    }
}
