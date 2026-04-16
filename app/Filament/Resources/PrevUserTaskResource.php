<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Toggle;
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
use Filament\Actions\ExportAction;
use App\Filament\Resources\PrevUserTaskResource\Pages\ListPrevUserTasks;
use App\Filament\Resources\PrevUserTaskResource\Pages\CreatePrevUserTask;
use App\Filament\Resources\PrevUserTaskResource\Pages\ViewPrevUserTask;
use App\Filament\Resources\PrevUserTaskResource\Pages\EditPrevUserTask;
use App\Filament\Exports\PrevUserTaskExporter;
use App\Filament\Resources\PrevUserTaskResource\Pages;
use App\Models\PrevUser;
use App\Models\PrevUserTask;
use Filament\Forms;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PrevUserTaskResource extends Resource
{
    protected static ?string $model = PrevUserTask::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $recordTitleAttribute = 'task_name';

    public static function getModelLabel(): string
    {
        return __('User Task');
    }

    public static function getPluralModelLabel(): string
    {
        return __('User Tasks');
    }

    public static function getNavigationGroup(): string
    {
        return __('Users Management');
    }

    public static function getNavigationLabel(): string
    {
        return __('User Tasks');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Task details'))
                    ->schema([
                        TextInput::make('task_name')->label(__('Task Name'))->required()->maxLength(255),
                        TextInput::make('task_type')->label(__('Task Type'))->maxLength(255),
                        TextInput::make('status')->label(__('Status'))->maxLength(255),
                        TextInput::make('pedigree_color')->label(__('Pedigree Color'))->maxLength(255),
                        DateTimePicker::make('due_date_time')->label(__('Due Date Time'))->seconds(false),
                        DateTimePicker::make('done_date_time')->label(__('Done Date Time'))->seconds(false),
                        DatePicker::make('review_date')->label(__('Review Date')),
                        TextInput::make('review_place')->label(__('Review Place'))->maxLength(255),
                        TextInput::make('Req_final_mark')->label(__('Final Mark'))->numeric(),
                        Toggle::make('read_status')->label(__('Read Status')),
                        Toggle::make('is_editable')->label(__('Is Editable')),
                        Toggle::make('male_owner_agree')->label(__('Male Owner Agree')),
                        Textarea::make('full_details')->label(__('Full Details'))->rows(4)->columnSpanFull(),
                    ])
                    ->columns(4),
                Section::make(__('Relations'))
                    ->schema([
                        Select::make('manager_user_id')
                            ->label(__('Manager User'))
                            ->searchable()
                            ->getSearchResultsUsing(fn(string $search): array => PrevUser::selectOptions($search, 50))
                            ->getOptionLabelUsing(fn($value): ?string => PrevUser::query()->find($value)?->name),
                        Select::make('related_to_user_id')
                            ->label(__('Related User'))
                            ->searchable()
                            ->getSearchResultsUsing(fn(string $search): array => PrevUser::selectOptions($search, 50))
                            ->getOptionLabelUsing(fn($value): ?string => PrevUser::query()->find($value)?->name),
                        Select::make('related_breeding_process_id')
                            ->label(__('Breeding Process'))
                            ->relationship('breeding', 'id')
                            ->searchable()
                            ->getOptionLabelFromRecordUsing(fn(Model $record): string => '#' . $record->id),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label(__('ID'))->numeric(decimalPlaces: 0, thousandsSeparator: '')->sortable(),
                TextColumn::make('task_name')->label(__('Task Name'))->searchable()->wrap()->sortable(),
                TextColumn::make('task_type')->label(__('Task Type'))->toggleable(),
                TextColumn::make('managerUser.name')
                    ->label(__('Manager'))
                    ->sortable(['last_name', 'first_name'])
                    ->searchable(['first_name', 'last_name', 'first_name_en', 'last_name_en'], isIndividual: true, isGlobal: false)
                    ->toggleable(),
                TextColumn::make('relatedUser.name')
                    ->label(__('Related User'))
                    ->sortable(['last_name', 'first_name'])
                    ->searchable(['first_name', 'last_name', 'first_name_en', 'last_name_en'], isIndividual: true, isGlobal: false)
                    ->toggleable(),
                TextColumn::make('breeding.id')->label(__('Breeding'))->formatStateUsing(fn($state): ?string => $state ? '#' . $state : null)->toggleable(),
                TextColumn::make('status')->label(__('Status'))->badge()->sortable(),
                TextColumn::make('due_date_time')->label(__('Due'))->dateTime()->sortable(),
                TextColumn::make('done_date_time')->label(__('Done'))->dateTime()->sortable()->toggleable(),
                IconColumn::make('read_status')->label(__('Read'))->boolean(),
                IconColumn::make('is_editable')->label(__('Editable'))->boolean(),
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
                    ->exporter(PrevUserTaskExporter::class),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->headerActions([
                ExportAction::make()
                    ->label(__('Export All'))
                    ->icon('fas-file-export')
                    ->color('primary')
                    ->iconPosition('after')
                    ->exporter(PrevUserTaskExporter::class),
            ])
            ->defaultSort('due_date_time', 'desc')
            ->searchOnBlur()
            ->striped();
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Task details'))
                    ->schema([
                        TextEntry::make('task_name')->label(__('Task Name')),
                        TextEntry::make('task_type')->label(__('Task Type')),
                        TextEntry::make('status')->label(__('Status'))->badge(),
                        TextEntry::make('pedigree_color')->label(__('Pedigree Color')),
                        TextEntry::make('due_date_time')->label(__('Due Date Time'))->dateTime(),
                        TextEntry::make('done_date_time')->label(__('Done Date Time'))->dateTime()->placeholder('-'),
                        TextEntry::make('review_date')->label(__('Review Date'))->date()->placeholder('-'),
                        TextEntry::make('review_place')->label(__('Review Place')),
                        TextEntry::make('Req_final_mark')->label(__('Final Mark'))->placeholder('-'),
                        IconEntry::make('read_status')->label(__('Read Status'))->boolean(),
                        IconEntry::make('is_editable')->label(__('Is Editable'))->boolean(),
                        IconEntry::make('male_owner_agree')->label(__('Male Owner Agree'))->boolean(),
                        TextEntry::make('full_details')->label(__('Full Details'))->columnSpanFull(),
                    ])
                    ->columns(4),
                Section::make(__('Relations'))
                    ->schema([
                        TextEntry::make('managerUser.name')->label(__('Manager User')),
                        TextEntry::make('relatedUser.name')->label(__('Related User')),
                        TextEntry::make('breeding.id')->label(__('Breeding Process'))->formatStateUsing(fn($state): ?string => $state ? '#' . $state : null),
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
            'index' => ListPrevUserTasks::route('/'),
            'create' => CreatePrevUserTask::route('/create'),
            'view' => ViewPrevUserTask::route('/{record}'),
            'edit' => EditPrevUserTask::route('/{record}/edit'),
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
