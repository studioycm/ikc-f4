<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Grid;
use App\Filament\Resources\PrevShowClassResource\Pages\ListPrevShowClasses;
use App\Filament\Resources\PrevShowClassResource\Pages\CreatePrevShowClass;
use App\Filament\Resources\PrevShowClassResource\Pages\ViewPrevShowClass;
use App\Filament\Resources\PrevShowClassResource\Pages\EditPrevShowClass;
use App\Filament\Resources\PrevShowArenaResource as ArenaRes;
use App\Filament\Resources\PrevShowClassResource\Pages;
use App\Filament\Resources\PrevShowResource as ShowRes;
use App\Models\PrevShowClass;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PrevShowClassResource extends Resource
{
    protected static ?string $model = PrevShowClass::class;

    protected static ?string $slug = 'prev-show-classes';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 80;

    public static function getModelLabel(): string
    {
        return __('Show Class');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Show Classes');
    }

    public static function getNavigationGroup(): string
    {
        return __('Shows Management');
    }

    public static function getNavigationLabel(): string
    {
        return __('Show Classes');
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

                TextInput::make('ClassName'),

                TextInput::make('Age_FromMonths')
                    ->numeric(),

                TextInput::make('Age_TillMonths')
                    ->numeric(),

                TextInput::make('SpecialClassID')
                    ->numeric(),

                TextInput::make('HairID')
                    ->numeric(),

                TextInput::make('ColorID')
                    ->numeric(),

                TextInput::make('ShowRaceID')
                    ->numeric(),

                TextInput::make('ShowID')
                    ->numeric(),

                TextInput::make('ShowArenaID')
                    ->numeric(),

                TextInput::make('Remarks'),

                TextInput::make('Status')
                    ->numeric(),

                TextInput::make('OrderID')
                    ->numeric(),

                TextInput::make('IsChampClass')
                    ->numeric(),

                TextInput::make('IsWorkingClass')
                    ->numeric(),

                TextInput::make('IsOpenClass')
                    ->numeric(),

                TextInput::make('IsVeteranClass')
                    ->numeric(),

                TextInput::make('GenderID')
                    ->numeric(),

                TextInput::make('BreedID')
                    ->numeric(),

                TextInput::make('ShowMainArenaID')
                    ->numeric(),

                TextInput::make('AwardIDClass')
                    ->numeric(),

                TextInput::make('IsCouplesClass')
                    ->numeric(),

                TextInput::make('IsZezaimClass')
                    ->numeric(),

                TextInput::make('IsYoungDriverClass')
                    ->numeric(),

                TextInput::make('IsBgidulClass')
                    ->numeric(),

                TextInput::make('ShowArenaID')
                    ->required()
                    ->integer(),

                TextInput::make('ShowID')
                    ->required()
                    ->integer(),

                Placeholder::make('created_at')
                    ->label(__('Created Date'))
                    ->content(fn(?PrevShowClass $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label(__('Last Modified Date'))
                    ->content(fn(?PrevShowClass $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ClassName')
                    ->label(__('Class type'))
                    ->description(fn(PrevShowClass $r) => $r->DataID ?? '—')
                    ->searchable(['ClassName', 'DataID'], isIndividual: true, isGlobal: false)
                    ->sortable(['id', 'ShowID']),

                TextColumn::make('arena.GroupName')
                    ->label(__('Arena name'))
                    ->description(fn(PrevShowClass $r) => $r->ShowArenaID ?? '—')
                    ->url(fn(PrevShowClass $r) => $r->ShowArenaID ? ArenaRes::getUrl('view', ['record' => $r->ShowArenaID]) : null)
                    ->openUrlInNewTab()
                    ->toggleable(),

                TextColumn::make('show.TitleName')
                    ->label(__('Show Name'))
                    ->description(fn(PrevShowClass $r) => $r->ShowID ?? '—')
                    ->url(fn(PrevShowClass $r) => $r->ShowID ? ShowRes::getUrl('view', ['record' => $r->ShowID]) : null)
                    ->openUrlInNewTab()
                    ->toggleable(),
            ])
            ->filters([
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('ClassTabs')->tabs([
                Tab::make(__('Overview'))
                    ->schema([
                        Grid::make(3)->schema([
                            TextEntry::make('ClassName')->label(__('Class Name')),
                            TextEntry::make('GenderID')->label(__('Gender')),
                            TextEntry::make('BreedID')->label(__('Breed (code)')),
                            TextEntry::make('ShowID')->label(__('Show')),
                            TextEntry::make('ShowArenaID')->label(__('Arena ID')),
                        ]),
                    ]),
                Tab::make(__('Age'))
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('Age_FromMonths')->label(__('From (months)')),
                            TextEntry::make('Age_TillMonths')->label(__('Till (months)')),
                        ]),
                    ]),
                Tab::make(__('Flags'))
                    ->schema([
                        Grid::make(3)->schema([
                            TextEntry::make('IsChampClass'),
                            TextEntry::make('IsWorkingClass'),
                            TextEntry::make('IsOpenClass'),
                            TextEntry::make('IsVeteranClass'),
                            TextEntry::make('IsCouplesClass'),
                            TextEntry::make('IsZezaimClass'),
                            TextEntry::make('IsYoungDriverClass'),
                            TextEntry::make('IsBgidulClass'),
                        ]),
                    ]),
            ])->columnSpanFull(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPrevShowClasses::route('/'),
            'create' => CreatePrevShowClass::route('/create'),
            'view' => ViewPrevShowClass::route('/{record}'),
            'edit' => EditPrevShowClass::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }
}
