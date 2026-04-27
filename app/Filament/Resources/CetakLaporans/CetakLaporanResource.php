<?php

namespace App\Filament\Resources\CetakLaporans;

use App\Filament\Resources\CetakLaporans\Pages\ListCetakLaporans;
use App\Filament\Resources\CetakLaporans\Tables\CetakLaporansTable;
use App\Models\PklPlacement; // <-- MANTRA SAKTI: Pakai model Penempatan
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CetakLaporanResource extends Resource
{
    protected static ?string $model = PklPlacement::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::DocumentDuplicate;

    protected static string | \UnitEnum | null $navigationGroup = 'Pencetakan';
    protected static ?string $navigationLabel = 'Laporan Siswa';
    protected static ?string $modelLabel = 'Laporan Siswa';
    protected static ?string $pluralModelLabel = 'Cetak Laporan Lengkap';
    protected static ?int $navigationSort = 2;

    // MATIKAN CRUD! Murni untuk lihat daftar dan cetak
    public static function canCreate(): bool
    {
        return false;
    }
    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }
    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([]);
    }

    public static function table(Table $table): Table
    {
        return CetakLaporansTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCetakLaporans::route('/'),
        ];
    }
}
