<?php

namespace App\Filament\Resources\Students\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StudentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Biodata Diri')
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('name')->label('Nama Lengkap Siswa'),
                            TextEntry::make('nis')->label('NIS (Nomor Induk Siswa)'),
                            TextEntry::make('nisn')->label('NISN')->placeholder('-'),
                            TextEntry::make('gender')
                                ->label('Jenis Kelamin')
                                ->formatStateUsing(fn($state) => $state === 'L' ? 'Laki-laki' : 'Perempuan')
                                ->badge()
                                ->color(fn($state) => $state === 'L' ? 'info' : 'danger'),
                            TextEntry::make('studentClass.name')->label('Kelas')->badge()->color('gray'),
                            TextEntry::make('academicYear.name')->label('Tahun Ajaran')->badge()->color('success'),
                            TextEntry::make('birth_place')->label('Tempat Lahir')->placeholder('-'),
                            TextEntry::make('birth_date')->label('Tanggal Lahir')->date('d F Y')->placeholder('-'),
                            TextEntry::make('religion')->label('Agama')->placeholder('-'),

                            // HP SISWA BISA DI KLIK
                            TextEntry::make('phone')
                                ->label('No. HP / WA Siswa')
                                ->icon('heroicon-m-chat-bubble-oval-left-ellipsis')
                                ->color('success')
                                ->url(function ($state) {
                                    if (blank($state)) return null;
                                    $phone = preg_replace('/[^0-9]/', '', $state);
                                    if (str_starts_with($phone, '0')) $phone = '62' . substr($phone, 1);
                                    return "https://wa.me/{$phone}";
                                })
                                ->openUrlInNewTab()
                                ->placeholder('-'),
                        ]),
                        TextEntry::make('address')
                            ->label('Alamat Lengkap')
                            ->columnSpanFull()
                            ->placeholder('-'),
                    ]),

                Section::make('Data Orang Tua / Wali')
                    ->collapsed()
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('father_name')->label('Nama Ayah')->placeholder('-'),
                            TextEntry::make('father_job')->label('Pekerjaan Ayah')->placeholder('-'),
                            TextEntry::make('mother_name')->label('Nama Ibu')->placeholder('-'),
                            TextEntry::make('mother_job')->label('Pekerjaan Ibu')->placeholder('-'),

                            // HP ORANG TUA BISA DI KLIK
                            TextEntry::make('parent_phone')
                                ->label('No. HP / WA Orang Tua')
                                ->icon('heroicon-m-chat-bubble-oval-left-ellipsis')
                                ->color('success')
                                ->url(function ($state) {
                                    if (blank($state)) return null;
                                    $phone = preg_replace('/[^0-9]/', '', $state);
                                    if (str_starts_with($phone, '0')) $phone = '62' . substr($phone, 1);
                                    return "https://wa.me/{$phone}";
                                })
                                ->openUrlInNewTab()
                                ->placeholder('-'),
                        ]),
                        TextEntry::make('parent_address')
                            ->label('Alamat Orang Tua')
                            ->columnSpanFull()
                            ->placeholder('-'),
                    ]),
            ]);
    }
}
