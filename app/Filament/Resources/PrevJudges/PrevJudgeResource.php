<?php

namespace App\Filament\Resources\PrevJudges;

use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Grid;
use App\Filament\Resources\PrevJudges\Pages\ListPrevJudges;
use App\Filament\Resources\PrevJudges\Pages\CreatePrevJudge;
use App\Filament\Resources\PrevJudges\Pages\ViewPrevJudge;
use App\Filament\Resources\PrevJudges\Pages\EditPrevJudge;
use App\Filament\Resources\PrevJudges\Pages;
use App\Models\PrevJudge;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Filament\Support\Icons\Heroicon;

class PrevJudgeResource extends Resource
{
    protected static ?string $model = PrevJudge::class;

    protected static ?string $slug = 'prev-judges';

    protected static string | \BackedEnum | null $navigationIcon = 'fas-gavel';

    protected static ?int $navigationSort = 10;

    public static function getModelLabel(): string
    {
        return __('Judge');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Judges');
    }

    public static function getNavigationGroup(): string
    {
        return __('Shows Management');
    }

    public static function getNavigationLabel(): string
    {
        return __('Judges');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([//
                TextInput::make('DataID')
                    ->disabled()
                    ->integer(),

                DatePicker::make('ModificationDateTime'),

                DatePicker::make('CreationDateTime'),

                TextInput::make('JudgeNameHE')
                    ->required(),

                TextInput::make('JudgeNameEN')
                    ->required(),

                TextInput::make('Country'),

                TextInput::make('BreedID')
                    ->integer(),

                TextInput::make('Email'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                return $query
                    ->with(['judgedBreedsWithDogs'])
                    ->withCount([
                        'arenas',
                        'showBreeds as breeds_count' => function (Builder $q) {
                            $q->select(DB::raw('COUNT(DISTINCT Shows_Breeds.RaceID)'))
                                ->whereExists(function ($ex) {
                                    $ex->select(DB::raw(1))
                                        ->from('Shows_Dogs_DB as sd')
                                        ->whereColumn('sd.ShowID', 'Shows_Breeds.ShowID')
                                        ->whereColumn('sd.BreedID', 'Shows_Breeds.RaceID')
                                        ->whereNull('sd.deleted_at');
                                });
                        },
                    ]);
            })
            ->columns([
                TextColumn::make('JudgeNameHE')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->sortable()
                    ->label(__('common.labels.hebrew_name')),

                TextColumn::make('JudgeNameEN')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->sortable()
                    ->label(__('common.labels.english_name')),

                TextColumn::make('Country')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->sortable()
                    ->label(__('Country')),

                TextColumn::make('Email')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->sortable()
                    ->label(__('Email'))
                    ->toggleable(),

                TextColumn::make('arenas_count')
                    ->counts('arenas')
                    ->numeric()
                    ->sortable(['arenas_count'])
                    ->label(__('Arenas')),
                TextColumn::make('breeds_count')
                    ->numeric()
                    ->sortable(['breeds_count'])
                    ->label(__('Breeds')),

                TextColumn::make('ModificationDateTime')
                    ->label(__('Modified On'))
                    ->date()
                    ->toggleable()
                    ->sortable(),

                TextColumn::make('CreationDateTime')
                    ->label(__('Created On'))
                    ->date()
                    ->toggleable()
                    ->sortable(),

                TextColumn::make('DataID')
                    ->numeric()
                    ->label(__('DataID'))
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([

            ])
            ->recordActions([
                Action::make('view_judge')
                    ->label(__('common.actions.view'))
                    ->icon(Heroicon::OutlinedEye)
                    ->modalHeading(__('common.actions.view'))
                    ->color('grey')
                    // Return a custom-loaded record for the modal (with eager loads and counts)
                    ->record(function (Model $record): Model {
                        /** @var PrevJudge $record */
                        return PrevJudge::query()
                            ->with(['judgedBreedsWithDogs'])
                            ->withCount([
                                'arenas',
                                'showBreeds as breeds_count' => function (Builder $q) {
                                    $q->select(DB::raw('COUNT(DISTINCT Shows_Breeds.RaceID)'))
                                        ->whereExists(function ($ex) {
                                            $ex->select(DB::raw(1))
                                                ->from('Shows_Dogs_DB as sd')
                                                ->whereColumn('sd.ShowID', 'Shows_Breeds.ShowID')
                                                ->whereColumn('sd.BreedID', 'Shows_Breeds.RaceID')
                                                ->whereNull('sd.deleted_at');
                                        });
                                },
                            ])
                            ->findOrFail($record->getKey());
                    })
                    // Build the modal’s infolist
                    ->schema(function (Schema $schema): Schema {
                        return PrevJudgeResource::infolist($schema);
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);

    }

    public static function infolist(Schema $schema): Schema
    {

        return $schema->components([
            Tabs::make('Judge Record')->tabs([
                Tab::make('general')->schema([
                    Grid::make(4)->schema([
                        TextEntry::make('DataID')->label(__('DataID')),
                        TextEntry::make('JudgeNameHE')->label(__('common.labels.hebrew_name')),
                        TextEntry::make('JudgeNameEN')->label(__('common.labels.english_name')),
                        TextEntry::make('Country')->label(__('Country')),
                    ]),
                    Grid::make(4)->schema([
                        TextEntry::make('Email')->label(__('Email')),
                        TextEntry::make('arenas_count')->label(__('Arenas')),
                        TextEntry::make('breeds_count')->label(__('Breeds')),
                        TextEntry::make('breeds_names_he')->label(fn(): string => __('Breeds') . ' (' . __('common.labels.hebrew') . ')'),
                        TextEntry::make('breeds_names_en')->label(fn(): string => __('Breeds') . ' (' . __('common.labels.english') . ')'),
                    ]),
                ])->label(__('General')),
                Tab::make('metadata')->schema([
                    Grid::make(4)->schema([
                        TextEntry::make('CreationDateTime')->label(__('Created at'))->date(),
                        TextEntry::make('ModificationDateTime')->label(__('Modified On'))->date(),
                    ]),
                ])->label(__('common.labels.metadata')),
            ])->columnSpanFull(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPrevJudges::route('/'),
            'create' => CreatePrevJudge::route('/create'),
            'view' => ViewPrevJudge::route('/{record}'),
            'edit' => EditPrevJudge::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        // Index page logic is already set in table->modifyQueryUsing
        // return parent::getEloquentQuery();

        // Index page query: keep counts consistent with modal
        return parent::getEloquentQuery()
            ->with(['judgedBreedsWithDogs'])
            ->withCount([
                'arenas',
                'showBreeds as breeds_count' => function (Builder $q) {
                    $q->select(DB::raw(
                        'COUNT(DISTINCT Shows_Breeds.RaceID)'
                    ))
                        ->whereExists(function ($ex) {
                            $ex->select(DB::raw(1))
                                ->from('Shows_Dogs_DB as sd')
                                ->whereColumn('sd.ShowID', 'Shows_Breeds.ShowID')
                                ->whereColumn('sd.BreedID', 'Shows_Breeds.RaceID')
                                ->whereNull('sd.deleted_at');
                        });
                },
            ]);
    }
}
