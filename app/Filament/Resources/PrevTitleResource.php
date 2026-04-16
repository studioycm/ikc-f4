<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
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
use App\Filament\Resources\PrevTitleResource\Pages\ListPrevTitles;
use App\Filament\Resources\PrevTitleResource\Pages\CreatePrevTitle;
use App\Filament\Resources\PrevTitleResource\Pages\ViewPrevTitle;
use App\Filament\Resources\PrevTitleResource\Pages\EditPrevTitle;
use App\Filament\Exports\PrevTitleExporter;
use App\Filament\Imports\PrevTitleImporter;

// use App\Filament\Resources\PrevTitleResource\RelationManagers;
use App\Filament\Resources\PrevTitleResource\Pages;
use App\Models\PrevTitle;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PrevTitleResource extends Resource
{
    protected static ?string $model = PrevTitle::class;

    public static function getModelLabel(): string
    {
        return __('Title');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Titles');
    }

    public static function getNavigationGroup(): string
    {
        return __('Shows Management');
    }

    public static function getNavigationLabel(): string
    {
        return __('Title Types');
    }

    protected static ?int $navigationSort = 5;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    //    public static function getNavigationBadge(): ?string
    //    {
    //        return (string) static::$model::count();
    //    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(6)->schema([
                    TextInput::make('DataID')
                        ->required()
                        ->numeric(),
                    TextInput::make('TitleCode')
                        ->numeric(),
                    TextInput::make('TitleName')
                        ->maxLength(200),
                    TextInput::make('TitleDesc')
                        ->maxLength(200),
                    DateTimePicker::make('ModificationDateTime'),
                    DateTimePicker::make('CreationDateTime'),
                    Textarea::make('Remark')
                        ->columnSpanFull(),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                return $query
                    ->withCount('awarding');
            })
            ->columns([
                TextColumn::make('id')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('DataID')
                    ->label(__('DataID'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('TitleCode')
                    ->label(__('Title Code'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('TitleName')
                    ->label(__('Title Name'))
                    ->sortable()
                    ->searchable(isIndividual: true)
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('TitleDesc')
                    ->label(__('Description'))
                    ->searchable(isIndividual: true)
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('Remark')
                    ->label(__('Remark'))
                    ->searchable(isIndividual: true)
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('awarding_count')
                    ->label(__('Awarded'))
                    ->counts('awarding')
                    ->numeric()
                    ->sortable(['awarding_count'])
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('CreationDateTime')
                    ->label(__('Create Date'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('ModificationDateTime')
                    ->label(__('Modify Date'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('Created at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('Updated at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->label(__('Deleted at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ],
                layout: FiltersLayout::AboveContentCollapsible
            )
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
                    ->exporter(PrevTitleExporter::class)
                    ->chunkSize(50)
                    ->modifyQueryUsing(fn (Builder $query) => $query->withCount('dogs')),
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
                    ->importer(PrevTitleImporter::class),
                ExportAction::make()
                    ->label(__('Export All'))
                    ->icon('fas-file-export')
                    ->color('primary')
                    ->iconPosition('after')
                    ->exporter(PrevTitleExporter::class)
                    ->chunkSize(50)
                    ->modifyQueryUsing(fn (Builder $query) => $query->withCount('dogs')),
            ])
            ->paginated([10, 25, 50, 100, 200, 250, 'all'])
            ->defaultPaginationPageOption(10)
            ->defaultSort('TitleName', 'asc')
            ->searchOnBlur()
            ->striped()
            ->deferLoading()
            // ->recordUrl(fn (PrevTitle $record): string => route('filament.admin.resources.prev-titles.view', $record), shouldOpenInNewTab: false,);
            ->recordUrl(false)
            ->filtersFormColumns(3);
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
            'index' => ListPrevTitles::route('/'),
            'create' => CreatePrevTitle::route('/create'),
            'view' => ViewPrevTitle::route('/{record}'),
            'edit' => EditPrevTitle::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class]);
    }
}
