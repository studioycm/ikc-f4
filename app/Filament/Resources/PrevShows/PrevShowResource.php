<?php

namespace App\Filament\Resources\PrevShows;

use App\Filament\Resources\PrevShowDogs\PrevShowDogResource;
use App\Filament\Resources\PrevShowResults\PrevShowResultResource;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use App\Filament\Resources\PrevShows\Pages\ListPrevShows;
use App\Filament\Resources\PrevShows\Pages\CreatePrevShow;
use App\Filament\Resources\PrevShows\Pages\ViewPrevShow;
use App\Filament\Resources\PrevShows\Pages\EditPrevShow;
use App\Enums\Legacy\LegacyShowTypeEnum;
use App\Filament\Resources\PrevShows\Pages;
use App\Filament\Resources\PrevShows\RelationManagers\PrevShowArenaRelationManager;
use App\Filament\Resources\PrevShows\RelationManagers\PrevShowClassRelationManager;
use App\Models\PrevShow;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;

class PrevShowResource extends Resource
{
    protected static ?string $model = PrevShow::class;

    protected static ?string $slug = 'prev-shows';

    protected static string | \BackedEnum | null $navigationIcon = 'fas-trophy';

    protected static ?int $navigationSort = 20;

    public static function getModelLabel(): string
    {
        return __('Show');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Shows');
    }

    public static function getNavigationGroup(): string
    {
        return __('Shows Management');
    }

    public static function getNavigationLabel(): string
    {
        return __('Shows');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('DataID')
                    ->required()
                    ->integer(),

                DatePicker::make('ModificationDateTime'),

                DatePicker::make('CreationDateTime'),

                TextInput::make('TitleName'),

                DatePicker::make('StartDate'),

                TextInput::make('ShortDesc'),

                TextInput::make('LongDesc'),

                TextInput::make('TopImage'),

                TextInput::make('MaxRegisters')
                    ->numeric(),

                ToggleButtons::make('ShowType')
                    ->label(__('Show Type'))
                    ->options(LegacyShowTypeEnum::class)
                    ->nullable(),


                TextInput::make('ClubID')
                    ->numeric(),

                DatePicker::make('EndRegistrationDate'),

                TextInput::make('ShowStatus')
                    ->numeric(),

                DatePicker::make('EndDate'),

                TextInput::make('ShowPrice')
                    ->numeric(),

                TextInput::make('Dog2Price1')
                    ->required()
                    ->numeric(),

                TextInput::make('Dog2Price2')
                    ->required()
                    ->numeric(),

                TextInput::make('Dog2Price3')
                    ->required()
                    ->numeric(),

                TextInput::make('Dog2Price4')
                    ->required()
                    ->numeric(),

                TextInput::make('Dog2Price5')
                    ->required()
                    ->numeric(),

                TextInput::make('Dog2Price6')
                    ->required()
                    ->numeric(),

                TextInput::make('Dog2Price7')
                    ->required()
                    ->numeric(),

                TextInput::make('Dog2Price8')
                    ->required()
                    ->numeric(),

                TextInput::make('Dog2Price9')
                    ->required()
                    ->numeric(),

                TextInput::make('Dog2Price10')
                    ->required()
                    ->numeric(),

                TextInput::make('CouplesPrice')
                    ->numeric(),

                TextInput::make('BGidulPrice')
                    ->numeric(),

                TextInput::make('ZezaimPrice')
                    ->numeric(),

                TextInput::make('YoungPrice')
                    ->numeric(),

                TextInput::make('MoreDogsPrice')
                    ->numeric(),

                TextInput::make('MoreDogsPrice2')
                    ->numeric(),

                TextInput::make('TicketCost')
                    ->numeric(),

                TextInput::make('IsExtraTickets'),

                TextInput::make('IsParking'),

                TextInput::make('MoreTicketsSelect'),

                TextInput::make('ParkingSelect'),

                TextInput::make('PeototCost')
                    ->numeric(),

                TextInput::make('FreeTextDesc'),

                TextInput::make('start_from_index'),

                TextInput::make('location'),

                TextInput::make('Check_all_members')
                    ->required()
                    ->integer(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                return $query
                    ->with(['judges', 'club'])
                    ->scopes(['withCountsForResource']);
            })
            ->columns([
                TextColumn::make('id')
                    ->label(__('ID'))
                    ->sortable()
                    ->toggleable()
                    ->searchable(),
                TextColumn::make('ShowType')
                    ->label(__('Show type'))
                    ->badge()
                    ->icon(fn(PrevShow $r): ?string => $r->ShowType?->getIcon())
                    ->color(fn(PrevShow $r): ?string => $r->ShowType?->getColor())
                    ->searchable(['ShowsDB.ShowType'], isIndividual: true, isGlobal: false)
                    ->sortable('ShowsDB.ShowType'),
                TextColumn::make('club.Name')
                    ->label(__('Club'))
                    ->searchable(['clubs.Name'], isIndividual: true, isGlobal: false)
                    ->sortable('clubs.Name'),
                TextColumn::make('location')
                    ->label(__('Location'))
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->sortable(),
                // 1. Combined Title & Description Column
                TextColumn::make('TitleName')
                    ->label(__('Show Title & Description'))
                    ->searchable(['TitleName', 'LongDesc'])
                    ->sortable(['TitleName'])
                    ->wrap()
                    ->color('primary')
                    ->description(function (PrevShow $record): string {
                        if (blank($record->LongDesc)) return '';
                        $cleanText = html_entity_decode(strip_tags(str_replace('&nbsp;', ' ', $record->LongDesc)), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                        return str($cleanText)->trim()->limit(60);
                    })
                    ->extraAttributes(fn(PrevShow $record) => !blank($record->LongDesc) ? ['class' => 'cursor-pointer underline'] : [])
                    ->action(
                        Action::make('view_description')
                            ->modalHeading(__('Description'))
                            ->modalWidth('2xl')
                            ->modalSubmitAction(false)
                            ->modalCancelActionLabel(__('Close'))
                            ->schema([
                                TextEntry::make('LongDesc')
                                    ->hiddenLabel()
                                    ->html()
                            ])
                            ->disabled(fn($record) => blank($record->LongDesc))
                    ),

                // 2. Combined Dates Column
                TextColumn::make('StartDate')
                    ->label(__('Show Dates'))
                    // Clicking the header sorts sequentially by all three date fields
                    ->sortable(['StartDate', 'EndDate', 'EndRegistrationDate'])
                    ->html()
                    ->formatStateUsing(function (PrevShow $record) {
                        $start = $record->StartDate ? $record->StartDate->format('d/m/Y H:i') : '-';
                        $end = $record->EndDate ? $record->EndDate->format('d/m/Y H:i') : '-';
                        $reg = $record->EndRegistrationDate ? $record->EndRegistrationDate->format('d/m/Y') : '-';

                        return "<div class='leading-tight space-y-1'>
                                <div class='text-sm'><strong>" . __('Start') . ":</strong> {$start}</div>
                                <div class='text-xs text-gray-500 dark:text-gray-400'><strong>" . __('End') . ":</strong> {$end}</div>
                                <div class='text-xs text-gray-500 dark:text-gray-400'><strong>" . __('Reg') . ":</strong> {$reg}</div>
                            </div>";
                    }),

                IconColumn::make('ShowStatus')
                    ->label(__('Show Status'))
                    ->boolean(fn($state): bool => $state === 2)
                    ->color(fn($state): string => $state === 2 ? 'success' : 'danger')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('arenas_count')
                    ->label(__('Arenas'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('classes_count')
                    ->label(__('Classes'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('registrations_count')
                    ->label(__('Registration'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(),

                // 3. Show Dogs Count -> Converted to Action Column
                TextColumn::make('show_dogs_count')
                    ->label(__('Show Dogs'))
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn(int $state): string => $state > 0 ? 'info' : 'black')
                    ->toggleable()
                    ->extraAttributes(fn(int $state) => $state > 0 ? ['class' => 'cursor-pointer font-bold'] : [])
                    ->url(fn(PrevShow $record, int $state): ?string => $state > 0
                        ? PrevShowDogResource::getUrl('index', ['filters' => ['ShowID' => ['value' => $record->id]]])
                        : null
                    ),

                // 4. Results Count -> Converted to Action Column
                TextColumn::make('results_count')
                    ->label(__('Results'))
                    ->badge()
                    ->sortable()
                    ->toggleable()
                    ->color(fn(int $state): string => $state > 0 ? 'warning' : 'grey')
                    ->extraAttributes(fn(int $state) => $state > 0 ? ['class' => 'cursor-pointer font-bold'] : [])
                    ->url(fn(PrevShow $record, int $state): ?string => $state > 0
                        ? PrevShowResultResource::getUrl('index', ['filters' => ['ShowID' => ['value' => $record->id]]])
                        : null
                    ),

                TextColumn::make('IsExtraTickets')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('IsParking')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('MoreTicketsSelect')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('ParkingSelect')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('start_from_index')
                    ->label(__('Index Start'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('Check_all_members')
                    ->label(__('All Members'))
                    ->boolean(fn($state): bool => $state === 1)
                    ->color(fn($state): string => $state === 1 ? 'success' : 'danger')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('DataID')
                    ->numeric()
                    ->label(__('Data ID'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('ModificationDateTime')
                    ->date()
                    ->label(__('Last Modified Date'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('CreationDateTime')
                    ->date()
                    ->label(__('Created Date'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->filters([
                TernaryFilter::make('has_results')
                    ->label(__('Has results'))
                    ->trueLabel(__('Yes'))
                    ->falseLabel(__('No'))
                    ->queries(
                        true: function (Builder $query) {
                            $showIdsWithResults = DB::connection('mysql_prev')
                                ->table('shows_results')
                                ->whereNotNull('ShowID')
                                ->distinct()
                                ->pluck('ShowID')
                                ->toArray();
                            return $query->whereIn('ShowsDB.id', $showIdsWithResults);
                        },
                        false: function (Builder $query) {
                            $showIdsWithResults = DB::connection('mysql_prev')
                                ->table('shows_results')
                                ->whereNotNull('ShowID')
                                ->distinct()
                                ->pluck('ShowID')
                                ->toArray();
                            return $query->whereNotIn('ShowsDB.id', $showIdsWithResults);
                        },
                        blank: fn(Builder $query) => $query,
                    ),
                TernaryFilter::make('is_assessment')
                    ->label(__('Assessment'))
                    ->trueLabel(__('Yes'))
                    ->falseLabel(__('No'))
                    ->queries(
                        true: fn(Builder $query) => $query->where(function (Builder $q) {
                            $q->orWhereRaw("TitleName REGEXP 'מבדק|מבחן'")
                                ->orWhereRaw("LongDesc REGEXP 'מבדק|מבחן'");
                        }),

                        false: fn(Builder $query) => $query->where(function (Builder $q) {
                            $q->where(fn($sub) => $sub->whereRaw("TitleName NOT REGEXP 'מבדק|מבחן'")->orWhereNull('TitleName'))
                                ->where(fn($sub) => $sub->whereRaw("LongDesc NOT REGEXP 'מבדק|מבחן'")->orWhereNull('LongDesc'));
                        }),

                        blank: fn(Builder $query) => $query,
                    ),
                // Date Filters
                Filter::make('StartDate')
                    ->schema([
                        DatePicker::make('start_from')->label(__('Starts From')),
                        DatePicker::make('start_until')->label(__('Starts Until')),
                    ])
                    ->query(fn(Builder $query, array $data): Builder => $query
                        ->when($data['start_from'] ?? null, fn($q, $date) => $q->whereDate('StartDate', '>=', $date))
                        ->when($data['start_until'] ?? null, fn($q, $date) => $q->whereDate('StartDate', '<=', $date))
                    ),

                Filter::make('EndDate')
                    ->schema([
                        DatePicker::make('end_from')->label(__('Ends From')),
                        DatePicker::make('end_until')->label(__('Ends Until')),
                    ])
                    ->query(fn(Builder $query, array $data): Builder => $query
                        ->when($data['end_from'] ?? null, fn($q, $date) => $q->whereDate('EndDate', '>=', $date))
                        ->when($data['end_until'] ?? null, fn($q, $date) => $q->whereDate('EndDate', '<=', $date))
                    ),

                Filter::make('EndRegistrationDate')
                    ->schema([
                        DatePicker::make('reg_from')->label(__('Reg. Ends From')),
                        DatePicker::make('reg_until')->label(__('Reg. Ends Until')),
                    ])
                    ->query(fn(Builder $query, array $data): Builder => $query
                        ->when($data['reg_from'] ?? null, fn($q, $date) => $q->whereDate('EndRegistrationDate', '>=', $date))
                        ->when($data['reg_until'] ?? null, fn($q, $date) => $q->whereDate('EndRegistrationDate', '<=', $date))
                    ),
                TrashedFilter::make('trashed'),
            ])
            ->recordActions([
                ViewAction::make(),
//                Action::make('view_dogs')
//                    ->label(false)
//                    ->tooltip(__('Show Dogs'))
//                    ->icon('fas-dog')
//                    ->color('info')
//                    ->badge(fn (PrevShow $record): int => $record->show_dogs_count ?? 0)
//                    ->url(fn (PrevShow $record): string => PrevShowDogResource::getUrl('index', [
//                        'tableFilters' => [
//                            'ShowID' => ['value' => $record->id],
//                        ],
//                    ])),
//
//                Action::make('view_results')
//                    ->label(false)
//                    ->tooltip(__('Results'))
//                    ->icon('fas-check-circle')
//                    ->color('warning')
//                    ->badge(fn (PrevShow $record): int => $record->results_count ?? 0)
//                    ->url(fn (PrevShow $record): string => PrevShowResultResource::getUrl('index', [
//                        'tableFilters' => [
//                            'ShowID' => ['value' => $record->id],
//                        ],
//                    ])),
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort(function (Builder $query): Builder {
                return $query
                    ->orderBy('StartDate', 'desc')
                    ->orderBy('id', 'desc');
            });
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('ShowTabs')
                    ->tabs([
                        Tab::make(__('Overview'))
                            ->schema([
                                Grid::make(3)->schema([
                                    TextEntry::make('TitleName')->label(__('Show title'))->columnSpan(2),
                                    TextEntry::make('club.Name')->label(__('Club')),
                                    TextEntry::make('ShowType')
                                        ->label(__('Show type'))
                                        ->badge()
                                        ->icon(fn(PrevShow $r): ?string => $r->ShowType?->getIcon())
                                        ->color(fn(PrevShow $r): ?string => $r->ShowType?->getColor()),

                                    TextEntry::make('location')->label(__('Location')),
                                    IconEntry::make('ShowStatus')->label(__('Show Status'))->boolean(),
                                ]),
                                Grid::make(2)->schema([
                                    Section::make(__('Show Description'))
                                        ->schema([
                                            TextEntry::make('LongDesc')
                                                ->label(false)
                                                ->html(),
                                        ])->columnSpan(1),
                                    Section::make(__('Judges'))
                                        ->schema([
                                            TextEntry::make('judges.JudgeNameHE')
                                                ->label(false)
                                                ->separator('; '),
                                        ])->columnSpan(1),
                                ]),
                                Section::make(__('Counts'))
                                    ->schema([
                                        TextEntry::make('arenas_count')->label(__('Arenas'))->state(fn(PrevShow $record) => $record->arenas()->count()),
                                        TextEntry::make('classes_count')->label(__('Classes'))->state(fn(PrevShow $record) => $record->classes()->count()),
                                        TextEntry::make('registrations_count')->label(__('Registrations'))->state(fn(PrevShow $record) => $record->registrations()->count()),
                                        TextEntry::make('show_dogs_count')->label(__('Show Dogs'))->state(fn(PrevShow $record) => $record->showDogs()->count()),
                                        TextEntry::make('results_count')->label(__('Results'))->state(fn(PrevShow $record) => $record->results()->count()),
                                    ])->columns(5),
                            ]),
                        Tab::make(__('Dates'))
                            ->schema([
                                Grid::make(2)->schema([
                                    TextEntry::make('StartDate')->dateTime()->label(__('Starting at')),
                                    TextEntry::make('EndDate')->dateTime()->label(__('Ending at')),
                                    TextEntry::make('EndRegistrationDate')->date()->label(__('Registration ends')),
                                    TextEntry::make('CreationDateTime')->since()->label(__('Created')),
                                    TextEntry::make('ModificationDateTime')->since()->label(__('Updated')),
                                ]),
                            ]),
                        Tab::make(__('Pricing'))
                            ->schema([
                                Grid::make(3)->schema([
                                    TextEntry::make('ShowPrice')->money('ILS'),
                                    TextEntry::make('Dog2Price1')->money('ILS'),
                                    TextEntry::make('Dog2Price2')->money('ILS'),
                                    TextEntry::make('Dog2Price3')->money('ILS'),
                                    TextEntry::make('Dog2Price4')->money('ILS'),
                                    TextEntry::make('Dog2Price5')->money('ILS'),
                                    TextEntry::make('Dog2Price6')->money('ILS'),
                                    TextEntry::make('Dog2Price7')->money('ILS'),
                                    TextEntry::make('Dog2Price8')->money('ILS'),
                                    TextEntry::make('Dog2Price9')->money('ILS'),
                                    TextEntry::make('Dog2Price10')->money('ILS'),
                                ]),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPrevShows::route('/'),
            'create' => CreatePrevShow::route('/create'),
            'view' => ViewPrevShow::route('/{record}'),
            'edit' => EditPrevShow::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            PrevShowArenaRelationManager::class,
            PrevShowClassRelationManager::class,
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
