<?php

namespace App\Filament\Resources\PrevUserRequests;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ExportAction;
use Filament\Tables\Enums\RecordActionsPosition;
use App\Filament\Resources\PrevUserRequests\Pages\ListPrevUserRequests;
use App\Filament\Resources\PrevUserRequests\Pages\CreatePrevUserRequest;
use App\Filament\Resources\PrevUserRequests\Pages\ViewPrevUserRequest;
use App\Filament\Resources\PrevUserRequests\Pages\EditPrevUserRequest;
use App\Enums\Legacy\LegacySagirPrefix;
use App\Enums\Legacy\LegacyUserRequestChampionType;
use App\Enums\Legacy\LegacyUserRequestPaperType;
use App\Enums\Legacy\LegacyUserRequestTopic;
use App\Filament\Exports\PrevUserRequestExporter;
use App\Filament\Resources\PrevUserRequests\Pages;
use App\Models\PrevUser;
use App\Models\PrevUserRequest;
use Filament\Forms;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Number;
use Filament\Support\Icons\Heroicon;

class PrevUserRequestResource extends Resource
{
    protected static ?string $model = PrevUserRequest::class;

    protected static string | \BackedEnum | null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $recordTitleAttribute = 'topic';

    public static function getRecordTitle(?Model $record): string|Htmlable|null
    {
        return $record->topic->getLabel();
    }

    public static function getModelLabel(): string
    {
        return __('User Request');
    }

    public static function getPluralModelLabel(): string
    {
        return __('User Requests');
    }

    public static function getNavigationGroup(): string
    {
        return __('Users Management');
    }

    public static function getNavigationLabel(): string
    {
        return __('User Requests');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Requester details'))
                    ->schema([
                        TextInput::make('first_name')->label(__('First Name'))->maxLength(255),
                        TextInput::make('last_name')->label(__('Last Name'))->maxLength(255),
                        TextInput::make('email')->label(__('Email'))->email()->maxLength(255),
                        TextInput::make('mobile_phone')->label(__('Mobile Phone'))->tel()->maxLength(255),
                        TextInput::make('mobile_prefix')->label(__('Mobile Prefix'))->maxLength(50),
                        Textarea::make('full_address')->label(__('Full Address'))->rows(2)->columnSpanFull(),
                        TextInput::make('street')->label(__('Street'))->maxLength(255),
                        TextInput::make('number')->label(__('Number'))->maxLength(255),
                        TextInput::make('city')->label(__('City'))->maxLength(255),
                    ])
                    ->columns(3),
                Section::make(__('Request details'))
                    ->schema([
                        Select::make('club_id')
                            ->label(__('Club'))
                            ->relationship('club', 'Name')
                            ->searchable(['Name', 'EngName'])
                            ->preload()
                            ->getOptionLabelFromRecordUsing(fn(Model $record): string => $record->Name ?? $record->EngName ?? (string)$record->id),
                        Select::make('owner_id')
                            ->label(__('Owner'))
                            ->searchable()
                            ->getSearchResultsUsing(fn(string $search): array => PrevUser::selectOptions($search, 50))
                            ->getOptionLabelUsing(fn($value): ?string => PrevUser::query()->find($value)?->name),
                        Select::make('DoneByUserID')
                            ->label(__('Done By'))
                            ->searchable()
                            ->getSearchResultsUsing(fn(string $search): array => PrevUser::selectOptions($search, 50))
                            ->getOptionLabelUsing(fn($value): ?string => PrevUser::query()->find($value)?->name),
                        Select::make('topic')
                            ->label(__('Topic'))
                            ->options(LegacyUserRequestTopic::class),
                        TextInput::make('status')->label(__('Status'))->maxLength(255),
                        TextInput::make('payment_by')->label(__('Payment By'))->maxLength(255),
                        TextInput::make('payment_incerments')->label(__('Payment Increments'))->numeric()->minValue(0),
                        TextInput::make('total_amount')
                            ->label(__('Cost'))
                            ->numeric()
                            ->minValue(0),
                        TextInput::make('payment_approval_id')->label(__('Payment Approval Number'))->maxLength(255),
                        TextInput::make('last_4_digits')->label(__('Last 4 Digits'))->maxLength(10),
                        DateTimePicker::make('record_date_time')->label(__('Recorded at'))->seconds(false),
                        DateTimePicker::make('payment_date_time')->label(__('Payment Date'))->seconds(false),
                        Toggle::make('approve_1')->label(__('Approve 1')),
                        Toggle::make('approve_2')->label(__('Approve 2')),
                        Toggle::make('approve_3')->label(__('Approve 3')),
                        Toggle::make('IsDone')->label(__('Is Done')),
                        DateTimePicker::make('DoneDate')->label(__('Done Date'))->seconds(false),
                    ])
                    ->columns(4),
                Section::make(__('Dog and request metadata'))
                    ->schema([
                        TextInput::make('owner_name')->label(__('Owner Name'))->maxLength(255),
                        TextInput::make('dog_name')->label(__('Dog name'))->maxLength(255),
                        Select::make('sagirID')
                            ->label(__('Dog Record'))
                            ->relationship('dog', 'SagirID')
                            ->searchable(['SagirID', 'Heb_Name', 'Eng_Name'])
                            ->getOptionLabelFromRecordUsing(fn(Model $record): string => $record->full_name . ' #' . $record->SagirID),
                        TextInput::make('certificate_type')->label(__('Certificate Type'))->maxLength(255),
                        TextInput::make('shipping')->label(__('Shipping'))->maxLength(255),
                        TextInput::make('shipping_type_id')->label(__('Shipping Type'))->numeric(),
                        ToggleButtons::make('paper_request_type')
                            ->label(__('Paper Request Type'))
                            ->options(LegacyUserRequestPaperType::class)
                            ->grouped(),
                        TextInput::make('champion_certificate_type')->label(__('Champion Certificate Type'))->maxLength(255),
                        TextInput::make('agra_city')->label(__('Agra City'))->maxLength(255),
                        TextInput::make('dog1_chip_number')->label(__('Dog 1 Chip Number'))->maxLength(255),
                        TextInput::make('dog2_chip_number')->label(__('Dog 2 Chip Number'))->maxLength(255),
                        TextInput::make('dog3_chip_number')->label(__('Dog 3 Chip Number'))->maxLength(255),
                        DatePicker::make('dog1_vaccine_date')->label(__('Dog 1 Vaccine Date')),
                        DatePicker::make('dog2_vaccine_date')->label(__('Dog 2 Vaccine Date')),
                        DatePicker::make('dog3_vaccine_date')->label(__('Dog 3 Vaccine Date')),
                        Textarea::make('breeding_abroad_file')->label(__('Breeding Abroad File'))->rows(2)->columnSpanFull(),
                    ])
                    ->columns(4),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query) => $query->with(
                ['owner', 'dog', 'doneBy', 'club', 'vetAuth']
            ))
            ->defaultSort('updated_at', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label(__('ID'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(),
                TextColumn::make('request_status')
                    ->label(__('Status'))
                    ->state(fn(PrevUserRequest $record): string => $record->IsDone ? __('Done') : __('Pending'))
                    ->badge()
                    ->color(fn(string $state, PrevUserRequest $record): string => $record->IsDone ? 'success' : 'warning'),
                TextColumn::make('status')
                    ->label(__('Payment Status'))
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending payment' => 'warning',
                        'payment done' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'pending payment' => __('Pending Payment'),
                        'payment done' => __('Payment Done'),
                        default => $state,
                    })
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('topic')
                    ->label(__('Topic'))
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->sortable()
                    ->badge()
                    ->description(function ($state, PrevUserRequest $record): ?string {
                        if ($state === null) {
                            return __('Topic') . ' ' . __('Missing');
                        }

                        return match ($state->value) {
                            'pedigree_paper_request' => $record->paper_request_type
                                ? $record->paper_request_type->getLabel()
                                : __('Pedigree Type') . ' ' . __('Missing'),

                            'champion_diploma_request' => $record->champion_certificate_type
                                ? $record->champion_certificate_type->getLabel()
                                : __('Champion Certificate') . ' ' . __('Missing'),

                            'Payment of pelvic / elbow photo decoding' => $record->total_amount
                                ? Number::currency($record->total_amount, in: 'ILS', locale: 'he_IL', precision: 0)
                                : __('Price') . ' ' . __('Missing'),
                            'agra_form' => $record->vetAuth
                                ? $record->vetAuth->name . ' (' . $record->vetAuth->vet_email . ')'
                                : __('Veterinarian Authority') . ' ' . __('Missing'),
                            'young_rider_registration' => $record->kids_name
                                ? $record->kids_name . ($record->class ? ' | ' . $record->class : '') . ($record->birth_date ? ' (' . $record->birth_date->format('Y-m-d') . ')' : '')
                                : __("Kid's Name") . ' ' . __('Missing'),
                            default => '',
                        };
                    })
                    ->toggleable(),
                TextColumn::make('paper_request_type')
                    ->label(__('Pedigree Type'))
                    ->badge()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('champion_certificate_type')
                    ->label(__('Champion Certificate'))
                    ->badge()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('vetAuth.name')
                    ->label(__('Veterinarian Authority'))
                    ->sortable()
                    ->searchable(['agra_cities.name', 'agra_cities.vet_email'], isIndividual: true, isGlobal: false)
                    ->description(fn(PrevUserRequest $record): ?string => $record->vetAuth?->vet_email ?? __('Missing') . ' ' . __('Email'))
                    ->toggleable(),
                TextColumn::make('club.Name')
                    ->label(__('Club'))
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('requester_name')
                    ->label(__('User Name'))
                    ->description(fn(PrevUserRequest $record): string => $record->mobile_phone ?? $record->email ?? __('Missing') . ' ' . __('Phone'))
                    ->searchable(['public_registration.first_name', 'public_registration.last_name'], isIndividual: true, isGlobal: false)
                    ->sortable(['public_registration.first_name', 'public_registration.last_name'])
                    ->toggleable(),
                TextColumn::make('resolved_prev_user')
                    ->label(__('Resolved User'))
                    ->state(fn(PrevUserRequest $record): ?string => $record->resolvedPrevUser()?->name)
                    ->description(fn(PrevUserRequest $record): ?string => $record->resolvedPrevUser()?->mobile_phone ?? $record->resolvedPrevUser()?->phone ?? $record->resolvedPrevUser()?->email)
                    ->toggleable(),
                TextColumn::make('dog.SagirID')
                    ->label(__('dog/model/general.labels.singular'))
                    ->description(fn(PrevUserRequest $record): ?string => $record->dog?->full_name)
                    ->sortable()
                    ->searchable(['DogsDB.SagirID', 'DogsDB.eng_name', 'DogsDB.heb_name'], isIndividual: true, isGlobal: false)
                    ->toggleable(),
                TextColumn::make('owner_name')
                    ->label(__('Owner Name'))
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('owner.name')
                    ->label(__('Owner'))
                    ->sortable(['users.last_name', 'users.first_name'])
                    ->searchable(['users.first_name', 'users.last_name', 'users.first_name_en', 'users.last_name_en', 'users.mobile_phone'], isIndividual: true, isGlobal: false)
                    ->toggleable(),
                TextColumn::make('total_amount')
                    ->label(__('Cost'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: ',')
                    ->money(currency: 'ILS')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('payment_date_time')
                    ->label(__('Payment Date'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('payment_approval_id')
                    ->label(__('Payment Approval Number'))
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(),
                TextColumn::make('record_date_time')
                    ->label(__('Recorded at'))
                    ->date()
                    ->sortable()
                    ->toggleable(),
                IconColumn::make('IsDone')
                    ->label(__('Done'))
                    ->boolean()
                    ->toggleable(),
                TextColumn::make('doneBy.name')
                    ->label(__('Done By'))
                    ->sortable(['users.first_name', 'users.last_name'])
                    ->searchable(['users.first_name', 'users.last_name', 'users.first_name_en', 'users.last_name_en', 'users.mobile_phone'], isIndividual: true, isGlobal: false)
                    ->toggleable(),
                TextColumn::make('DoneDate')
                    ->label(__('Done Date'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label(__('Created at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('updated_at')
                    ->label(__('Updated at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('deleted_at')
                    ->label(__('Deleted at'))
                    ->since()
                    ->dateTimeTooltip()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options([
                        'pending payment' => __('Pending Payment'),
                        'payment done' => __('Payment Done'),
                    ]),
                SelectFilter::make('club')
                    ->label(__('Club'))
                    ->relationship('club', 'Name')
                    ->searchable(['Name', 'EngName'])
                    ->multiple()
                    ->preload(),
                SelectFilter::make('topic')
                    ->label(__('Topic'))
                    ->options(LegacyUserRequestTopic::class)
                    ->multiple(),
                SelectFilter::make('paper_request_type')
                    ->label(__('Pedigree Type'))
                    ->options(LegacyUserRequestPaperType::class),
                SelectFilter::make('champion_certificate_type')
                    ->label(__('Champion Certificate'))
                    ->options(LegacyUserRequestChampionType::class)
                    ->multiple(),
                SelectFilter::make('dog.sagir_prefix')
                    ->label(__('Sagir Prefix'))
                    ->options(LegacySagirPrefix::class),
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
                    ->exporter(PrevUserRequestExporter::class),
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
                    ->exporter(PrevUserRequestExporter::class),
            ])
            ->defaultSort('record_date_time', 'desc')
            ->searchOnBlur()
            ->striped()
            ->recordActionsPosition(RecordActionsPosition::BeforeColumns);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->columns(4)
            ->components([
                Section::make(__('Request details'))
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('topic')
                            ->label(__('Topic'))
                            ->badge()
                            ->inlineLabel(),
                        TextEntry::make('champion_certificate_type')
                            ->label(__('Certificate Type'))
                            ->badge()
                            ->hidden(fn(PrevUserRequest $record) => $record->champion_certificate_type === null)
                            ->inlineLabel(),
                        TextEntry::make('paper_request_type')
                            ->label(__('Paper Request Type'))
                            ->badge()
                            ->hidden(fn(PrevUserRequest $record) => $record->paper_request_type === null)
                            ->inlineLabel(),
                        TextEntry::make('shipping')->label(__('Shipping'))
                            ->visible(fn(PrevUserRequest $record) => filled($record->shipping))
                            ->inlineLabel(),
                        TextEntry::make('shipping_type_id')->label(__('Shipping Type'))
                            ->visible(fn(PrevUserRequest $record) => filled($record->shipping_type_id))
                            ->inlineLabel(),
                        TextEntry::make('club.Name')->label(__('Club'))
                            ->hidden(fn(PrevUserRequest $record) => $record->club === null)
                            ->inlineLabel(),
                        TextEntry::make('vetAuth.name')->label(__('Veterinarian Authority'))
                            ->tooltip(fn(PrevUserRequest $record) => $record->vetAuth?->vet_email)
                            ->copyable()
                            ->copyableState(fn(PrevUserRequest $record) => $record->vetAuth?->vet_email)
                            ->copyMessage(__('filament::components/copyable.messages.copied'))
                            ->copyMessageDuration(1500)
                            ->hidden(fn(PrevUserRequest $record) => $record->vetAuth === null)
                            ->inlineLabel(),
                        TextEntry::make('kids_name')
                            ->label(__("Kid's Name"))
                            ->formatStateUsing(fn($state, PrevUserRequest $record) => $state . ($record->class ? ' | ' . $record->class : '') . ($record->birth_date ? ' (' . $record->birth_date->format('Y-m-d') . ')' : ''))
                            ->visible(fn(PrevUserRequest $record) => filled($record->kids_name))
                            ->inlineLabel(),
                        TextEntry::make('total_amount')
                            ->label(__('Cost'))
                            ->numeric(decimalPlaces: 0, thousandsSeparator: ',')
                            ->money('ILS', 0, 'he_IL')
                            ->inlineLabel(),
                        TextEntry::make('status')->label(__('Status'))->badge()
                            ->inlineLabel(),
                        TextEntry::make('payment_by')->label(__('Payment Type'))
                            ->visible(fn(PrevUserRequest $record) => filled($record->payment_by))
                            ->inlineLabel(),
                        TextEntry::make('payment_approval_id')->label(__('Payment Approval Number'))
                            ->inlineLabel(),
                    ])
                    ->columns(1),
                Section::make(__('Requester details'))
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('requesterName')->label(__('Name'))
                            ->inlineLabel(),
                        TextEntry::make('normalized_mobile')->label(__('Mobile Phone'))
                            ->copyable()
                            ->copyableState(fn(PrevUserRequest $record) => $record->normalizedMobile)
                            ->copyMessage(__('filament::components/copyable.messages.copied'))
                            ->copyMessageDuration(1500)
                            ->inlineLabel(),
                        TextEntry::make('email')->label(__('Email'))
                            ->inlineLabel(),
                        TextEntry::make('full_address')->label(__('Full Address'))
                            ->inlineLabel()
                            ->columnSpanFull(),
                        TextEntry::make('other_address')
                            ->label(fn() => __('City') . ', ' . __('Street') . ', ' . __('Number'))
                            ->state(fn(PrevUserRequest $record): string => ($record->city ?? '-') . ', ' . ($record->street ?? '-') . ', ' . ($record->number ?? '-'))
                            ->inlineLabel()
                            ->columnSpanFull(),
                        TextEntry::make('dog.full_name')->label(__('dog/model/general.labels.singular'))
                            ->tooltip(fn(PrevUserRequest $record) => __('Copy Sagir ') . $record->sagirID)
                            ->formatStateUsing(fn(PrevUserRequest $record) => $record->dog->full_name . ' / ' . $record->sagirID)
                            ->copyable()
                            ->copyableState(fn(PrevUserRequest $record) => $record->sagirID)
                            ->copyMessage(__('filament::components/copyable.messages.copied'))
                            ->copyMessageDuration(1500)
                            ->inlineLabel(),
                        TextEntry::make('dog_name')->label(__('Dog name'))
                            ->inlineLabel(),
                        TextEntry::make('owner_name')->label(__('Owner Name'))
                            ->inlineLabel(),
                        TextEntry::make('owner.name')->label(__('Associated Owner User'))
                            ->inlineLabel(),
                        TextEntry::make('owner.mobile_phone')->label(__('Owner Phone'))
                            ->copyable()
                            ->copyableState(fn(PrevUserRequest $record) => $record->owner?->mobile_phone)
                            ->copyMessage(__('filament::components/copyable.messages.copied'))
                            ->copyMessageDuration(1500)
                            ->inlineLabel(),
                    ])
                    ->columns(1),
                Section::make(__('Resolved User'))
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('resolved_prev_user_id')->label(__('ID'))
                            ->state(fn(PrevUserRequest $record): ?int => $record->resolvedPrevUser()?->id)
                            ->inlineLabel(),
                        TextEntry::make('resolved_prev_user_name')->label(__('Name'))
                            ->state(fn(PrevUserRequest $record): ?string => $record->resolvedPrevUser()?->full_name)
                            ->inlineLabel(),
                        TextEntry::make('resolved_prev_user_phone')->label(__('Phone'))
                            ->state(fn(PrevUserRequest $record): ?string => $record->resolvedPrevUser()?->mobile_phone ?? $record->resolvedPrevUser()?->phone)
                            ->copyable()
                            ->copyableState(fn(PrevUserRequest $record) => $record->resolvedPrevUser()?->mobile_phone ?? $record->resolvedPrevUser()?->phone)
                            ->copyMessage(__('filament::components/copyable.messages.copied'))
                            ->copyMessageDuration(1500)
                            ->inlineLabel(),
                        TextEntry::make('resolved_prev_user_record_type')->label(__('Record Type'))
                            ->state(fn(PrevUserRequest $record): ?string => $record->resolvedPrevUser()?->record_type)
                            ->inlineLabel(),
                        TextEntry::make('resolved_prev_user_email')->label(__('Email'))
                            ->state(fn(PrevUserRequest $record): ?string => $record->resolvedPrevUser()?->email)
                            ->copyable(fn(PrevUserRequest $record) => $record->resolvedPrevUser()?->email ? true : false)
                            ->copyableState(fn(PrevUserRequest $record) => $record->resolvedPrevUser()?->email)
                            ->copyMessage(__('filament::components/copyable.messages.copied'))
                            ->copyMessageDuration(1500)
                            ->inlineLabel(),
                        TextEntry::make('resolved_prev_user_address')->label(__('Full Address'))
                            ->state(fn(PrevUserRequest $record): ?string => $record->resolvedPrevUser()?->buildAddress())
                            ->inlineLabel()
                            ->columnSpanFull(),
                    ])
                    ->hidden(fn(PrevUserRequest $record) => $record->resolvedPrevUser() === null)
                    ->columns(1),
                Section::make(__('Dates'))
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('payment_date_time')->label(__('Payment Date'))->dateTime()
                            ->inlineLabel(),
                        TextEntry::make('record_date_time')->label(__('Recorded at'))->dateTime()
                            ->inlineLabel(),
                        TextEntry::make('created_at')->label(__('Created at'))
                            ->dateTime()
                            ->inlineLabel(),
                        TextEntry::make('updated_at')->label(__('Updated at'))
                            ->dateTime()
                            ->inlineLabel(),
                        TextEntry::make('deleted_at')->label(__('Deleted at'))
                            ->dateTime()
                            ->hidden(fn(PrevUserRequest $record) => $record->deleted_at === null)
                            ->inlineLabel(),
                        IconEntry::make('IsDone')->label(__('Is Done'))->boolean()
                            ->inlineLabel(),
                        TextEntry::make('doneBy.name')->label(__('Done By'))
                            ->inlineLabel(),
                        TextEntry::make('DoneDate')->label(__('Done Date'))->dateTime()->placeholder('-')
                            ->inlineLabel(),
                    ])
                    ->columns(1),
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
            'index' => ListPrevUserRequests::route('/'),
            'create' => CreatePrevUserRequest::route('/create'),
            'view' => ViewPrevUserRequest::route('/{record}'),
            'edit' => EditPrevUserRequest::route('/{record}/edit'),
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
