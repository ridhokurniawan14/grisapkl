<?php

namespace App\Filament\Resources\Announcements\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AnnouncementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Pengumuman')
                    ->columnspanFull()
                    ->schema([
                        TextInput::make('title')
                            ->label('Judul Pengumuman')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        // FIELD BARU: Target Audience
                        Select::make('target_audience')
                            ->label('Ditujukan Kepada (Target)')
                            ->options([
                                'Umum' => 'Semua Pengguna (Umum)',
                                'Siswa' => 'Hanya Siswa PKL',
                                'Guru' => 'Hanya Guru Pembimbing',
                                'Dudika' => 'Hanya DUDIKA (Tempat PKL)',
                            ])
                            ->default('Umum')
                            ->required()
                            ->columnSpanFull(),

                        RichEditor::make('content')
                            ->label('Isi Pengumuman')
                            ->required()
                            ->toolbarButtons([
                                'blockquote',
                                'bold',
                                'bulletList',
                                'h2',
                                'h3',
                                'italic',
                                'link',
                                'orderedList',
                                'redo',
                                'strike',
                                'underline',
                                'undo',
                            ])
                            ->columnSpanFull(),

                        Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->helperText('Jika dimatikan, pengumuman ini tidak akan muncul di Dasbor siapapun.')
                            ->default(true),
                    ]),
            ]);
    }
}
