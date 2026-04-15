<?php

namespace App\Filament\Resources\StudentClasses;

use App\Filament\Resources\StudentClasses\Pages\CreateStudentClass;
use App\Filament\Resources\StudentClasses\Pages\EditStudentClass;
use App\Filament\Resources\StudentClasses\Pages\ListStudentClasses;
use App\Filament\Resources\StudentClasses\Schemas\StudentClassForm;
use App\Filament\Resources\StudentClasses\Tables\StudentClassesTable;
use App\Models\StudentClass;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon; // Pastikan ini ada
use Filament\Tables\Table;

class StudentClassResource extends Resource
{
    protected static ?string $model = StudentClass::class;

    // Pakai icon sekumpulan orang (Users) cocok untuk Kelas
    protected static string|BackedEnum|null $navigationIcon = Heroicon::BuildingOffice2;

    protected static ?string $recordTitleAttribute = 'name';

    // Translasi ke Bahasa Indonesia
    protected static ?string $navigationLabel = 'Kelas';
    protected static ?string $modelLabel = 'Kelas';
    protected static ?string $pluralModelLabel = 'Kelas';

    // Grouping Menu biar rapi
    public static function getNavigationGroup(): ?string
    {
        return 'Data Master';
    }

    public static function form(Schema $schema): Schema
    {
        return StudentClassForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StudentClassesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStudentClasses::route('/'),
            'create' => CreateStudentClass::route('/create'),
            'edit' => EditStudentClass::route('/{record}/edit'),
        ];
    }
}
