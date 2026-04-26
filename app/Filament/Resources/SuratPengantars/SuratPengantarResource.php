<?php

namespace App\Filament\Resources\SuratPengantars;

// Hapus import Create dan Edit karena kita tidak butuh form input
use App\Filament\Resources\SuratPengantars\Pages\ListSuratPengantars;
use App\Filament\Resources\SuratPengantars\Tables\SuratPengantarsTable;
use App\Models\Dudika; // <--- MANTRA SAKTI: Kita pakai model Dudika
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use BackedEnum;
use Filament\Tables\Table;

class SuratPengantarResource extends Resource
{
    // Arahkan ke model Dudika
    protected static ?string $model = Dudika::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPrinter;

    protected static string | \UnitEnum | null $navigationGroup = 'Pencetakan';
    protected static ?string $navigationLabel = 'Surat Pengantar';
    protected static ?string $modelLabel = 'Surat Pengantar';
    protected static ?string $pluralModelLabel = 'Cetak Surat Pengantar';
    protected static ?int $navigationSort = 1;

    // =========================================================
    // MATIKAN SEMUA FITUR CRUD (Karena ini murni halaman cetak)
    // =========================================================
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
        // Kosongkan saja form-nya karena tidak bisa di-klik Edit/Create
        return $schema->schema([]);
    }

    public static function table(Table $table): Table
    {
        return SuratPengantarsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            // Cukup halaman index (List) saja
            'index' => ListSuratPengantars::route('/'),
        ];
    }
}
