<?php

namespace App\Filament\Resources\AssessmentSchemes;

use App\Filament\Resources\AssessmentSchemes\Pages\CreateAssessmentScheme;
use App\Filament\Resources\AssessmentSchemes\Pages\EditAssessmentScheme;
use App\Filament\Resources\AssessmentSchemes\Pages\ListAssessmentSchemes;
use App\Filament\Resources\AssessmentSchemes\Schemas\AssessmentSchemeForm;
use App\Filament\Resources\AssessmentSchemes\Tables\AssessmentSchemesTable;
use App\Models\AssessmentScheme;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AssessmentSchemeResource extends Resource
{
    protected static ?string $model = AssessmentScheme::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ClipboardDocumentList;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'Skema Penilaian';
    protected static ?string $modelLabel = 'Skema Penilaian';
    protected static ?string $pluralModelLabel = 'Skema Penilaian';

    protected static string | \UnitEnum | null $navigationGroup = 'Data Master';

    protected static ?int $navigationSort = 9;

    public static function form(Schema $schema): Schema
    {
        return AssessmentSchemeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AssessmentSchemesTable::configure($table);
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
            'index' => ListAssessmentSchemes::route('/'),
            'create' => CreateAssessmentScheme::route('/create'),
            'edit' => EditAssessmentScheme::route('/{record}/edit'),
        ];
    }
}
