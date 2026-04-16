<?php

namespace App\Filament\Resources\PrevBreedingHouses;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use App\Filament\Resources\PrevBreedingHouses\Pages\ListPrevBreedingHouses;
use App\Filament\Resources\PrevBreedingHouses\Pages\CreatePrevBreedingHouse;
use App\Filament\Resources\PrevBreedingHouses\Pages\ViewPrevBreedingHouse;
use App\Filament\Resources\PrevBreedingHouses\Pages\EditPrevBreedingHouse;
use App\Filament\Resources\PrevBreedingHouses\Pages;
use App\Filament\Resources\PrevBreedingHouses\RelationManagers\DogsRelationManager;
use App\Filament\Resources\PrevBreedingHouses\RelationManagers\UsersRelationManager;
use App\Models\PrevBreedingHouse;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PrevBreedingHouseResource extends Resource
{
    protected static ?string $model = PrevBreedingHouse::class;

    // Keep Shield consistent: do NOT set $slug here.

    protected static string | \BackedEnum | null $navigationIcon = 'fas-house-chimney';

    protected static ?int $navigationSort = 25;

    public static function getNavigationGroup(): string
    {
        return __('dog/model/general.labels.navigation_group');
    }

    public static function getNavigationLabel(): string
    {
        return __('dog/kennel/general.labels.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('dog/kennel/general.labels.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('dog/kennel/general.labels.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('common.labels.general'))
                ->schema([
                    Grid::make(5)->schema([
                        TextInput::make('GidulCode')->label(__('common.labels.code'))->numeric()->readOnly(),
                        TextInput::make('HebName')->label(__('common.labels.hebrew_name')),
                        TextInput::make('EngName')->label(__('common.labels.english_name')),
                    ]),
                ]),
            Section::make(__('common.labels.status_and_flags'))
                ->schema([
                    Grid::make(5)->schema([
                        Toggle::make('status')
                            ->label(__('common.labels.active'))
                            ->inline(false),
                        Toggle::make('recommended')
                            ->label(__('common.labels.recommended'))
                            ->inline(false),
                        DatePicker::make('recommended_from_date')->label(__('common.labels.recommended_from')),
                        Toggle::make('perfect')
                            ->label(__('common.labels.perfect'))
                            ->inline(false),
                        DatePicker::make('perfect_from_date')->label(__('common.labels.perfect_from')),
                    ]),
                ]),
            Section::make(__('common.labels.metadata'))
                ->schema([
                    Grid::make(5)->schema([
                        TextInput::make('MegadelCode')->label(__('common.labels.breeder_code'))->numeric(),
                        TextInput::make('MisparNosaf')->label(__('common.labels.extra_number'))->numeric(),
                        TextInput::make('Notes')->label(__('common.labels.notes')),
                    ]),
                ]),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('common.labels.overview'))->schema([
                Grid::make(4)->schema([
                    TextEntry::make('GidulCode')->label(__('common.labels.code'))->numeric(decimalPlaces: 0, thousandsSeparator: ''),
                    TextEntry::make('HebName')->label(__('common.labels.hebrew_name')),
                    TextEntry::make('EngName')->label(__('common.labels.english_name')),
                    IconEntry::make('status')->label(__('common.labels.active'))->boolean(),
                ]),
                Grid::make(4)->schema([
                    IconEntry::make('recommended')->label(__('common.labels.recommended'))->boolean(),
                    TextEntry::make('recommended_from_date')->label(__('common.labels.recommended_from'))->date(),
                ]),
                Grid::make(4)->schema([
                    IconEntry::make('perfect')->label(__('common.labels.perfect'))->boolean(),
                    TextEntry::make('perfect_from_date')->label(__('common.labels.perfect_from'))->date(),
                ]),
            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                return $query
                    ->with(['users'])
                    ->withCount(['dogs']);
            })
            ->columns([
                TextColumn::make('id')
                    ->label(__('ID'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('GidulCode')->label(__('Code'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->searchable(isIndividual: true, isGlobal: false),
                TextColumn::make('HebName')
                    ->label(__('Hebrew Name'))
                    ->searchable(isIndividual: true, isGlobal: false),
                TextColumn::make('EngName')
                    ->label(__('English Name'))
                    ->searchable(isIndividual: true, isGlobal: false),
                TextColumn::make('dogs_count')
                    ->label(__('dog/model/general.labels.plural'))
                    ->counts('dogs')
                    ->sortable(['dogs_count'])
                    ->toggleable(),
                TextColumn::make('users.name')
                    ->label(__('Owners'))
                    ->listWithLineBreaks()
                    ->limitList(2)
                    ->searchable(['users.first_name', 'users.last_name', 'users.first_name_en', 'users.last_name_en'], isIndividual: true, isGlobal: false)
                    ->toggleable(),
                IconColumn::make('recommended')->label(__('Recommended'))->boolean()->toggleable(),
                TextColumn::make('recommended_from_date')->label(__('Recommended From'))->date()->toggleable(),
                IconColumn::make('perfect')->label(__('Perfect'))->boolean()->toggleable(),
                TextColumn::make('perfect_from_date')->label(__('Perfect From'))->date()->toggleable(),
            ])
            ->filters([

            ])
            ->recordActions([
                ViewAction::make()->label(__('common.actions.view')),
                EditAction::make()->label(__('common.actions.edit')),
                //                DeleteAction::make()->label(__('common.actions.delete')),
                //                RestoreAction::make()->label(__('common.actions.restore')),
                //                ForceDeleteAction::make()->label(__('common.actions.force_delete')),
            ])
            ->toolbarActions([
                //                BulkActionGroup::make([
                //                    DeleteBulkAction::make(),
                //                    RestoreBulkAction::make(),
                //                    ForceDeleteBulkAction::make(),
                //                ]),
            ]);
        //            ->defaultSort('updated_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            DogsRelationManager::class,
            UsersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPrevBreedingHouses::route('/'),
            'create' => CreatePrevBreedingHouse::route('/create'),
            'view' => ViewPrevBreedingHouse::route('/{record}'),
            'edit' => EditPrevBreedingHouse::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    }
}
