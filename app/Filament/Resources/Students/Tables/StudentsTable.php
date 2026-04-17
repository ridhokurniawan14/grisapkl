<?php

namespace App\Filament\Resources\Students\Tables;

use App\Models\Student;
use App\Models\AcademicYear;
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
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class StudentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->poll('5s')
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('rowIndex')
                    ->label('No')
                    ->rowIndex(),
                TextColumn::make('name')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nis')
                    ->label('NIS')
                    ->searchable(),
                TextColumn::make('gender')
                    ->label('Jenis Kelamin')
                    ->formatStateUsing(fn($state) => $state === 'L' ? 'Laki-laki' : 'Perempuan')
                    ->badge()
                    ->color(fn($state) => $state === 'L' ? 'info' : 'danger'),
                TextColumn::make('studentClass.name')
                    ->label('Kelas')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray'),
                TextColumn::make('phone')
                    ->label('No. HP Siswa')
                    ->searchable()
                    ->icon('heroicon-m-chat-bubble-oval-left-ellipsis')
                    ->color('success')
                    ->url(function ($state) {
                        if (blank($state)) return null;
                        $phone = preg_replace('/[^0-9]/', '', $state);
                        if (str_starts_with($phone, '0')) $phone = '62' . substr($phone, 1);
                        return "https://wa.me/{$phone}";
                    })
                    ->openUrlInNewTab(),
                TextColumn::make('is_complete')
                    ->label('Status Data')
                    ->getStateUsing(fn($record) => $record->is_complete ? 'Lengkap' : 'Belum Lengkap')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Lengkap' => 'success',
                        'Belum Lengkap' => 'danger',
                    }),
                ToggleColumn::make('is_active')
                    ->label('Akses Sistem')
                    ->onColor('success')
                    ->offColor('danger')
                    ->onIcon('heroicon-m-check') // Icon Centang saat nyala
                    ->offIcon('heroicon-m-x-mark') // Icon Silang saat mati
                    ->tooltip('Geser untuk membuka/menutup akses login siswa'),
            ])
            ->filters([
                // FILTER KELAS (Revisi 3)
                SelectFilter::make('student_class_id')
                    ->label('Filter Kelas')
                    ->relationship('studentClass', 'name')
                    ->searchable()
                    ->preload(),

                // FILTER TAHUN AJARAN (Default ke yang aktif)
                SelectFilter::make('academic_year_id')
                    ->label('Filter Tahun Ajaran')
                    ->relationship('academicYear', 'name')
                    ->default(fn() => AcademicYear::where('is_active', true)->value('id')),
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
                        ->action(function (Student $record) {
                            if ($record->user) {
                                $record->user->update(['password' => bcrypt($record->nis)]);
                                Notification::make()->title("Password direset menjadi NIS: {$record->nis}")->success()->send();
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

                    // BULK RESET PASSWORD
                    BulkAction::make('bulk_reset_password')
                        ->label('Reset Password Terpilih')
                        ->icon('heroicon-o-key')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                if ($record->user) {
                                    $record->user->update(['password' => bcrypt($record->nis)]);
                                }
                            });
                            Notification::make()->title('Password massal berhasil direset ke NIS')->success()->send();
                        }),

                    // BULK NONAKTIFKAN AKUN
                    BulkAction::make('bulk_deactivate')
                        ->label('Nonaktifkan Terpilih')
                        ->icon('heroicon-o-lock-closed')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each->update(['is_active' => false]);
                            Notification::make()->title('Akun terpilih berhasil dinonaktifkan')->success()->send();
                        }),

                    // BULK AKTIFKAN AKUN (Bonus bro, biar gampang balikinnya)
                    BulkAction::make('bulk_activate')
                        ->label('Aktifkan Terpilih')
                        ->icon('heroicon-o-lock-open')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each->update(['is_active' => true]);
                            Notification::make()->title('Akun terpilih berhasil diaktifkan')->success()->send();
                        }),
                ]),
            ]);
    }
}
