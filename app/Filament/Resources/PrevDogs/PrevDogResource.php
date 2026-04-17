<?php

namespace App\Filament\Resources\PrevDogs;

use App\Enums\Legacy\LegacyDogGender;
use App\Enums\Legacy\LegacyDogSize;
use App\Enums\Legacy\LegacyDogStatus;
use App\Enums\Legacy\LegacyPedigreeColor;
use App\Enums\Legacy\LegacySagirPrefix;
use App\Filament\Exports\PrevDogExporter;
use App\Filament\Resources\PrevDogs\Pages\CreatePrevDog;
use App\Filament\Resources\PrevDogs\Pages\EditPrevDog;
use App\Filament\Resources\PrevDogs\Pages\ListPrevDogs;
use App\Filament\Resources\PrevDogs\Pages\ManagePedigree;
use App\Filament\Resources\PrevDogs\Pages\ViewPrevDog;
use App\Filament\Resources\PrevDogs\RelationManagers\ChildrenRelationManager;
use App\Filament\Resources\PrevDogs\RelationManagers\FemaleBreedingsRelationManager;
use App\Filament\Resources\PrevDogs\RelationManagers\HealthRecordsRelationManager;
use App\Filament\Resources\PrevDogs\RelationManagers\MaleBreedingsRelationManager;
use App\Filament\Resources\PrevDogs\RelationManagers\OwnersRelationManager;
use App\Filament\Resources\PrevDogs\RelationManagers\PaymentsRelationManager;
use App\Filament\Resources\PrevDogs\RelationManagers\PrevDogDocumentRelationManager;
use App\Filament\Resources\PrevDogs\RelationManagers\ShowDogsRelationManager;
use App\Filament\Resources\PrevDogs\RelationManagers\TitlesRelationManager;
use App\Filament\Resources\PrevDogs\RelationManagers\UserRequestsRelationManager;
use App\Filament\Resources\PrevDogs\Widgets\DogStats;
use App\Livewire\Legacy\Pedigree\PedigreeTree;
use App\Models\PrevBreed;
use App\Models\PrevColor;
use App\Models\PrevDog;
use App\Models\PrevHair;
use App\Models\PrevUser;
use App\Services\Legacy\PrevDogService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\IconSize;
use Filament\Support\Enums\TextSize;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;

// use Filament\Tables\Filters\QueryBuilder;
// use Filament\Tables\Filters\SelectFilter;
// use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
// use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
// use Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint;
// use Filament\Tables\Filters\QueryBuilder\Constraints\BooleanConstraint;
// use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
// use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
// use Filament\Support\Facades\FilamentColor;
// use App\Filament\Resources\PrevDogs\RelationManagers;
// infolist class
// use Filament\Infolists\Components\KeyValueEntry;

class PrevDogResource extends Resource
{
    protected static ?string $model = PrevDog::class;

    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'prev-dogs';

    protected static string|\BackedEnum|null $navigationIcon = 'fas-paw';

    //    protected static ?string $recordRouteKeyName = 'SagirID';

    //    public static function getNavigationBadge(): ?string
    //    {
    //        return (string) static::$model::count();
    //    }

    public static function getModelLabel(): string
    {
        return __('dog/model/general.labels.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('dog/model/general.labels.plural');
    }

    public static function getNavigationGroup(): string
    {
        return __('dog/model/general.labels.navigation_group');
    }

    public static function getNavigationLabel(): string
    {
        return __('Studbook');
    }

    protected static ?string $recordTitleAttribute = 'Heb_Name';

    public static function getGlobalSearchResultTitle(Model $record): Htmlable|string
    {
        return $record->full_name;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['Heb_Name', 'Eng_Name', 'SagirID', 'ImportNumber', 'Chip'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Sagir' => $record->SagirID,
            'Import' => $record->ImportNumber,
            'Breed' => $record->breed->BreedName,
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['breed']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                Tabs::make('prevDogFormTabs')
                    ->tabs([
                        Tab::make('general')
                            ->schema([
                                Section::make('identity')
                                    ->schema([
                                        Hidden::make('sagir_prefix'),
                                        TextInput::make('SagirID')
                                            ->label(__('Sagir'))
                                            ->numeric()
                                            ->extraAttributes(fn (Model $record): array => ['class' => 'dark:disabled:text-white fi-form-sagir fi-form-sagir-'.$record->sagir_prefix?->getColor()])
                                            ->prefix(fn (Model $record): HtmlString => $record->sagir_prefix?->code() ? new HtmlString('<span class="fi-form-sagir">'.$record->sagir_prefix?->code().'</span>') : new HtmlString('<span>---</span>'))
                                            ->suffixAction(
                                                Action::make('setPrefix')
                                                    ->label(__('Prefix'))
                                                    ->icon('fas-chevron-circle-down')
                                                    ->color(fn (Model $record): string => 'white')
                                                    ->modalHeading(__('Select SAGIR Prefix'))
                                                    ->schema([
                                                        Select::make('prefix')
                                                            ->label(__('Sagir Prefix'))
                                                            ->options(LegacySagirPrefix::class)
                                                            ->required(),
                                                    ])
                                                    ->action(function (array $data, Get $get, Set $set): void {
                                                        $set('sagir_prefix', (int) $data['prefix']);
                                                    })
                                            )
                                            ->disabled(),
                                        TextInput::make('Heb_Name')
                                            ->label(__('Hebrew Name'))
                                            ->maxLength(200),
                                        TextInput::make('Eng_Name')
                                            ->label(__('English Name'))
                                            ->maxLength(200),
                                        ToggleButtons::make('GenderID')
                                            ->label(__('Gender'))
                                            ->grouped()
                                            ->options(LegacyDogGender::class),
                                        DatePicker::make('BirthDate')
                                            ->label(__('Birth Date'))
                                            ->timezone('Asia/Jerusalem')
                                            ->native(false)
                                            ->locale('he')
                                            ->format('Y-m-d')
                                            ->displayFormat('Y-m-d')
                                            ->weekStartsOnSunday()
                                            ->closeOnDateSelection(),
                                        DatePicker::make('RegDate')
                                            ->label(__('Registration Date'))
                                            ->timezone('Asia/Jerusalem')
                                            ->native(false)
                                            ->locale('he')
                                            ->format('Y-m-d')
                                            ->displayFormat('Y-m-d')
                                            ->weekStartsOnSunday()
                                            ->closeOnDateSelection(),
                                        TextInput::make('Chip')
                                            ->label(__('Chip'))
                                            ->maxLength(200),
                                        TextInput::make('DnaID')
                                            ->label(__('DNA'))
                                            ->maxLength(200),
                                        TextInput::make('ImportNumber')
                                            ->label(__('Import Number'))
                                            ->maxLength(200),
                                        TextInput::make('Chip_2')
                                            ->label(__('Chip 2'))
                                            ->maxLength(255),
                                        ToggleButtons::make('Status')
                                            ->label(__('Status'))
                                            ->options(LegacyDogStatus::class)
                                            ->grouped()
                                            ->nullable()
                                            ->columnSpanFull(),
                                    ])
                                    ->heading(__('Identity'))
                                    ->columns(4),
                                Section::make('breed_appearance')
                                    ->schema([
                                        Select::make('RaceID')
                                            ->label(__('Breed'))
                                            ->relationship('breed', 'BreedName')
                                            ->searchable()
                                            ->required(),
                                        Select::make('ColorID')
                                            ->label(__('Color'))
                                            ->relationship('color', 'ColorNameHE')
                                            ->searchable()
                                            ->required(),
                                        Select::make('HairID')
                                            ->label(__('Hair'))
                                            ->relationship('hair', 'HairNameHE')
                                            ->searchable()
                                            ->preload()
                                            ->default(4)
                                            ->required(),
                                        Select::make('GroupID')
                                            ->label(__('Group ID'))
                                            ->options(array_combine(range(0, 7), range(0, 7))),
                                        ToggleButtons::make('SizeID')
                                            ->label(__('Size'))
                                            ->options(LegacyDogSize::class)
                                            ->grouped()
                                            ->nullable()
                                            ->columnSpanFull(),
                                    ])
                                    ->heading(__('Breed & Appearance'))
                                    ->columns(4),
                                Section::make('ownership')
                                    ->schema([
                                        Select::make('CurrentOwnerId')
                                            ->label(__('Owner pre 2022'))
                                            ->searchable()
                                            ->getSearchResultsUsing(function (string $search) {
                                                return PrevUser::query()
                                                    ->where(function ($q) use ($search) {
                                                        $q->where('first_name', 'like', "%{$search}%")
                                                            ->orWhere('last_name', 'like', "%{$search}%")
                                                            ->orWhere('first_name_en', 'like', "%{$search}%")
                                                            ->orWhere('last_name_en', 'like', "%{$search}%")
                                                            ->orWhere('owner_code', 'like', "%{$search}%");
                                                    })
                                                    ->limit(50)
                                                    ->get()
                                                    ->mapWithKeys(fn ($u) => [$u->owner_code => $u->name])
                                                    ->all();
                                            })
                                            ->getOptionLabelUsing(fn ($value): ?string => PrevUser::query()->where('owner_code', $value)->first()?->name),
                                        DatePicker::make('OwnershipDate')
                                            ->label(__('Ownership Date'))
                                            ->timezone('Asia/Jerusalem')
                                            ->native(false)
                                            ->locale('he')
                                            ->format('Y-m-d')
                                            ->displayFormat('Y-m-d')
                                            ->weekStartsOnSunday()
                                            ->closeOnDateSelection(),
                                        Select::make('BeitGidulID')
                                            ->label(__('Beit Gidul'))
                                            ->relationship('breedinghouse', 'GidulCode')
                                            ->searchable(['breedinghouses.HebName', 'breedinghouses.EngName', 'GidulCode'])
                                            ->getOptionLabelFromRecordUsing(fn (Model $record) => $record->name)
                                            ->preload()
                                            ->optionsLimit(25),
                                        TextInput::make('BeitGidulName')
                                            ->label(__('Beit Gidul Name (pre 2022)'))
                                            ->maxLength(200),
                                        TextInput::make('GrowerId')
                                            ->label(__('Breeder ID'))
                                            ->numeric(),
                                        TextInput::make('Breeder_Name')
                                            ->label(__('Breeder Name'))
                                            ->maxLength(300),
                                        TextInput::make('Foreign_Breeder_name')
                                            ->label(__('Foreign Breeder'))
                                            ->maxLength(255),
                                        TextInput::make('Breeding_ManagerID')
                                            ->label(__('Breeding Manager ID'))
                                            ->numeric(),
                                        TextInput::make('GidulShowType')
                                            ->label(__('Beit Gidul Name Position'))
                                            ->maxLength(200),
                                    ])
                                    ->heading(__('Ownership, Kennel and Breeder'))
                                    ->columns(4),
                                Section::make('miscellaneous')
                                    ->schema([
                                        Toggle::make('encoding')
                                            ->label(__('Encoding Issue'))
                                            ->inline(false),
                                        Toggle::make('is_correct')
                                            ->label(__('Is Correct'))
                                            ->inline(false),
                                        Toggle::make('not_relevant')
                                            ->label(__('Not Relevant'))
                                            ->inline(false),
                                        DateTimePicker::make('ModificationDateTime')
                                            ->label(__('Modified On'))
                                            ->format('Y-m-d H:i:s')
                                            ->timezone('Asia/Jerusalem')
                                            ->native(false)
                                            ->locale('he')
                                            ->displayFormat('d-m-Y H:i:s')
                                            ->weekStartsOnSunday()
                                            ->closeOnDateSelection()
                                            ->default(now()),
                                    ])
                                    ->heading(__('Miscellaneous'))
                                    ->columns(4),
                                Section::make('media')
                                    ->schema([
                                        TextInput::make('ProfileImage')
                                            ->label(__('Profile Image'))
                                            ->maxLength(300),
                                        TextInput::make('Image2')
                                            ->label(__('Image 2'))
                                            ->maxLength(300),
                                    ])
                                    ->heading(__('Media'))
                                    ->columns(2),
                            ])
                            ->label(__('General')),

                        Tab::make('pedigree_and_parents')
                            ->schema([
                                Section::make('parents')
                                    ->schema([
                                        Group::make([
                                            Select::make('FatherSAGIR')
                                                ->label(__('Father'))
                                                ->searchable(['SagirID', 'Heb_Name', 'Eng_Name', 'Chip', 'ImportNumber'])
                                                ->relationship('father', 'SagirID', modifyQueryUsing: fn (Builder $query) => $query->where('GenderID', '=', LegacyDogGender::Male->value), ignoreRecord: true)
                                                ->optionsLimit(20)
                                                ->searchDebounce(1500)
                                                ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->SagirID} - {$record->full_name}")
                                                ->createOptionForm([
                                                    Grid::make(3)
                                                        ->schema([
                                                            TextInput::make('ImportNumber')
                                                                ->label(__('Import Number'))
                                                                ->maxLength(200),
                                                            TextInput::make('Eng_Name')
                                                                ->label(__('English Name'))
                                                                ->maxLength(200),
                                                            TextInput::make('Heb_Name')
                                                                ->label(__('Hebrew Name'))
                                                                ->maxLength(200),
                                                            Group::make([
                                                                DatePicker::make('BirthDate')
                                                                    ->label(__('Birth Date'))
                                                                    ->timezone('Asia/Jerusalem')
                                                                    ->native(false)
                                                                    ->locale('he')
                                                                    ->format('Y-m-d')
                                                                    ->displayFormat('Y-m-d')
                                                                    ->weekStartsOnSunday()
                                                                    ->closeOnDateSelection(),
                                                                DatePicker::make('RegDate')
                                                                    ->label(__('Registration Date'))
                                                                    ->timezone('Asia/Jerusalem')
                                                                    ->native(false)
                                                                    ->locale('he')
                                                                    ->format('Y-m-d')
                                                                    ->displayFormat('Y-m-d')
                                                                    ->weekStartsOnSunday()
                                                                    ->closeOnDateSelection()
                                                                    ->default(now()),
                                                            ])
                                                                ->columns(3)
                                                                ->columnSpan(3),
                                                            Select::make('RaceID')
                                                                ->label(__('Breed'))
                                                                ->relationship('breed', 'BreedName')
                                                                ->searchable()
                                                                ->preload()
                                                                ->default(fn (EditPrevDog $livewire) => $livewire->getRecord()->RaceID),
                                                            Select::make('ColorID')
                                                                ->label(__('Color'))
                                                                ->relationship('color', 'ColorNameHE')
                                                                ->searchable()
                                                                ->preload()
                                                                ->default(9000),
                                                            Select::make('HairID')
                                                                ->label(__('Hair'))
                                                                ->relationship('hair', 'HairNameHE')
                                                                ->searchable()
                                                                ->preload()
                                                                ->default(4),
                                                            TextInput::make('Chip')
                                                                ->label(__('Chip'))
                                                                ->unique()
                                                                ->maxLength(200),
                                                            TextInput::make('DnaID')
                                                                ->label(__('DNA'))
                                                                ->maxLength(200),
                                                            TextInput::make('Breeder_Name')
                                                                ->label(__('Breeder Name'))
                                                                ->maxLength(300),
                                                            Textarea::make('HealthNotes')
                                                                ->label(__('Health Notes'))
                                                                ->maxLength(4000),
                                                            Textarea::make('Notes')
                                                                ->label(__('Notes'))
                                                                ->maxLength(1000),
                                                            TextInput::make('PedigreeNotes')
                                                                ->label(__('Titles (Pedigree Notes)'))
                                                                ->helperText(__('Comma separated')),
                                                            ToggleButtons::make('GenderID')
                                                                ->label(__('Gender'))
                                                                ->grouped()
                                                                ->options(LegacyDogGender::class)
                                                                ->default(fn (Get $get) => LegacyDogGender::Male->value),
                                                            Select::make('sagir_prefix')
                                                                ->label(__('Sagir Prefix'))
                                                                ->options(LegacySagirPrefix::class)
                                                                ->default(LegacySagirPrefix::NUL->value),
                                                            Hidden::make('SagirID'),
                                                            Hidden::make('DataID'),
                                                        ]),
                                                ])
                                                ->createOptionUsing(function (array $data, Get $get): int|string {
                                                    $service = app(PrevDogService::class);
                                                    $father = $service->createMinimalParent($data, LegacyDogGender::Male);

                                                    return $father->SagirID;
                                                })
                                                ->createOptionAction(fn (Action $action) => $action
                                                    ->modalHeading(__('Create Father'))
                                                    ->modalWidth('6xl')
                                                    ->label(__('Create new dog as Father'))
                                                    ->color('warning')
                                                    ->icon('fas-circle-plus')
                                                ),

                                            Select::make('MotherSAGIR')
                                                ->label(__('Mother'))
                                                ->searchable(['SagirID', 'Heb_Name', 'Eng_Name', 'Chip', 'ImportNumber'])
                                                ->relationship('mother', 'SagirID', modifyQueryUsing: fn (Builder $query) => $query->where('GenderID', '=', LegacyDogGender::Female->value), ignoreRecord: true)
                                                ->optionsLimit(20)
                                                ->searchDebounce(1500)
                                                ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->SagirID} - {$record->full_name}")
                                                ->createOptionForm([
                                                    Grid::make(3)
                                                        ->schema([
                                                            TextInput::make('ImportNumber')
                                                                ->label(__('Import Number'))
                                                                ->maxLength(200),
                                                            TextInput::make('Eng_Name')
                                                                ->label(__('English Name'))
                                                                ->maxLength(200),
                                                            TextInput::make('Heb_Name')
                                                                ->label(__('Hebrew Name'))
                                                                ->maxLength(200),
                                                            Group::make([
                                                                DatePicker::make('BirthDate')
                                                                    ->label(__('Birth Date'))
                                                                    ->timezone('Asia/Jerusalem')
                                                                    ->native(false)
                                                                    ->locale('he')
                                                                    ->format('Y-m-d')
                                                                    ->displayFormat('Y-m-d')
                                                                    ->weekStartsOnSunday()
                                                                    ->closeOnDateSelection(),
                                                                DatePicker::make('RegDate')
                                                                    ->label(__('Registration Date'))
                                                                    ->timezone('Asia/Jerusalem')
                                                                    ->native(false)
                                                                    ->locale('he')
                                                                    ->format('Y-m-d')
                                                                    ->displayFormat('Y-m-d')
                                                                    ->weekStartsOnSunday()
                                                                    ->closeOnDateSelection()
                                                                    ->default(now()),
                                                            ])
                                                                ->columns(3)
                                                                ->columnSpan(3),
                                                            Select::make('RaceID')
                                                                ->label(__('Breed'))
                                                                ->relationship('breed', 'BreedName')
                                                                ->searchable()
                                                                ->preload()
                                                                ->default(fn (EditPrevDog $livewire) => $livewire->getRecord()->RaceID),
                                                            Select::make('ColorID')
                                                                ->label(__('Color'))
                                                                ->relationship('color', 'ColorNameHE')
                                                                ->searchable()
                                                                ->preload()
                                                                ->default(9000),
                                                            Select::make('HairID')
                                                                ->label(__('Hair'))
                                                                ->relationship('hair', 'HairNameHE')
                                                                ->searchable()
                                                                ->preload()
                                                                ->default(4),
                                                            TextInput::make('Chip')
                                                                ->label(__('Chip'))
                                                                ->unique()
                                                                ->maxLength(200),
                                                            TextInput::make('DnaID')
                                                                ->label(__('DNA'))
                                                                ->maxLength(200),
                                                            TextInput::make('Breeder_Name')
                                                                ->label(__('Breeder Name'))
                                                                ->maxLength(300),
                                                            Textarea::make('HealthNotes')
                                                                ->label(__('Health Notes'))
                                                                ->maxLength(4000),
                                                            Textarea::make('Notes')
                                                                ->label(__('Notes'))
                                                                ->maxLength(1000),
                                                            TextInput::make('PedigreeNotes')
                                                                ->label(__('Titles (Pedigree Notes)'))
                                                                ->helperText(__('Comma separated')),
                                                            ToggleButtons::make('GenderID')
                                                                ->label(__('Gender'))
                                                                ->grouped()
                                                                ->options(LegacyDogGender::class)
                                                                ->default(fn (Get $get) => LegacyDogGender::Female->value),
                                                            Select::make('sagir_prefix')
                                                                ->label(__('Sagir Prefix'))
                                                                ->options(LegacySagirPrefix::class)
                                                                ->default(LegacySagirPrefix::NUL->value),
                                                            Hidden::make('SagirID'),
                                                            Hidden::make('DataID'),
                                                        ]),
                                                ])
                                                ->createOptionUsing(function (array $data, Get $get): int|string {
                                                    $service = app(PrevDogService::class);
                                                    $mother = $service->createMinimalParent($data, LegacyDogGender::Female);

                                                    return $mother->SagirID;
                                                })
                                                ->createOptionAction(fn (Action $action) => $action
                                                    ->modalHeading(__('Create Mother'))
                                                    ->modalWidth('6xl')
                                                    ->label(__('Create new dog as Mother'))
                                                    ->color('warning')
                                                    ->icon('fas-circle-plus')
                                                ),
                                        ])
                                            ->columns(2)
                                            ->columnSpan(2),
                                    ])
                                    ->heading(__('Parents'))
                                    ->columns(3),
                                Section::make('pedigree')
                                    ->schema([
                                        Group::make([
                                            TextInput::make('sheger_id')
                                                ->label(__('Sheger ID'))
                                                ->numeric(),
                                            ToggleButtons::make('pedigree_color')
                                                ->label(__('Pedigree Color'))
                                                ->options(LegacyPedigreeColor::class)
                                                ->grouped(),
                                            Select::make('RemarkCode')
                                                ->label(__('Remark Code'))
                                                ->options(fn () => array_combine(range(0, 36), range(0, 36)))
                                                ->searchable(),
                                            Toggle::make('red_pedigree')
                                                ->label(__('Red Pedigree'))
                                                ->inline(false)
                                                ->onColor('danger')
                                                ->offColor('gray'),
                                        ])
                                            ->columns(2)
                                            ->columnSpan(1),
                                        Group::make([
                                            Textarea::make('PedigreeNotes')
                                                ->label(__('Pedigree Notes'))
                                                ->maxLength(4000)
                                                ->autosize(),
                                            Textarea::make('PedigreeNotes_2')
                                                ->label(__('Pedigree Notes (2)'))
                                                ->maxLength(1000)
                                                ->autosize(),
                                            Textarea::make('Notes')
                                                ->label(__('Notes'))
                                                ->maxLength(1000)
                                                ->autosize(),
                                            Textarea::make('Notes_2')
                                                ->label(__('Notes (2)'))
                                                ->maxLength(1000)
                                                ->autosize(),
                                            Textarea::make('message')
                                                ->label(__('Message'))
                                                ->maxLength(255)
                                                ->autosize(),
                                            Textarea::make('message_test')
                                                ->label(__('Message Test'))
                                                ->maxLength(255)
                                                ->autosize(),
                                        ])
                                            ->columns(2)
                                            ->columnSpan(1),
                                    ])
                                    ->heading(__('Pedigree'))
                                    ->columns(2),
                            ])
                            ->label(__('Pedigree')),

                        Tab::make('pedigree_tree')
                            ->schema([
                                Livewire::make(PedigreeTree::class, fn (?PrevDog $record): array => [
                                    'dogId' => $record?->getKey(),
                                    'showBuilder' => true,
                                    'settings' => config('pedigree_tree.presets.resource_edit', []),
                                ])
                                    ->hidden(fn (?PrevDog $record): bool => $record === null)
                                    ->key(fn (?PrevDog $record): string => 'prev-dog-form-pedigree-tree-'.($record?->getKey() ?? 'new'))
                                    ->lazy(),
                            ])
                            ->label(__('Pedigree Tree')),

                        Tab::make('health_pre_2022')
                            ->schema([
                                Section::make('health_section')
                                    ->schema([
                                        Textarea::make('HealthNotes')
                                            ->label(__('Health Notes (pre 22)'))
                                            ->maxLength(4000)
                                            ->autosize()
                                            ->columnSpan(1),
                                        TextInput::make('Pelvis')
                                            ->label(__('Pelvis Test Remark (pre 22)'))
                                            ->maxLength(200)
                                            ->columnSpan(1),
                                    ])
                                    ->heading(__('Pre 2022 Health Information'))
                                    ->columns(4),
                                Section::make('mag')
                                    ->schema([
                                        TextInput::make('IsMagPass')
                                            ->label(__('MAG Pass'))
                                            ->numeric(),
                                        DatePicker::make('MagDate')
                                            ->label(__('MAG Date'))
                                            ->timezone('Asia/Jerusalem')
                                            ->native(false)
                                            ->locale('he')
                                            ->format('Y-m-d')
                                            ->displayFormat('Y-m-d')
                                            ->weekStartsOnSunday()
                                            ->closeOnDateSelection(),
                                        TextInput::make('MagJudge')
                                            ->label(__('MAG Judge'))
                                            ->maxLength(200),
                                        TextInput::make('MagPlace')
                                            ->label(__('MAG Place'))
                                            ->maxLength(200),
                                        TextInput::make('IsMagPass_2')
                                            ->label(__('MAG 2nd Pass'))
                                            ->numeric(),
                                        DatePicker::make('MagDate_2')
                                            ->label(__('MAG 2nd Date'))
                                            ->timezone('Asia/Jerusalem')
                                            ->native(false)
                                            ->locale('he')
                                            ->format('Y-m-d')
                                            ->displayFormat('Y-m-d')
                                            ->weekStartsOnSunday()
                                            ->closeOnDateSelection(),
                                        TextInput::make('MagJudge_2')
                                            ->label(__('MAG 2nd Judge'))
                                            ->maxLength(255),
                                        TextInput::make('MagPlace_2')
                                            ->label(__('MAG 2nd Place'))
                                            ->maxLength(255),
                                    ])
                                    ->heading(__('MAG pre 2022'))
                                    ->columns(4),
                            ])
                            ->label(__('Health pre 22')),

                    ])
                    ->persistTabInQueryString()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                return $query
                    ->with([
                        // BelongsTo: include owner key
                        'breed' => fn ($r) => $r->select(['BreedsDB.BreedCode', 'BreedsDB.BreedName', 'BreedsDB.BreedNameEN']),
                        'color' => fn ($r) => $r->select(['ColorsDB.OldCode', 'ColorsDB.ColorNameHE', 'ColorsDB.ColorNameEN']),
                        'hair' => fn ($r) => $r->select(['HairsDB.OldCode', 'HairsDB.HairNameHE', 'HairsDB.HairNameEN']),
                        'breedinghouse' => fn ($r) => $r->select(['breedinghouses.GidulCode', 'breedinghouses.HebName', 'breedinghouses.EngName']),
                        // Parents (PrevDog): include PK used to match and shown fields
                        'father' => fn ($r) => $r->select(['id', 'SagirID', 'Heb_Name', 'Eng_Name']),
                        'mother' => fn ($r) => $r->select(['id', 'SagirID', 'Heb_Name', 'Eng_Name']),
                        // Many-to-many Owners: include related PK + fields shown in list
                        'owners' => fn ($r) => $r->select(['users.id', 'first_name', 'last_name', 'first_name_en', 'last_name_en', 'mobile_phone', 'email']),
                        //                        'legacyOwner' => fn($r) => $r->select(['users.id', 'owner_code', 'first_name', 'last_name', 'first_name_en', 'last_name_en', 'mobile_phone', 'email']),
                        'titles' => fn ($r) => $r->select(['dogs_titles_db.TitleCode', 'dogs_titles_db.TitleName']),
                    ]);
                //                    ->with('duplicates');
            })
            ->columns([
                TextColumn::make('id')
                    ->label(__('id'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('SagirID')
                    ->label(__('Sagir'))
                    ->color(function (PrevDog $record): string {
                        return $record->sagir_prefix?->getColor() ?? 'grey';
                    })
                    ->prefix(fn (PrevDog $record): string => ($record->sagir_prefix?->code() ?? 'NUL').' | ')
                    ->icon(function (PrevDog $record): ?string {
                        return $record->sagir_prefix?->getIcon();
                    })
                    ->size('lg')
                    ->tooltip(__('Copy Sagir'))
                    ->copyable()
                    ->copyMessageDuration(duration: 1200)
                    ->copyMessage(fn ($state): string => __('Copied Sagir: :id', ['id' => $state]))
                    ->searchable(['SagirID'], isIndividual: true, isGlobal: false)
                    ->sortable(['SagirID']),
                TextColumn::make('fullName')
                    ->label(__('Full Name'))
                    ->searchable(['Heb_Name', 'Eng_Name'], isIndividual: true, isGlobal: false),
                TextColumn::make('Heb_Name')
                    ->label(__('Hebrew Name'))
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('Eng_Name')
                    ->label(__('English Name'))
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('breedinghouse.name')
                    ->label(__('Beit Gidul'))
                    ->searchable(['breedinghouses.HebName', 'breedinghouses.EngName'], isIndividual: true, isGlobal: false)
                    ->sortable(['breedinghouses.HebName'])
                    ->toggleable(),
                TextColumn::make('BeitGidulName')
                    ->label(__('Beit Gidul Name (pre 2022)'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('breed.BreedName')
                    ->label(__('Breed'))
                    ->description(function (PrevDog $record): string {
                        return $record->breed?->BreedNameEN ?? '~';
                    }, position: 'under')
                    ->sortable(['BreedName'])
                    ->toggleable(),
                TextColumn::make('color.ColorNameHE')
                    ->label(__('Color'))
                    ->description(function (PrevDog $record): string {
                        return $record->color?->ColorNameEN ?? '~';
                    }, position: 'under')
                    ->sortable(['ColorNameHE'])
                    ->toggleable(),
                TextColumn::make('hair.HairNameHE')
                    ->label(__('Hair'))
                    ->description(function (PrevDog $record): string {
                        return $record->hair?->HairNameEN ?? '~';
                    }, position: 'under')
                    ->sortable(['HairNameHE'])
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('GenderID')
                    ->label(__('Gender'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('Sex')
                    ->label(__('Sex'))
                    ->toggleable(),
                TextColumn::make('Chip')
                    ->label(__('Chip'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(),
                TextColumn::make('DnaID')
                    ->label(__('DNA'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('ImportNumber')
                    ->label(__('Import Number'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(),
                TextColumn::make('Chip_2')
                    ->label(__('Chip 2'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('BirthDate')
                    ->label(__('Birth Date'))
                    ->date()
                    ->sinceTooltip()
                    ->sortable(),
                TextColumn::make('RegDate')
                    ->label(__('Registration Date'))
                    ->date()
                    ->sinceTooltip()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('owners.full_name')
                    ->label(__('Owners'))
                    ->listWithLineBreaks()
                    ->limitList(2)
                    ->searchable(['users.first_name', 'users.last_name', 'users.first_name_en', 'users.last_name_en'], isIndividual: true, isGlobal: false)
                    ->description(function (PrevDog $record): string {
                        // Get the first two owners' names
                        return $record->owners?->pluck('id')->implode(', ');
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('father.full_name')
                    ->label(__('Father'))
                    ->description(function (PrevDog $record): string {
                        return $record->father?->SagirID ?? 'n/a';
                    }, position: 'under')
                    ->searchable(['Eng_Name', 'Heb_Name', 'SagirID'], isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('mother.full_name')
                    ->label(__('Mother'))
                    ->description(function (PrevDog $record): string {
                        return $record->mother?->SagirID ?? 'n/a';
                    }, position: 'under')
                    ->searchable(['Eng_Name', 'Heb_Name', 'SagirID'], isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('titles.name')
                    ->label(__('Titles'))
                    ->listWithLineBreaks()
                    ->limitList(1)
                    ->tooltip(fn (TextColumn $column): ?string => (($state = $column->getState()) === null) ? null :
                        (is_array($state)
                            ? (count($state) > $column->getListLimit() ? implode(' | ', $state) : null)
                            : (string) $state
                        )
                    )
                    ->searchable(['dogs_titles_db.TitleName'], isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('TitleName')
                    ->label(__('Titles pre 2022'))
                    ->wrapHeader()
                    ->separator(',')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('GrowerId')
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('Breeder_Name')
                    ->label(__('Breeder Name'))
                    ->wrapHeader()
                    ->sortable()
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('Foreign_Breeder_name')
                    ->label(__('Foreign Breeder'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('Breeding_ManagerID')
                    ->label(__('Breeding Manager ID - check'))
                    ->wrapHeader()
                    ->description(fn (PrevDog $record): string => $record->breedingManager->full_name ?? 'n/a')
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('Status')
                    ->label(__('Status'))
                    ->badge()
                    ->icon(fn (PrevDog $record): string => $record->Status?->getIcon() ?? 'fas-minus-circle')
                    ->color(fn (PrevDog $record): string => $record->Status?->getColor() ?? 'gray')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('BreedID')
                    ->label(__('Breed ID - pre 2022'))
                    ->wrapHeader()
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('SizeID')
                    ->label(__('Size'))
                    ->badge()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('Pelvis')
                    ->label(__('Pelvis'))
                    ->limit(200)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }

                        return $state;
                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('SCH')
                    ->label(__('SCH'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('RemarkCode')
                    ->label(__('Remark Code'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('GroupID')
                    ->label(__('Group ID'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('GidulShowType')
                    ->label(__('Beit Gidul Name Position'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('pedigree_color')
                    ->label(__('Pedigree Color'))
                    ->badge()
                    ->formatStateUsing(function ($state) {
                        $c = $state instanceof LegacyPedigreeColor
                            ? $state
                            : (! is_null($state) ? LegacyPedigreeColor::tryFrom((string) $state) : null);

                        return $c?->getLabel();
                    })
                    ->color(function ($state) {
                        $c = $state instanceof LegacyPedigreeColor
                            ? $state
                            : (! is_null($state) ? LegacyPedigreeColor::tryFrom((string) $state) : null);

                        return $c?->getColor();
                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('red_pedigree')
                    ->label(__('Red Pedigree'))
                    ->boolean()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('PedigreeNotes')
                    ->label(__('Pedigree Notes'))
                    ->limit(200)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }

                        return $state;
                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('PedigreeNotes_2')
                    ->label(__('Pedigree Notes 2'))
                    ->limit(200)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }

                        return $state;
                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('HealthNotes')
                    ->label(__('Health Notes'))
                    ->limit(200)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }

                        return $state;
                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('Notes_2')
                    ->label(__('Notes 2'))
                    ->limit(200)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }

                        return $state;
                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('message_test')
                    ->label(__('Message Test'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('sheger_id')
                    ->label(__('Sheger ID'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                // combine the Mag columns into one column, pass will be the value/state and the rest in a description
                TextColumn::make('IsMagPass')
                    ->label(__('Mag'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->description(function (PrevDog $record): string {
                        $magDate = $record->MagDate ?? '~';
                        $magJudge = $record->MagJudge ?? '~';
                        $magPlace = $record->MagPlace ?? '~';

                        return "{$magDate} | {$magJudge} | {$magPlace}";
                    }, position: 'under')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                // combine the Mag 2 columns into one column, pass will be the value/state and the rest in a description
                TextColumn::make('IsMagPass_2')
                    ->label(__('Mag 2'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->description(function (PrevDog $record): string {
                        $magDate = $record->MagDate_2 ?? '~';
                        $magJudge = $record->MagJudge_2 ?? '~';
                        $magPlace = $record->MagPlace_2 ?? '~';

                        return "{$magDate} | {$magJudge} | {$magPlace}";
                    }, position: 'under')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('is_correct')
                    ->label(__('Is Correct'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('ProfileImage')
                    ->label(__('Profile Image'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('Image2')
                    ->label(__('Profile Image 2'))
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('not_relevant')
                    ->label(__('Not Relevant'))
                    ->boolean()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('encoding')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                TextColumn::make('ModificationDateTime')
                    ->label(__('Modification Date'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('CreationDateTime')
                    ->label(__('Creation Date'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                //                Tables\Columns\TextColumn::make('duplicates_count')
                //                    ->label(__('Duplicates Count'))
                //                    ->numeric()
                //                    ->counts('duplicates')
                //                    ->sortable(['duplicates_count'])
                //                    ->toggleable(isToggledHiddenByDefault: true),
                //                Tables\Columns\TextColumn::make('duplicates')
                //                    ->label(__('Other Duplicate IDs'))
                //                    ->formatStateUsing(function (PrevDog $record): HtmlString {
                //                        // format the related duplicates so each of the duplicates array items will be a link to the route of PrevDogResource view page using the id as the parameter
                //                        $duplicatesLinks = $record->duplicates?->pluck('id')
                //                            ->filter(fn ($id) => $id != $record->id)
                //                            ->map(fn($id) => '<a href="' . PrevDogResource::getUrl('view', ['record' => $id]) . '" target="_blank">' . $id . '</a>'
                //                            )
                //                            ->implode(', ');
                //
                //                        return new HtmlString($duplicatesLinks);
                //                    })
                //                    ->wrap()
                //                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
                Filter::make('GenderID')
                    ->label(__('Gender'))
                    ->schema([
                        ToggleButtons::make('GenderID')
                            ->label(__('Gender'))
                            ->options(LegacyDogGender::class)
                            ->grouped()
                            ->nullable(),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        filled($data['GenderID'] ?? null),
                        fn (Builder $query): Builder => $query->where('GenderID', $data['GenderID']),
                    )),
                Filter::make('sagir_prefix')
                    ->schema([
                        ToggleButtons::make('sagir_prefix')
                            ->label(__('Sagir Prefix'))
                            ->options(LegacySagirPrefix::class)
                            ->multiple()
                            ->grouped()
                            ->nullable(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (empty($data['sagir_prefix'])) {
                            return $query;
                        }

                        return $query->whereIn('sagir_prefix', $data['sagir_prefix']);
                    }),
                SelectFilter::make('breed')
                    ->label(__('Breed'))
                    ->relationship('breed', 'BreedName')
                    ->multiple()
                    ->searchable(['BreedName', 'BreedNameEN'])
                    ->getOptionLabelFromRecordUsing(fn (PrevBreed $record): string => $record->BreedName.' | '.$record->BreedNameEN),
                SelectFilter::make('color')
                    ->label(__('Color'))
                    ->relationship('color', 'ColorNameHE')
                    ->multiple()
                    ->searchable(['ColorNameHE', 'ColorNameEN'])
                    ->getOptionLabelFromRecordUsing(fn (PrevColor $record): string => $record->ColorNameHE.' | '.$record->ColorNameEN),
                SelectFilter::make('hair')
                    ->label(__('Hair'))
                    ->relationship('hair', 'HairNameHE')
                    ->multiple()
                    ->searchable(['HairNameHE', 'HairNameEN'])
                    ->getOptionLabelFromRecordUsing(fn (PrevHair $record): string => $record->HairNameHE.' | '.$record->HairNameEN),
                Filter::make('father')
                    ->query(function (Builder $query, array $data): Builder {
                        $search = trim((string) ($data['father_search'] ?? ''));

                        if ($search === '') {
                            return $query;
                        }

                        return $query->whereHas('father', function (Builder $query) use ($search): void {
                            $query->where(function (Builder $query) use ($search): void {
                                $query->where('Heb_Name', 'like', "%{$search}%")
                                    ->orWhere('Eng_Name', 'like', "%{$search}%")
                                    ->orWhere('SagirID', 'like', "%{$search}%");
                            });
                        });
                    })
                    ->schema([
                        TextInput::make('father_search')
                            ->label(__('Father'))
                            ->hint(__('Name \ Sagir'))
                            ->helperText(__('Search by Hebrew\English Name or Sagir')),
                    ]),
                Filter::make('mother')
                    ->label(__('Mother'))
                    ->query(function (Builder $query, array $data): Builder {
                        $search = trim((string) ($data['mother_search'] ?? ''));

                        if ($search === '') {
                            return $query;
                        }

                        return $query->whereHas('mother', function (Builder $query) use ($search): void {
                            $query->where(function (Builder $query) use ($search): void {
                                $query->where('Heb_Name', 'like', "%{$search}%")
                                    ->orWhere('Eng_Name', 'like', "%{$search}%")
                                    ->orWhere('SagirID', 'like', "%{$search}%");
                            });
                        });
                    })
                    ->schema([
                        TextInput::make('mother_search')
                            ->label(__('Mother'))
                            ->hint(__('Name \ Sagir'))
                            ->helperText(__('Search by Hebrew\English Name or Sagir')),
                    ]),
                SelectFilter::make('owners')
                    ->label(__('Owners'))
                    ->multiple()
                    ->searchable()
                    ->optionsLimit(50)
                    ->getSearchResultsUsing(fn (?string $search): array => PrevUser::selectOptions($search))
                    ->getOptionLabelsUsing(fn (array $values): array => PrevUser::query()
                        ->nativeRecords()
                        ->whereIn('id', $values)
                        ->get()
                        ->pluck('search_label', 'id')
                        ->toArray())
                    ->query(function (Builder $query, array $data): Builder {
                        $ownerIds = collect($data['values'] ?? [])
                            ->filter(static fn (mixed $value): bool => filled($value))
                            ->map(static fn (mixed $value): int => (int) $value)
                            ->all();

                        if ($ownerIds === []) {
                            return $query;
                        }

                        return $query->whereHas('owners', fn (Builder $query): Builder => $query->whereIn('users.id', $ownerIds));
                    }),
                Filter::make('RegDate')
                    ->schema([
                        Section::make(__('Registration Date Range'))
                            ->description(__('Leave “Until” empty to include up to today'))
                            ->schema([
                                DatePicker::make('RegDate_from')
                                    ->label(__('Registration Date From'))
                                    ->timezone('Asia/Jerusalem')
                                    ->native(false)
                                    ->locale('he')
                                    ->format('Y-m-d')
                                    ->displayFormat('Y-m-d')
                                    ->weekStartsOnSunday()
                                    ->closeOnDateSelection(),
                                DatePicker::make('RegDate_until')
                                    ->label(__('Registration Date Until'))
                                    ->timezone('Asia/Jerusalem')
                                    ->native(false)
                                    ->locale('he')
                                    ->format('Y-m-d')
                                    ->displayFormat('Y-m-d')
                                    ->weekStartsOnSunday()
                                    ->closeOnDateSelection(),
                            ])
                            ->columns(2),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['RegDate_from'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('RegDate', '>=', $date),
                            )
                            ->when(
                                ($data['RegDate_from'] ?? null) && ! ($data['RegDate_until'] ?? null),
                                fn (Builder $query): Builder => $query->whereDate('RegDate', '<=', now()->toDateString()),
                            )
                            ->when(
                                $data['RegDate_until'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('RegDate', '<=', $date),
                            );
                    }),
                Filter::make('BirthDate')
                    ->schema([
                        Section::make(__('Birth Date Range'))
                            ->description(__('Leave “Until” empty to include up to today'))
                            ->schema([
                                DatePicker::make('BirthDate_from')
                                    ->label(__('Birth Date From'))
                                    ->timezone('Asia/Jerusalem')
                                    ->native(false)
                                    ->locale('he')
                                    ->format('Y-m-d')
                                    ->displayFormat('Y-m-d')
                                    ->weekStartsOnSunday()
                                    ->closeOnDateSelection(),
                                DatePicker::make('BirthDate_until')
                                    ->label(__('Birth Date Until'))
                                    ->timezone('Asia/Jerusalem')
                                    ->native(false)
                                    ->locale('he')
                                    ->format('Y-m-d')
                                    ->displayFormat('Y-m-d')
                                    ->weekStartsOnSunday()
                                    ->closeOnDateSelection(),
                            ])
                            ->columns(2),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['BirthDate_from'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('BirthDate', '>=', $date),
                            )
                            ->when(
                                ($data['BirthDate_from'] ?? null) && ! ($data['BirthDate_until'] ?? null),
                                fn (Builder $query): Builder => $query->whereDate('BirthDate', '<=', now()->toDateString()),
                            )
                            ->when(
                                $data['BirthDate_until'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('BirthDate', '<=', $date),
                            );
                    }),
                Filter::make('OwnershipDate')
                    ->schema([
                        Section::make(__('Ownership Date Range'))
                            ->description(__('Leave “Until” empty to include up to today'))
                            ->schema([
                                DatePicker::make('OwnershipDate_from')
                                    ->label(__('Ownership Date From'))
                                    ->timezone('Asia/Jerusalem')
                                    ->native(false)
                                    ->locale('he')
                                    ->format('Y-m-d')
                                    ->displayFormat('Y-m-d')
                                    ->weekStartsOnSunday()
                                    ->closeOnDateSelection(),
                                DatePicker::make('OwnershipDate_until')
                                    ->label(__('Ownership Date Until'))
                                    ->timezone('Asia/Jerusalem')
                                    ->native(false)
                                    ->locale('he')
                                    ->format('Y-m-d')
                                    ->displayFormat('Y-m-d')
                                    ->weekStartsOnSunday()
                                    ->closeOnDateSelection(),
                            ])
                            ->columns(2),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['OwnershipDate_from'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('OwnershipDate', '>=', $date),
                            )
                            ->when(
                                ($data['OwnershipDate_from'] ?? null) && ! ($data['OwnershipDate_until'] ?? null),
                                fn (Builder $query): Builder => $query->whereDate('OwnershipDate', '<=', now()->toDateString()),
                            )
                            ->when(
                                $data['OwnershipDate_until'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('OwnershipDate', '<=', $date),
                            );
                    }),

            ], layout: FiltersLayout::AboveContentCollapsible)
            ->filtersFormColumns(3)
            ->recordActionsPosition(RecordActionsPosition::BeforeColumns)
            ->recordActions([
                ViewAction::make()
                    ->iconButton()
                    ->iconSize(IconSize::Large)
                    ->tooltip(__('View')),
                EditAction::make()
                    ->iconButton()
                    ->iconSize(IconSize::Large)
                    ->tooltip(__('Edit')),
                Action::make('pedigree_tree_modal')
                    ->iconButton()
                    ->iconSize(IconSize::Large)
                    ->tooltip(__('Pedigree'))
                    ->icon('fas-sitemap')
                    ->color('info')
                    ->hidden(fn (PrevDog $record): bool => empty($record->father) && empty($record->mother))
                    ->modalHeading(__('Pedigree Tree'))
                    ->modalWidth(Width::Full)
                    ->modalSubmitAction(false)
                    ->modalCancelAction(fn (Action $action) => $action->label(__('Close')))
                    ->modalContent(fn (PrevDog $record): View => view('legacy.pedigree.pedigree-tree-modal', ['dogId' => $record->id])),

                Action::make('edit_pedigree')
                    ->iconButton()
                    ->iconSize(IconSize::Large)
                    ->tooltip(__('Manage Pedigree'))
                    ->icon(Heroicon::Share)
                    ->url(fn (PrevDog $record): string => PrevDogResource::getUrl('pedigree', ['record' => $record])),
                //                Tables\Actions\DeleteAction::make()
                //                    ->iconButton()
                //                    ->iconSize(IconSize::Large)
                //                    ->tooltip(__('Delete'))
                //                    ->icon('fas-trash-alt')
                //                    ->requiresConfirmation()
                //                    ->hidden(fn($record) => $record->trashed()),
                //                Tables\Actions\ForceDeleteAction::make()
                //                    ->iconButton()
                //                    ->iconSize(IconSize::Large)
                //                    ->tooltip(__('Force Delete'))
                //                    ->icon('fas-trash')
                //                    ->requiresConfirmation()
                //                    ->color('danger')
                //                    ->visible(fn($record) => $record->trashed()),

            ])
            ->headerActions([
                ExportAction::make()
                    ->label(__('Export All'))
                    ->icon('fas-file-export')
                    ->color('primary')
                    ->iconPosition('after')
                    ->exporter(PrevDogExporter::class),
            ])
            ->toolbarActions([
                ExportBulkAction::make()
                    ->label(__('Export Selected'))
                    ->icon('fas-file-export')
                    ->color('primary')
                    ->iconPosition('after')
                    ->exporter(PrevDogExporter::class),
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label(__('Delete'))
                        ->icon('fas-trash-alt')
                        ->requiresConfirmation(),
                    ForceDeleteBulkAction::make()
                        ->label(__('Force Delete'))
                        ->icon('fas-trash')
                        ->requiresConfirmation(),
                ]),
            ])
            ->paginated([5, 10, 15, 25])
            ->defaultPaginationPageOption(5)
            ->defaultSort('id', 'desc')
            ->defaultKeySort(false)
            ->searchOnBlur()
            ->striped()
            ->deferLoading()
            ->recordUrl(false)
//            ->recordUrl(fn(PrevDog $record): string => PrevDogResource::getUrl('edit', ['record' => $record]))
            ->recordClasses(fn (Model $record) => $record->trashed() ? 'fi-ta-row-deleted' : null);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Dog Record')->tabs([
                    /***** 1. Overview *****/
                    Tab::make('General')
                        ->schema([
                            Grid::make(1)->schema([
                                TextEntry::make('SagirID')
                                    ->label(__('Sagir'))
                                    ->inlineLabel()
                                    ->prefix(fn (PrevDog $record): string => $record->sagir_prefix->code())
                                    ->numeric(decimalPlaces: 0, thousandsSeparator: ''),
                                TextEntry::make('full_name')
                                    ->label(__('Full Name'))
                                    ->inlineLabel(),
                                TextEntry::make('RegDate')
                                    ->label(__('Registration Date'))
                                    ->inlineLabel()
                                    ->date(),
                                TextEntry::make('BirthDate')
                                    ->label(__('Birth Date'))
                                    ->inlineLabel()
                                    ->date(),
                                TextEntry::make('GenderID')
                                    ->label(__('Gender'))
                                    ->inlineLabel()
                                    ->state(fn (PrevDog $record): string => ($record->GenderID?->getLabel() ?? 'n/a')
                                        .(! empty($record->Sex) ? " ({$record->Sex})" : '')
                                    )
                                    ->color(fn (PrevDog $record) => $record->GenderID?->getColor())
                                    ->icon(fn (PrevDog $record) => $record->GenderID?->getIcon())
                                    ->iconColor(fn (PrevDog $record) => $record->GenderID?->getColor()),
                            ])
                                ->columnSpan(1),
                            Grid::make(1)->schema([
                                TextEntry::make('breeding_house_name')
                                    ->label(__('Beit Gidul'))
                                    ->inlineLabel(),
                                TextEntry::make('breed.BreedName')
                                    ->label(__('Breed'))
                                    ->inlineLabel(),
                                TextEntry::make('color.ColorNameHE')
                                    ->label(__('Color'))
                                    ->inlineLabel(),
                                TextEntry::make('hair.HairNameHE')
                                    ->label(__('Hair'))
                                    ->inlineLabel(),
                                TextEntry::make('Status')
                                    ->label(__('Status'))
                                    ->inlineLabel()
                                    ->badge()
                                    ->icon(fn (PrevDog $record): string => $record->Status?->getIcon() ?? 'fas-minus-circle')
                                    ->color(fn (PrevDog $record): string => $record->Status?->getColor() ?? 'gray'),
                            ])
                                ->columnSpan(1),
                        ])
                        ->label(__('General'))
                        ->columns(2),

                    /***** 2. Ownership & Breeding *****/
                    Tab::make('Ownership & Breeding')->schema([
                        Section::make('Ownership')->schema([
                            RepeatableEntry::make('owners')
                                ->schema([
                                    TextEntry::make('full_name')->label(__('Full Name')),
                                    TextEntry::make('mobile_phone')->label(__('Phone')),
                                    TextEntry::make('email')->label(__('Email')),
                                ])
                                ->label(fn (PrevDog $record): string => __('Owners')." ({$record->owners->count()})")
                                ->grid(4),
                            TextEntry::make('legacyOwner.full_name')
                                ->label(__('Owner pre 2022')),
                            TextEntry::make('OwnershipDate')
                                ->label(__('Ownership pre 2022'))
                                ->date(),
                        ])
                            ->label(__('Ownership')),
                        Section::make('Breeding')->schema([
                            TextEntry::make('Breeder_Name')->label(__('Breeder')),
                            TextEntry::make('Foreign_Breeder_name')->label(__('Foreign Breeder')),
                            TextEntry::make('breedingManager.full_name')->label(__('Breeding Manager')),
                        ])
                            ->label(__('Breeding')),
                    ])
                        ->label(__('Ownership & Breeding')),

                    /***** 3. Pedigree & Titles *****/
                    Tab::make('Pedigree & Titles')->schema([
                        Section::make('pedigree_section')
                            ->key('pedigree_section')
                            ->schema([
                                Section::make('Parants')
                                    ->schema([
                                        Section::make('Father Details')
                                            ->schema([
                                                TextEntry::make('father.full_name')->label(__('Father Name')),
                                                TextEntry::make('father.SagirID')->label(__('Father Sagir ID')),
                                            ])
                                            ->columns(3)
                                            ->columnSpan(1),
                                        Section::make('Mother Details')
                                            ->schema([
                                                TextEntry::make('mother.full_name')->label(__('Mother Name')),
                                                TextEntry::make('mother.SagirID')->label(__('Mother Sagir ID')),
                                            ])
                                            ->columns(3)
                                            ->columnSpan(1),
                                    ])
                                    ->columns(2)
                                    ->columnSpanFull(),
                                TextEntry::make('pedigree_color')
                                    ->label(__('Pedigree Color'))
                                    ->columnSpan(1),
                                IconEntry::make('red_pedigree')
                                    ->label(__('Red Pedigree'))
                                    ->columnSpan(1),
                                TextEntry::make('PedigreeNotes')
                                    ->label(__('Pedigree Notes'))
                                    ->columnSpan(2),
                            ])
                            ->heading(__('Pedigree'))
                            ->headerActions([
                                //                                InfolistAction::make('pedigree_tree_modal')
                                //                                    ->label(__('Pedigree'))
                                //                                    ->icon('fas-sitemap')
                                //                                    ->color('info')
                                //                                    ->hidden(fn(PrevDog $record): bool => empty($record->father) && empty($record->mother))
                                //                                    ->modalWidth(MaxWidth::Full)
                                //                                    ->modalHeading(__('Pedigree'))
                                //                                    ->modalSubmitAction(false)
                                //                                    ->modalCancelAction(fn(StaticAction $action) => $action->label(__('Close')))
                                //                                    ->modalContent(fn(PrevDog $record): View => view('legacy.pedigree.pedigree-tree-modal', ['dogId' => $record->id])),

                                //                                InfolistAction::make('edit_pedigree')
                                //                                ->label(__('Manage Pedigree'))
                                //                                ->icon('heroicon-m-share')
                                //                                ->url(fn (PrevDog $record): string => PrevDogResource::getUrl('pedigree', ['record' => $record])),
                            ]),
                        Section::make('Titles & Shows')->schema([
                            RepeatableEntry::make('titles')
                                ->label(fn (PrevDog $record): string => __('Titles')." ({$record->titles->count()})")
                                ->schema([
                                    TextEntry::make('name')
                                        ->hiddenLabel()
                                        ->size(TextSize::Large)
                                        ->weight(FontWeight::Bold)
                                        ->color(Color::Blue)
                                        ->columnSpan(2),
                                    TextEntry::make('awarding.EventPlace')
                                        ->hiddenLabel()
                                        // ->badge()
                                        ->color('warning')
                                        ->size(TextSize::Medium)
                                        ->columnSpan(3),
                                    TextEntry::make('awarding.EventDate')
                                        ->hiddenLabel()
                                        ->date()
                                        ->columnSpan(2),
                                    TextEntry::make('awarding.EventName')
                                        ->hiddenLabel()
                                        ->columnSpan(3),
                                ])
                                ->columns(5)
                                ->grid(5),
                            TextEntry::make('ShowsCount')->label(__('Shows Count')),
                        ])
                            ->label(__('Titles & Shows')),
                        TextEntry::make('TitleName')->label(__('Titles pre 2022')),
                    ])
                        ->label(__('Pedigree & Titles')),

                    Tab::make('Pedigree Tree')
                        ->schema([
                            Livewire::make(PedigreeTree::class, fn (PrevDog $record): array => [
                                'dogId' => $record->getKey(),
                                'showBuilder' => false,
                                'settings' => config('pedigree_tree.presets.resource_view', []),
                            ])
                                ->key(fn (PrevDog $record): string => "prev-dog-pedigree-tree-{$record->getKey()}")
                                ->lazy()
                                ->columnSpanFull(),
                        ])
                        ->label(__('Pedigree Tree')),

                    /***** 4. Metrics & Performance *****/
                    Tab::make('Metrics & Performance')->schema([
                        Grid::make(2)->schema([
                            IconEntry::make('IsMagPass')->label(__('MHG Pass')),
                            IconEntry::make('IsMagPass_2')->label(__('MHG 2nd Pass')),
                        ]),
                        Grid::make(3)->schema([
                            TextEntry::make('SupplementarySign')->label(__('Supplementary Sign')),
                            TextEntry::make('SizeID')->label(__('Size ID')),
                            TextEntry::make('GroupID')->label(__('Group ID')),
                        ]),
                    ]),

                    /***** 5. Health & Notes *****/
                    Tab::make('Health & Notes')->schema([
                        TextEntry::make('HealthNotes')
                            ->label(__('Health Notes'))
                            ->columnSpanFull(),
                        Grid::make(2)->schema([
                            TextEntry::make('Pelvis')->label(__('Pelvis')),
                            TextEntry::make('SCH')->label(__('SCH')),
                        ]),
                        TextEntry::make('Notes_2')
                            ->label(__('Additional Notes'))
                            ->columnSpanFull(),
                        TextEntry::make('message_test')->label(__('Message Test')),
                    ]),

                    /***** 6. Media & Flags *****/
                    Tab::make('Media')->schema([
                        Grid::make(2)->schema([
                            ImageEntry::make('ProfileImage')->label(__('Profile Image')),
                            ImageEntry::make('Image2')->label(__('Image 2')),
                        ]),
                    ]),

                    /***** 7. Metadata *****/
                    Tab::make('Metadata')
                        ->label(__('common.labels.metadata'))
                        ->schema([
                            Grid::make(5)->schema([
                                TextEntry::make('id')->label(__('ID')),
                                IconEntry::make('not_relevant')->label(__('Not Relevant')),
                                IconEntry::make('encoding')->label(__('Encoding Issue')),
                            ]),
                            Grid::make(5)->schema([
                                TextEntry::make('CreationDateTime')
                                    ->label(__('Created On'))
                                    ->date(),
                                TextEntry::make('ModificationDateTime')
                                    ->label(__('Modified On'))
                                    ->date(),
                                TextEntry::make('created_at')
                                    ->label(__('Created At'))
                                    ->date(),
                                TextEntry::make('updated_at')
                                    ->label(__('Updated At'))
                                    ->date(),
                                TextEntry::make('deleted_at')
                                    ->label(__('Deleted at'))
                                    ->date(),
                            ]),
                        ]),
                ])
                    ->columnSpanFull()
                    ->persistTabInQueryString(),

            ]);
    }

    public static function getRelations(): array
    {
        return [
            OwnersRelationManager::class,
            FemaleBreedingsRelationManager::class,
            MaleBreedingsRelationManager::class,
            ChildrenRelationManager::class,
            TitlesRelationManager::class,
            HealthRecordsRelationManager::class,
            PrevDogDocumentRelationManager::class,
            PaymentsRelationManager::class,
            UserRequestsRelationManager::class,
            ShowDogsRelationManager::class,
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPrevDogs::route('/'),
            'create' => CreatePrevDog::route('/create'),
            // Place custom routes before the generic `{record}` routes to avoid collisions.
            'pedigree' => ManagePedigree::route('/pedigree/{record?}'),
            'view' => ViewPrevDog::route('/{record}'),
            'edit' => EditPrevDog::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            DogStats::class,
        ];
    }
}
