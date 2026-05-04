<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;
use App\Models\User;
use BackedEnum;
use Filament\Support\Icons\Heroicon;
use Spatie\DbDumper\Databases\MySql;

class BackupDatabase extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::CircleStack;
    protected string $view = 'filament.pages.backup-database';

    // Taruh di sub menu Sistem
    protected static string | \UnitEnum | null $navigationGroup = 'Sistem';
    protected static ?string $navigationLabel = 'Backup & Reset DB';
    protected static ?string $title = 'Manajemen Database';
    protected static ?int $navigationSort = 5;

    protected function getHeaderActions(): array
    {
        return [
            // ==============================================================
            // TOMBOL 1: BACKUP DATABASE
            // ==============================================================
            Action::make('backup_db')
                ->label('Download Backup (.sql)')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    try {
                        $fileName = 'backup-grisa-' . date('Y-m-d-H-i-s') . '.sql';
                        $path = storage_path('app/public/' . $fileName);

                        MySql::create()
                            ->setDbName(env('DB_DATABASE'))
                            ->setUserName(env('DB_USERNAME'))
                            ->setPassword(env('DB_PASSWORD') ?? '')
                            // ->setDumpBinaryPath('C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin') // Buka komen ini jika error mysqldump tidak ditemukan (sesuaikan path MySQL Herd kamu)
                            ->dumpToFile($path);

                        Notification::make()
                            ->title('Backup Berhasil Dibuat!')
                            ->success()
                            ->send();

                        return response()->download($path)->deleteFileAfterSend(true);
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Gagal melakukan backup!')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            // ==============================================================
            // TOMBOL 3: RESTORE DATABASE DARI FILE .SQL
            // ==============================================================
            Action::make('restore_db')
                ->label('Restore dari Backup (.sql)')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('warning')
                ->form([
                    \Filament\Forms\Components\FileUpload::make('sql_file')
                        ->label('Upload File Backup (.sql)')
                        ->helperText('Hanya file .sql yang dihasilkan dari fitur backup ini yang didukung.')
                        ->acceptedFileTypes([
                            'text/plain',
                            'text/sql',
                            'text/x-sql',
                            'application/sql',
                            'application/x-sql',
                            'application/octet-stream'
                        ])
                        ->disk('local')
                        ->directory('restore-temp')
                        ->visibility('private')
                        ->required(),
                ])
                ->requiresConfirmation()
                ->modalHeading('⚠️ Restore Database dari File Backup?')
                ->modalDescription('Proses ini akan MENIMPA data yang ada sekarang dengan isi file .sql yang Anda upload. Pastikan file backup yang Anda pilih benar. Tindakan ini TIDAK BISA dibatalkan!')
                ->modalSubmitActionLabel('Ya, Restore Sekarang!')
                ->action(function (array $data) {
                    try {
                        $relativePath = is_array($data['sql_file'])
                            ? reset($data['sql_file'])
                            : $data['sql_file'];

                        // MANTRA SAKTI: Pakai Storage Facade, jangan file_exists manual!
                        if (!\Illuminate\Support\Facades\Storage::disk('local')->exists($relativePath)) {
                            throw new \Exception("File backup tidak ditemukan oleh Storage (Path: {$relativePath})");
                        }

                        // MANTRA SAKTI: Baca isinya pakai Storage::get()
                        $sql = \Illuminate\Support\Facades\Storage::disk('local')->get($relativePath);

                        if (empty(trim($sql))) {
                            throw new \Exception('File SQL kosong atau tidak valid.');
                        }

                        // Eksekusi dump SQL (Wajib pakai statement agar aman)
                        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                        DB::unprepared($sql);
                        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

                        // Bersihkan jejak: Hapus file dari server setelah sukses restore
                        \Illuminate\Support\Facades\Storage::disk('local')->delete($relativePath);

                        Notification::make()
                            ->title('Restore Berhasil!')
                            ->body('Database berhasil dipulihkan dari file backup dengan sempurna.')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        // Safety net: pastikan foreign key nyala lagi kalau terjadi error
                        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

                        // Bersihkan jejak meski gagal
                        if (isset($relativePath)) {
                            \Illuminate\Support\Facades\Storage::disk('local')->delete($relativePath);
                        }

                        Notification::make()
                            ->title('Restore Gagal!')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            // ==============================================================
            // TOMBOL 2: RESET DATABASE (WIPE ALL DATA)
            // ==============================================================
            Action::make('reset_db')
                ->label('Factory Reset (Wipe DB)')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('🔥 PERINGATAN KERAS: Reset Seluruh Database?')
                ->modalDescription('Apakah Anda yakin? Semua data Siswa, DUDIKA, Penempatan, Kunjungan, dan Pengumuman akan dihapus PERMANEN. Hanya akun Super Admin, Profil Sekolah, dan Pengaturan Hak Akses (Role) yang akan dipertahankan. Tindakan ini TIDAK BISA dibatalkan!')
                ->modalSubmitActionLabel('Ya, Hapus Semua Data!')
                ->action(function () {
                    try {
                        // 1. Matikan pengecekan Foreign Key agar tidak error saat dihapus
                        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

                        // 2. Ambil semua nama tabel di database
                        $tables = DB::select('SHOW TABLES');

                        // 3. Daftar tabel yang HARAM dihapus
                        $ignoredTables = [
                            'migrations',
                            'password_reset_tokens',
                            'failed_jobs',
                            'personal_access_tokens',
                            'roles',
                            'permissions',
                            'model_has_roles',
                            'model_has_permissions',
                            'role_has_permissions',
                            'users', // Di-handle khusus di bawah
                            'school_profiles' // Amankan logo sekolah bro!
                        ];

                        // 4. Bantai semua tabel yang tidak ada di daftar pengecualian
                        foreach ($tables as $table) {
                            $tableName = array_values((array)$table)[0];
                            if (!in_array($tableName, $ignoredTables)) {
                                DB::table($tableName)->truncate();
                            }
                        }

                        // 5. EKSEKUSI KHUSUS TABEL USERS (Hapus semua KECUALI super_admin)
                        User::whereDoesntHave('roles', function ($query) {
                            $query->where('name', 'super_admin');
                        })->delete();

                        // 6. Nyalakan kembali pengecekan Foreign Key
                        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

                        Notification::make()
                            ->title('Sistem Berhasil Direset!')
                            ->body('Database kini bersih layaknya baru di-install. Hanya Super Admin yang tersisa.')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        DB::statement('SET FOREIGN_KEY_CHECKS=1;'); // Safety net
                        Notification::make()
                            ->title('Gagal melakukan reset!')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

        ];
    }
}
