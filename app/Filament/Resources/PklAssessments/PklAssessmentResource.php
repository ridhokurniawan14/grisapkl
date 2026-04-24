<?php

namespace App\Filament\Resources\PklAssessments;

use App\Filament\Resources\PklAssessments\Pages\CreatePklAssessment;
use App\Filament\Resources\PklAssessments\Pages\EditPklAssessment;
use App\Filament\Resources\PklAssessments\Pages\ListPklAssessments;
use App\Filament\Resources\PklAssessments\Schemas\PklAssessmentForm;
use App\Filament\Resources\PklAssessments\Tables\PklAssessmentsTable;
use App\Models\PklAssessment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PklAssessmentResource extends Resource
{
    protected static ?string $model = PklAssessment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ClipboardDocumentCheck;

    protected static ?string $navigationLabel = 'Penilaian PKL';
    protected static ?string $modelLabel = 'Penilaian PKL';
    protected static ?string $pluralModelLabel = 'Data Penilaian PKL';
    protected static string | \UnitEnum | null $navigationGroup = 'Data PKL';
    protected static ?int $navigationSort = 11;

    public static function form(Schema $schema): Schema
    {
        return PklAssessmentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PklAssessmentsTable::configure($table);
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
            'index' => ListPklAssessments::route('/'),
            'create' => CreatePklAssessment::route('/create'),
            'edit' => EditPklAssessment::route('/{record}/edit'),
        ];
    }
}
