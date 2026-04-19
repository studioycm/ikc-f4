<?php /** @noinspection PhpMultipleClassDeclarationsInspection */

namespace App\Filament\Resources\PrevUserActivities;

use BackedEnum;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use App\Filament\Resources\PrevUserActivities\Pages\ListPrevUserActivities;
use App\Filament\Resources\PrevUserActivities\Pages\CreatePrevUserActivity;
use App\Filament\Resources\PrevUserActivities\Pages\ViewPrevUserActivity;
use App\Filament\Resources\PrevUserActivities\Pages\EditPrevUserActivity;
use App\Filament\Resources\PrevUserActivities\Pages;
use App\Models\PrevUser;
use App\Models\PrevUserActivity;
use Filament\Forms;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Icons\Heroicon;

class PrevUserActivityResource extends Resource
{
    protected static ?string $model = PrevUserActivity::class;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedClock;

    protected static ?string $recordTitleAttribute = 'Activity_Type';

    public static function getModelLabel(): string
    {
        return __('User Activity');
    }

    public static function getPluralModelLabel(): string
    {
        return __('User Activities');
    }

    public static function getNavigationGroup(): string
    {
        return __('Users Management');
    }

    public static function getNavigationLabel(): string
    {
        return __('User Activities');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Activity details'))
                    ->schema([
                        Select::make('UserID')
                            ->label(__('User'))
                            ->searchable()
                            ->getSearchResultsUsing(fn(string $search): array => PrevUser::selectOptions($search, 50))
                            ->getOptionLabelUsing(fn($value): ?string => PrevUser::query()->find($value)?->name),
                        Select::make('CreatedBy')
                            ->label(__('Created By'))
                            ->searchable()
                            ->getSearchResultsUsing(fn(string $search): array => PrevUser::selectOptions($search, 50))
                            ->getOptionLabelUsing(fn($value): ?string => PrevUser::query()->find($value)?->name),
                        TextInput::make('Activity_Type')
                            ->label(__('Activity Type'))
                            ->required()
                            ->maxLength(255),
                        TextInput::make('UserIP')
                            ->label(__('User IP'))
                            ->maxLength(255),
                        DateTimePicker::make('CreationDateTime')
                            ->label(__('Created At'))
                            ->seconds(false),
                        Toggle::make('Is_Payment')->label(__('Is Payment')),
                        Toggle::make('Is_Show')->label(__('Is Show')),
                        Toggle::make('Is_Study')->label(__('Is Study')),
                        Textarea::make('Activity_Desc')->label(__('Activity Description'))->rows(3)->columnSpanFull(),
                        Textarea::make('Activity_Log')->label(__('Activity Log'))->rows(6)->columnSpanFull(),
                    ])
                    ->columns(4),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label(__('ID'))->numeric(decimalPlaces: 0, thousandsSeparator: '')->sortable(),
                TextColumn::make('user.name')
                    ->label(__('User'))
                    ->sortable(['last_name', 'first_name'])
                    ->searchable(['first_name', 'last_name', 'first_name_en', 'last_name_en'], isIndividual: true, isGlobal: false)
                    ->toggleable(),
                TextColumn::make('Activity_Type')->label(__('Activity Type'))->searchable()->sortable(),
                TextColumn::make('Activity_Desc')->label(__('Description'))->wrap()->limit(50)->toggleable(),
                TextColumn::make('CreationDateTime')->label(__('Created At'))->dateTime()->sortable(),
                TextColumn::make('createdBy.name')
                    ->label(__('Created By'))
                    ->sortable(['last_name', 'first_name'])
                    ->searchable(['first_name', 'last_name', 'first_name_en', 'last_name_en'], isIndividual: true, isGlobal: false)
                    ->toggleable(),
                IconColumn::make('Is_Payment')->label(__('Payment'))->boolean(),
                IconColumn::make('Is_Show')->label(__('Show'))->boolean(),
                IconColumn::make('Is_Study')->label(__('Study'))->boolean(),
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
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('CreationDateTime', 'desc')
            ->searchOnBlur()
            ->striped();
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Activity details'))
                    ->schema([
                        TextEntry::make('user.name')->label(__('User')),
                        TextEntry::make('createdBy.name')->label(__('Created By')),
                        TextEntry::make('Activity_Type')->label(__('Activity Type')),
                        TextEntry::make('UserIP')->label(__('User IP')),
                        TextEntry::make('CreationDateTime')->label(__('Created At'))->dateTime(),
                        IconEntry::make('Is_Payment')->label(__('Is Payment'))->boolean(),
                        IconEntry::make('Is_Show')->label(__('Is Show'))->boolean(),
                        IconEntry::make('Is_Study')->label(__('Is Study'))->boolean(),
                        TextEntry::make('Activity_Desc')->label(__('Activity Description'))->columnSpanFull(),
                        TextEntry::make('Activity_Log')->label(__('Activity Log'))->columnSpanFull(),
                        TextEntry::make('deleted_at')->label(__('Deleted at'))->since()->dateTimeTooltip()->placeholder('-'),
                    ])
                    ->columns(4),
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
            'index' => ListPrevUserActivities::route('/'),
            'create' => CreatePrevUserActivity::route('/create'),
            'view' => ViewPrevUserActivity::route('/{record}'),
            'edit' => EditPrevUserActivity::route('/{record}/edit'),
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
