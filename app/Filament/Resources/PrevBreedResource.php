<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\ToggleButtons;
use Filament\Actions\EditAction;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ImportAction;
use Filament\Actions\ExportAction;
use App\Filament\Resources\PrevBreedResource\Pages\ListPrevBreeds;
use App\Filament\Resources\PrevBreedResource\Pages\CreatePrevBreed;
use App\Filament\Resources\PrevBreedResource\Pages\EditPrevBreed;
use App\Filament\Resources\PrevBreedResource\Pages\ViewPrevBreed;
use App\Filament\Resources\PrevBreedResource\Widgets\BreedStats;
use App\Filament\Exports\PrevBreedExporter;
use App\Filament\Imports\PrevBreedImporter;
use App\Filament\Resources\PrevBreedResource\Pages;
use App\Models\PrevBreed;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PrevBreedResource extends Resource
{
    protected static ?string $model = PrevBreed::class;

    public static function getModelLabel(): string
    {
        return __('Breed');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Breeds');
    }

    public static function getNavigationGroup(): string
    {
        return __('dog/model/general.labels.navigation_group');
    }

    public static function getNavigationLabel(): string
    {
        return __('Breeds');
    }

    protected static ?int $navigationSort = 2;

    protected static string | \BackedEnum | null $navigationIcon = 'fas-dna';

    //    public static function getNavigationBadge(): ?string
    //    {
    //        return static::getModel()::count();
    //    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('DataID')
                    ->numeric(),
                DateTimePicker::make('ModificationDateTime'),
                DateTimePicker::make('CreationDateTime'),
                TextInput::make('BreedName')
                    ->maxLength(200),
                TextInput::make('BreedCode')
                    ->numeric(),
                Textarea::make('Desc')
                    ->columnSpanFull(),
                TextInput::make('BreedNameEN')
                    ->maxLength(200),
                TextInput::make('GroupID')
                    ->numeric(),
                TextInput::make('FCICODE')
                    ->maxLength(200),
                TextInput::make('UserManagerID')
                    ->numeric(),
                TextInput::make('ClubManagerID')
                    ->numeric(),
                TextInput::make('fci_group')
                    ->maxLength(50),
                TextInput::make('status')
                    ->maxLength(50),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                return $query
                    ->with(['promoters'])
                    ->withCount(['dogs']);
            })
            ->columns([
                TextColumn::make('id')
                    ->label(__('ID'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('BreedName')
                    ->label(__('Hebrew Name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('BreedNameEN')
                    ->label(__('English Name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('BreedCode')
                    ->label(__('Breed Code'))
                    ->numeric()
                    ->sortable()
                    ->searchable(isGlobal: false, isIndividual: true),
                TextColumn::make('dogs_count')
                    ->label(__('Dogs Count'))
                    ->counts('dogs')
                    ->numeric()
                    ->sortable(['dogs_count'])
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('FCICODE')
                    ->label(__('FCI Code'))
                    ->sortable()
                    ->searchable(isGlobal: false, isIndividual: true)
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('fci_group')
                    ->label(__('FCI Group'))
                    ->sortable()
                    ->searchable(isGlobal: false, isIndividual: true)
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('status')
                    ->label(__('Status'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('DataID')
                    ->label(__('Previous ID'))
                    ->numeric()
                    ->sortable()
                    ->searchable(isGlobal: false, isIndividual: true)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('ModificationDateTime')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('CreationDateTime')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('GroupID')
                    ->label(__('Previous GroupID'))
                    ->numeric()
                    ->sortable()
                    ->searchable(isGlobal: false, isIndividual: true)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('promoters.full_name')
                    ->label(__('Promoters'))
                    ->listWithLineBreaks()
                    ->limitList(3)
                    ->expandableLimitedList()
                    ->searchable(['first_name', 'last_name', 'first_name_en', 'last_name_en'], isGlobal: false, isIndividual: true)
                    ->toggleable(isToggledHiddenByDefault: false),
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
            ])
            ->filters([
                Filter::make('trashed')
                    ->schema([
                        ToggleButtons::make('trashed')
                            ->label(__('Trashed'))
                            ->options([
                                'not_deleted' => 'Not Deleted',
                                'deleted' => 'Deleted',
                                'all' => 'All',
                            ])
                            ->colors([
                                'not_deleted' => 'success',
                                'deleted' => 'danger',
                                'all' => 'gray',
                            ])
                            ->default('not_deleted'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (empty($data['trashed']) || $data['trashed'] === 'all') {
                            return $query;
                        }

                        return match ($data['trashed']) {
                            'deleted' => $query->onlyTrashed(),
                            'not_deleted' => $query->withoutTrashed(),
                        };
                    }),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                ExportBulkAction::make()
                    ->label(__('Export Selected'))
                    ->icon('fas-file-export')
                    ->color('primary')
                    ->iconPosition('after')
                    ->exporter(PrevBreedExporter::class),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                ImportAction::make()
                    ->label(__('Import'))
                    ->icon('fas-file-import')
                    ->color('gray')
                    ->iconPosition('after')
                    ->importer(PrevBreedImporter::class),
                ExportAction::make()
                    ->label(__('Export All'))
                    ->icon('fas-file-export')
                    ->color('primary')
                    ->iconPosition('after')
                    ->exporter(PrevBreedExporter::class),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
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
            'index' => ListPrevBreeds::route('/'),
            'create' => CreatePrevBreed::route('/create'),
            'edit' => EditPrevBreed::route('/{record}/edit'),
            'view' => ViewPrevBreed::route('/{record}'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            BreedStats::class,
        ];
    }
}
