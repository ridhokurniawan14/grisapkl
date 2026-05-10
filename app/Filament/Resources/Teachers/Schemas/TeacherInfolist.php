<?php

namespace App\Filament\Resources\Teachers\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class TeacherInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->label('Nama Lengkap'),
                TextEntry::make('title')
                    ->label('Gelar')
                    ->placeholder('-'),
                TextEntry::make('nip')
                    ->label('NIP')
                    ->placeholder('-'),
                TextEntry::make('phone')
                    ->label('No. HP')
                    ->placeholder('-'),
                TextEntry::make('subject')
                    ->label('Mata Pelajaran')
                    ->placeholder('-'),

                TextEntry::make('signature_path')
                    ->label('Tanda Tangan')
                    ->formatStateUsing(function ($state) {

                        if (!$state) {
                            return '-';
                        }

                        $src = str_starts_with($state, 'data:image')
                            ? $state
                            : asset('storage/' . $state);

                        return new HtmlString("
            <img 
                src='{$src}'
                style='
                    height:100px;
                    background:#fff;
                    border-radius:8px;
                    padding:5px;
                    border:1px solid #ccc;
                    object-fit:contain;
                '
            />
        ");
                    })
                    ->html(),

                TextEntry::make('user.email')
                    ->label('Email / Username')
                    ->icon('heroicon-m-envelope')
                    ->color('gray')
                    ->copyable()
                    ->copyMessage('Email berhasil disalin!')
                    ->copyMessageDuration(1500),

                TextEntry::make('created_at')
                    ->label('Tanggal Terdaftar')
                    ->dateTime(),
            ]);
    }
}
