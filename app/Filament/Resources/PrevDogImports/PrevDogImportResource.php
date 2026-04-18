<?php

namespace App\Filament\Resources\PrevDogImports;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ImportAction;
use Filament\Actions\ExportAction;
use App\Filament\Resources\PrevDogImports\Pages\ListPrevDogImports;
use App\Filament\Resources\PrevDogImports\Pages\CreatePrevDogImport;
use App\Filament\Resources\PrevDogImports\Pages\ViewPrevDogImport;
use App\Filament\Resources\PrevDogImports\Pages\EditPrevDogImport;
use App\Filament\Exports\PrevDogImportExporter;
use App\Filament\Imports\PrevDogImportImporter;
use App\Filament\Resources\PrevDogImports\Pages;
use App\Models\PrevDogImport;
use App\Models\PrevUser;
use Filament\Forms;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Icons\Heroicon;

class PrevDogImportResource extends Resource
{
    protected static ?string $model = PrevDogImport::class;

    protected static string | \BackedEnum | null $navigationIcon = Heroicon::OutlinedArrowDownTray;

    protected static ?string $recordTitleAttribute = 'dog_name';

    protected static ?int $navigationSort = 26;

    public static function getModelLabel(): string
    {
        return __('Imported Dog');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Imported Dogs');
    }

    public static function getNavigationGroup(): string
    {
        return __('dog/model/general.labels.navigation_group');
    }

    public static function getNavigationLabel(): string
    {
        return __('Imported Dogs');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Dog Details'))
                    ->schema([
                        TextInput::make('dog_name')->label(__('Dog name'))->required()->maxLength(255),
                        TextInput::make('dog_import_sagir')->label(__('Import Number'))->numeric(),
                        DatePicker::make('dog_birth_date')->label(__('Birth Date')),
                        TextInput::make('dog_breed')->label(__('Breed'))->maxLength(255),
                        TextInput::make('dog_hair_type')->label(__('Hair'))->maxLength(255),
                        TextInput::make('dog_hair_color')->label(__('Color'))->maxLength(255),
                        TextInput::make('dog_gender')->label(__('Gender'))->maxLength(50),
                        TextInput::make('dog_sagir_prefix')->label(__('Sagir Prefix'))->maxLength(50),
                        TextInput::make('dog_chip')->label(__('Chip'))->maxLength(255),
                        TextInput::make('dog_dna')->label(__('DNA'))->maxLength(255),
                        TextInput::make('dog_type')->label(__('Dog Type'))->maxLength(255),
                        TextInput::make('dog_sagir_id')->label(__('Sagir'))->numeric(),
                        Textarea::make('dog_tests')->label(__('Tests'))->rows(3)->columnSpanFull(),
                        Textarea::make('dog_titles')->label(__('Titles'))->rows(3)->columnSpanFull(),
                        Textarea::make('dog_notes')->label(__('Notes'))->rows(4)->columnSpanFull(),
                    ])
                    ->columns(4),
                Section::make(__('Breeder and owner details'))
                    ->schema([
                        TextInput::make('dog_breeder_name')->label(__('Breeder Name'))->maxLength(255),
                        TextInput::make('Foreign_Breeder_name')->label(__('Foreign Breeder'))->maxLength(255),
                        Select::make('user_id')
                            ->label(__('Done By'))
                            ->searchable()
                            ->getSearchResultsUsing(fn(string $search): array => PrevUser::selectOptions($search, 50))
                            ->getOptionLabelUsing(fn($value): ?string => PrevUser::query()->find($value)?->name),
                        TextInput::make('dog_owner_fname')->label(__('First Name'))->maxLength(255),
                        TextInput::make('dog_owner_lname')->label(__('Last Name'))->maxLength(255),
                        TextInput::make('dog_owner_email')->label(__('Owner Email'))->email()->maxLength(255),
                        TextInput::make('dog_mobile_phone_code')->label(__('Mobile Prefix'))->maxLength(20),
                        TextInput::make('dog_mobile_phone')->label(__('Mobile Phone'))->tel()->maxLength(255),
                        TextInput::make('dog_owner_phone')->label(__('Owner Phone'))->tel()->maxLength(255),
                        TextInput::make('dog_country_id')->label(__('Country ID'))->numeric(),
                        TextInput::make('dog_owner_fname_2')->label(__('Owner 2 First Name'))->maxLength(255),
                        TextInput::make('dog_owner_lname_2')->label(__('Owner 2 Last Name'))->maxLength(255),
                        TextInput::make('dog_owner_email_2')->label(__('Owner 2 Email'))->email()->maxLength(255),
                        TextInput::make('dog_mobile_phone_code_2')->label(__('Owner 2 Mobile Prefix'))->maxLength(20),
                        TextInput::make('dog_mobile_phone_2')->label(__('Owner 2 Mobile Phone'))->tel()->maxLength(255),
                        TextInput::make('dog_owner_phone_2')->label(__('Owner 2 Phone'))->tel()->maxLength(255),
                        TextInput::make('dog_country_id_2')->label(__('Owner 2 Country ID'))->numeric(),
                        TextInput::make('dog_owner_fname_3')->label(__('Owner 3 First Name'))->maxLength(255),
                        TextInput::make('dog_owner_lname_3')->label(__('Owner 3 Last Name'))->maxLength(255),
                        TextInput::make('dog_owner_email_3')->label(__('Owner 3 Email'))->email()->maxLength(255),
                        TextInput::make('dog_mobile_phone_code_3')->label(__('Owner 3 Mobile Prefix'))->maxLength(20),
                        TextInput::make('dog_mobile_phone_3')->label(__('Owner 3 Mobile Phone'))->tel()->maxLength(255),
                        TextInput::make('dog_owner_phone_3')->label(__('Owner 3 Phone'))->tel()->maxLength(255),
                        TextInput::make('dog_country_id_3')->label(__('Owner 3 Country ID'))->numeric(),
                    ])
                    ->columns(4),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label(__('ID'))->numeric(decimalPlaces: 0, thousandsSeparator: '')->sortable(),
                TextColumn::make('dog_name')
                    ->label(__('Dog name'))
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->sortable(),
                TextColumn::make('dog_import_sagir')
                    ->label(__('Import Number'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->sortable(),
                TextColumn::make('breed.BreedName')
                    ->label(__('Breed'))
                    ->searchable(['BreedName', 'BreedNameEN', 'BreedCode'], isIndividual: true, isGlobal: false)
                    ->toggleable(),
                TextColumn::make('color.ColorNameHE')
                    ->label(__('Color'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('dog_gender')
                    ->label(__('Gender'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('dog_chip')
                    ->label(__('Chip'))
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(),
                TextColumn::make('dog_tests')
                    ->label(__('Tests'))
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(),
                TextColumn::make('dog_titles')
                    ->label(__('Titles'))
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(),
                TextColumn::make('dog_type')
                    ->label(__('Type'))
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(),
                TextColumn::make('dog.full_name')
                    ->label(__('Main Dog'))
                    ->description(fn(PrevDogImport $record) => $record->dog_sagir_id ?? '-')
                    ->sortable(['SagirID'])
                    ->searchable(['SagirID', 'Eng_Name', 'Heb_Name'], isIndividual: true, isGlobal: false)
                    ->toggleable(),
                TextColumn::make('user.name')
                    ->label(__('Done By'))
                    ->sortable(['last_name', 'first_name'])
                    ->searchable(['first_name', 'last_name', 'first_name_en', 'last_name_en'], isIndividual: true, isGlobal: false)
                    ->toggleable(),
                TextColumn::make('created_at')->label(__('Created At'))->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')->label(__('Deleted at'))->since()->dateTimeTooltip()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                ExportBulkAction::make()
                    ->label(__('Export Selected'))
                    ->icon('fas-file-export')
                    ->color('primary')
                    ->iconPosition('after')
                    ->exporter(PrevDogImportExporter::class),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->headerActions([
                ImportAction::make()
                    ->label(__('Import'))
                    ->icon('fas-file-import')
                    ->color('gray')
                    ->iconPosition('after')
                    ->importer(PrevDogImportImporter::class),
                ExportAction::make()
                    ->label(__('Export All'))
                    ->icon('fas-file-export')
                    ->color('primary')
                    ->iconPosition('after')
                    ->exporter(PrevDogImportExporter::class),
            ])
            ->defaultSort('created_at', 'desc')
            ->searchOnBlur()
            ->striped();
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Dog Details'))
                    ->schema([
                        TextEntry::make('dog_name')->label(__('Dog name')),
                        TextEntry::make('dog_import_sagir')->label(__('Import Number')),
                        TextEntry::make('dog_birth_date')->label(__('Birth Date'))->date()->placeholder('-'),
                        TextEntry::make('breed.BreedName')->label(__('Breed')),
                        TextEntry::make('dog_hair_type')->label(__('Hair')),
                        TextEntry::make('color.ColorNameHE')->label(__('Color')),
                        TextEntry::make('dog_gender')->label(__('Gender')),
                        TextEntry::make('dog_sagir_prefix')->label(__('Sagir Prefix')),
                        TextEntry::make('dog_chip')->label(__('Chip')),
                        TextEntry::make('dog_dna')->label(__('DNA')),
                        TextEntry::make('dog_type')->label(__('Dog Type')),
                        TextEntry::make('dog.full_name')->label(__('Main Dog')),
                        TextEntry::make('dog_tests')->label(__('Tests'))->columnSpanFull(),
                        TextEntry::make('dog_titles')->label(__('Titles'))->columnSpanFull(),
                        TextEntry::make('dog_notes')->label(__('Notes'))->columnSpanFull(),
                    ])
                    ->columns(4),
                Section::make(__('Owner and breeder details'))
                    ->schema([
                        TextEntry::make('user.name')->label(__('Done By')),
                        TextEntry::make('dog_breeder_name')->label(__('Breeder Name')),
                        TextEntry::make('Foreign_Breeder_name')->label(__('Foreign Breeder')),
                        TextEntry::make('dog_owner_fname')->label(__('First Name')),
                        TextEntry::make('dog_owner_lname')->label(__('Last Name')),
                        TextEntry::make('dog_owner_email')->label(__('Owner Email')),
                        TextEntry::make('dog_mobile_phone')->label(__('Mobile Phone')),
                        TextEntry::make('dog_owner_phone')->label(__('Owner Phone')),
                        TextEntry::make('deleted_at')->label(__('Deleted at'))->since()->dateTimeTooltip()->placeholder('-'),
                    ])
                    ->columns(3),
            ]);
    }

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPrevDogImports::route('/'),
            'create' => CreatePrevDogImport::route('/create'),
            'view' => ViewPrevDogImport::route('/{record}'),
            'edit' => EditPrevDogImport::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
