<?php

namespace App\Filament\Resources\Dudikas\Tables;

use App\Models\Dudika;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Illuminate\Database\Eloquent\Collection;
use Filament\Notifications\Notification;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class DudikasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->poll('5s')
            ->columns([
                TextColumn::make('name')
                    ->label('Nama DUDIKA')
                    ->searchable(),
                TextColumn::make('user.email')
                    ->label('Email / Username')
                    ->searchable()
                    ->icon('heroicon-m-envelope')
                    ->color('gray')
                    ->copyable()
                    ->copyMessage('Email berhasil disalin!')
                    ->copyMessageDuration(1500)
                    ->placeholder('Belum ada akun'),
                TextColumn::make('is_complete')
                    ->label('Status Data')
                    ->getStateUsing(fn($record) => $record->is_complete ? 'Lengkap' : 'Belum Lengkap')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Lengkap' => 'success', // Warna Hijau
                        'Belum Lengkap' => 'danger', // Warna Merah
                    }),
                TextColumn::make('supervisor_name')
                    ->label('Pembimbing')
                    ->searchable(),
                TextColumn::make('supervisor_phone')
                    ->label('No. HP Pembimbing')
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
                // Kolom di bawah ini kita hide default biar tabel nggak penuh
                TextColumn::make('head_name')
                    ->label('Pimpinan')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('head_nip')
                    ->label('NIP Pimpinan')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('supervisor_nip')
                    ->label('NIP Pembimbing')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                Action::make('print_pdf')
                    ->label('Cetak PDF')
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    ->url(fn() => route('dudika.print')) // Nanti kita buat route ini
                    ->openUrlInNewTab(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()->label('Lihat Detail'),
                    EditAction::make()->label('Ubah Data'),
                    DeleteAction::make()->label('Hapus'),

                    // Fitur Reset Password (Kunci 5 Digit Terakhir)
                    Action::make('reset_password')
                        ->label('Reset Password')
                        ->icon('heroicon-o-key')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(function (Dudika $record) {
                            if ($record->user) {
                                $lastFive = substr(preg_replace('/[^0-9]/', '', $record->supervisor_phone), -5);
                                $record->user->update(['password' => bcrypt($lastFive)]);
                                Notification::make()->title('Password berhasil direset ke 5 digit terakhir HP')->success()->send();
                            }
                        }),
                ])->label('Aksi')->button()->outlined(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // ✅ Cukup begini, bersih
                    DeleteBulkAction::make(),
                    BulkAction::make('bulk_reset_password')
                        ->label('Reset Password Terpilih')
                        ->icon('heroicon-o-key')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                if ($record->user) {
                                    $lastFive = substr(preg_replace('/[^0-9]/', '', $record->supervisor_phone), -5);
                                    $record->user->update(['password' => bcrypt($lastFive)]);
                                }
                            });
                            Notification::make()->title('Password massal berhasil direset')->success()->send();
                        }),
                    // CETAK PDF UNTUK DATA YANG DICENTANG SAJA
                    BulkAction::make('print_selected')
                        ->label('Cetak PDF Terpilih')
                        ->icon('heroicon-o-printer')
                        ->color('info')
                        // Hapus "\Illuminate\Database\Eloquent\Collection" dari dalam kurung
                        ->action(function ($records) {
                            $ids = $records->pluck('id')->join(',');
                            // Redirect ke halaman cetak
                            return redirect()->route('dudika.print', ['ids' => $ids]);
                        }),
                ]),

            ]);
    }
}
