<?php

namespace App\Filament\Resources\PrevUsers;

use App\Filament\Resources\PrevDogs\PrevDogResource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Fieldset;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Actions\ExportAction;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\PrevUsers\RelationManagers\DogsRelationManager;
use App\Filament\Resources\PrevUsers\RelationManagers\ClubsRelationManager;
use App\Filament\Resources\PrevUsers\RelationManagers\OwnerFilesRelationManager;
use App\Filament\Resources\PrevUsers\RelationManagers\UserRequestsRelationManager;
use App\Filament\Resources\PrevUsers\RelationManagers\PaymentsRelationManager;
use App\Filament\Resources\PrevUsers\RelationManagers\UserActivitiesRelationManager;
use App\Filament\Resources\PrevUsers\RelationManagers\ManagedTasksRelationManager;
use App\Filament\Resources\PrevUsers\RelationManagers\RelatedTasksRelationManager;
use App\Filament\Resources\PrevUsers\Pages\ListPrevUsers;
use App\Filament\Resources\PrevUsers\Pages\CreatePrevUser;
use App\Filament\Resources\PrevUsers\Pages\EditPrevUser;
use App\Filament\Resources\PrevUsers\Widgets\UserStats;
use App\Filament\Exports\PrevUserExporter;
use App\Filament\Resources\PrevUsers\Pages;
use App\Models\PrevUser;
use App\Notifications\UserMessageNotification;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification as LaravelNotification;

// use App\Filament\Resources\PrevUsers\RelationManagers;

class PrevUserResource extends Resource
{
    protected static ?string $model = PrevUser::class;

    public static function getModelLabel(): string
    {
        return __('User');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Users');
    }

    public static function getNavigationGroup(): string
    {
        return __('Users Management');
    }

    public static function getNavigationLabel(): string
    {
        return __('Users');
    }

    protected static ?string $slug = 'prev-users';

    protected static string | \BackedEnum | null $navigationIcon = 'fas-user';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'first_name';

    public static function getGlobalSearchResultTitle(Model $record): Htmlable|string
    {
        return $record->name;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['first_name', 'last_name', 'first_name_en', 'last_name_en', 'mobile_phone', 'phone'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            ($record->normalised_phone ? '☎ ' . $record->normalised_phone . ' ' : '') . ($record->email ? '✉ ' . $record->email : ''),
        ];
    }

    //    public static function getNavigationBadge(): ?string
    //    {
    //        return (string) static::$model::count();
    //    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('prev_user_form_tabs')
                    ->tabs([
                        Tab::make(__('Identity'))
                            ->schema([
                                Section::make(__('Names'))
                                    ->schema([
                                        TextInput::make('first_name')
                                            ->label(__('First Name'))
                                            ->maxLength(255),
                                        TextInput::make('last_name')
                                            ->label(__('Last Name'))
                                            ->maxLength(255),
                                        TextInput::make('first_name_en')
                                            ->label(__('First Name EN'))
                                            ->maxLength(255),
                                        TextInput::make('last_name_en')
                                            ->label(__('Last Name EN'))
                                            ->maxLength(255),
                                        DatePicker::make('birth_date')
                                            ->label(__('Birth Date')),
                                        TextInput::make('role_id')
                                            ->label(__('Role'))
                                            ->numeric(),
                                    ])
                                    ->columns(3),
                                Section::make(__('Account'))
                                    ->schema([
                                        TextInput::make('email')
                                            ->label(__('Email'))
                                            ->email()
                                            ->maxLength(255),
                                        TextInput::make('owner_email')
                                            ->label(__('Owner Email'))
                                            ->email()
                                            ->maxLength(100),
                                        DateTimePicker::make('email_verified_at')
                                            ->label(__('Email Verified At')),
                                        TextInput::make('password')
                                            ->label(__('Password'))
                                            ->password()
                                            ->maxLength(255),
                                        TextInput::make('otp')
                                            ->label(__('OTP'))
                                            ->numeric(),
                                        TextInput::make('language_id')
                                            ->label(__('Language'))
                                            ->required()
                                            ->numeric(),
                                        TextInput::make('status')
                                            ->label(__('Status'))
                                            ->numeric()
                                            ->default(0),
                                        TextInput::make('record_type')
                                            ->label(__('Record Type'))
                                            ->maxLength(50),
                                        TextInput::make('is_superadmin')
                                            ->label(__('Is Superadmin'))
                                            ->required()
                                            ->numeric(),
                                    ])
                                    ->columns(3),
                            ]),
                        Tab::make(__('Contact'))
                            ->schema([
                                Section::make(__('Phones'))
                                    ->schema([
                                        TextInput::make('mobile_phone')
                                            ->label(__('Mobile Phone'))
                                            ->tel()
                                            ->maxLength(255),
                                        TextInput::make('phone')
                                            ->label(__('Phone'))
                                            ->tel()
                                            ->maxLength(255),
                                        TextInput::make('private_phone_1')
                                            ->label(__('Private Phone 1'))
                                            ->tel()
                                            ->maxLength(200),
                                        TextInput::make('private_phone_2')
                                            ->label(__('Private Phone 2'))
                                            ->tel()
                                            ->maxLength(200),
                                        TextInput::make('fax')
                                            ->label(__('Fax'))
                                            ->maxLength(255),
                                    ])
                                    ->columns(3),
                                Section::make(__('Address'))
                                    ->schema([
                                        TextInput::make('address_city')
                                            ->label(__('Address City'))
                                            ->maxLength(255),
                                        TextInput::make('address_city_en')
                                            ->label(__('Address City EN'))
                                            ->maxLength(255),
                                        TextInput::make('address_street')
                                            ->label(__('Address Street'))
                                            ->maxLength(255),
                                        TextInput::make('address_street_en')
                                            ->label(__('Address Street EN'))
                                            ->maxLength(250),
                                        TextInput::make('address_street_number')
                                            ->label(__('Address Street Number'))
                                            ->maxLength(255),
                                        TextInput::make('house_number')
                                            ->label(__('House Number'))
                                            ->maxLength(150),
                                        TextInput::make('address_zip')
                                            ->label(__('Address Zip'))
                                            ->maxLength(255),
                                        TextInput::make('country_id')
                                            ->label(__('Country'))
                                            ->numeric(),
                                        TextInput::make('country_code')
                                            ->label(__('Country Code'))
                                            ->maxLength(255),
                                        TextInput::make('city_id')
                                            ->label(__('City'))
                                            ->numeric(),
                                    ])
                                    ->columns(3),
                            ]),
                        Tab::make(__('Membership & ownership'))
                            ->schema([
                                Section::make(__('Legacy identifiers'))
                                    ->schema([
                                        TextInput::make('data_id')
                                            ->label(__('Data ID'))
                                            ->numeric(),
                                        TextInput::make('owner_code')
                                            ->label(__('Owner Code'))
                                            ->numeric(),
                                        TextInput::make('info_id')
                                            ->label(__('Info ID'))
                                            ->numeric(),
                                        TextInput::make('sagir_owner_id')
                                            ->label(__('Sagir Owner ID'))
                                            ->numeric(),
                                        TextInput::make('order_id')
                                            ->label(__('Order ID'))
                                            ->numeric(),
                                        TextInput::make('new_sid')
                                            ->label(__('New SID'))
                                            ->maxLength(200),
                                        TextInput::make('new_org_data_id')
                                            ->label(__('New Org Data ID'))
                                            ->numeric(),
                                        DatePicker::make('new_fill_date')
                                            ->label(__('New Fill Date')),
                                        TextInput::make('new_filler_ip')
                                            ->label(__('New Filler IP'))
                                            ->maxLength(200),
                                    ])
                                    ->columns(3),
                                Section::make(__('Club & membership'))
                                    ->schema([
                                        TextInput::make('club_id')
                                            ->label(__('Club'))
                                            ->numeric(),
                                        TextInput::make('member_status')
                                            ->label(__('Member Status'))
                                            ->numeric(),
                                        DatePicker::make('start_date')
                                            ->label(__('Start Date')),
                                        DatePicker::make('expire_date')
                                            ->label(__('Expire Date')),
                                        TextInput::make('payment_status')
                                            ->label(__('Payment Status'))
                                            ->maxLength(200),
                                        TextInput::make('owner_payment_sum')
                                            ->label(__('Owner Payment Sum'))
                                            ->maxLength(200),
                                        TextInput::make('owner_payment_last4')
                                            ->label(__('Owner Payment Last4'))
                                            ->maxLength(200),
                                        TextInput::make('owner_total_payment')
                                            ->label(__('Owner Total Payment'))
                                            ->numeric(),
                                        TextInput::make('invoice_id')
                                            ->label(__('Invoice ID'))
                                            ->maxLength(200),
                                        TextInput::make('record_source')
                                            ->label(__('Record Source'))
                                            ->numeric(),
                                    ])
                                    ->columns(3),
                                Section::make(__('Breeding & approvals'))
                                    ->schema([
                                        TextInput::make('breed_id')
                                            ->label(__('Breed'))
                                            ->numeric(),
                                        TextInput::make('beit_gidul_id')
                                            ->label(__('Beit Gidul ID'))
                                            ->numeric(),
                                        TextInput::make('ClubManagerID')
                                            ->label(__('Club Manager ID'))
                                            ->numeric(),
                                        TextInput::make('is_current_owner')
                                            ->label(__('Is Current Owner'))
                                            ->numeric(),
                                        TextInput::make('is_breed_manager')
                                            ->label(__('Is Breed Manager'))
                                            ->maxLength(200),
                                        TextInput::make('is_judge')
                                            ->label(__('Is Judge'))
                                            ->maxLength(200),
                                        TextInput::make('approved_terms')
                                            ->label(__('Approved Terms'))
                                            ->maxLength(255),
                                        DatePicker::make('approved_date')
                                            ->label(__('Approved Date')),
                                        TextInput::make('breeding_otp')
                                            ->label(__('Breeding OTP'))
                                            ->numeric(),
                                    ])
                                    ->columns(3),
                            ]),
                        Tab::make(__('Media & notes'))
                            ->schema([
                                Section::make(__('Media'))
                                    ->schema([
                                        TextInput::make('profile_photo')
                                            ->label(__('Profile Photo'))
                                            ->maxLength(255),
                                        FileUpload::make('image')
                                            ->label(__('Image'))
                                            ->image(),
                                        DateTimePicker::make('last_active_date_time')
                                            ->label(__('Last Active Date Time')),
                                        Toggle::make('logout')
                                            ->label(__('Logout'))
                                            ->required(),
                                    ])
                                    ->columns(2),
                                Section::make(__('Documents & security'))
                                    ->schema([
                                        TextInput::make('social_id_number')
                                            ->label(__('Social ID Number'))
                                            ->maxLength(255),
                                        TextInput::make('passport_id')
                                            ->label(__('Passport ID'))
                                            ->maxLength(255),
                                        TextInput::make('migration_status')
                                            ->label(__('Migration Status')),
                                        TextInput::make('special_key')
                                            ->label(__('Special Key'))
                                            ->maxLength(4000),
                                        TextInput::make('user_key')
                                            ->label(__('User Key'))
                                            ->maxLength(4000),
                                    ])
                                    ->columns(2),
                                Section::make(__('Notes'))
                                    ->schema([
                                        Textarea::make('note')
                                            ->label(__('Note'))
                                            ->columnSpanFull(),
                                        Textarea::make('created_from')
                                            ->label(__('Created From'))
                                            ->columnSpanFull(),
                                        Textarea::make('grower_remarks')
                                            ->label(__('Grower Remarks'))
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(1),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function ($query) {
                $query->with([
                    'legacyDog:id,SagirID,Heb_Name,Eng_Name',
                ])
                    ->withCount(['dogs']);
            })
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label(__('ID'))
                    ->sortable()
                    ->searchable(isIndividual: true, isGlobal: false),
                TextColumn::make('record_type')
                    ->label(__('User Type'))
                    ->badge()
//                    ->color(fn(PrevUser $record): string => match ($record->record_type) {
//                        'Native' => 'success',
//                        'Owners' => 'warning',
//                        'Members' => 'blue',
//                        default => 'gray',
//                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('owner_code')
                    ->label(__('Owner Code'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('name')
                    ->label(__('Name'))
                    ->sortable(['last_name', 'first_name'])
                    ->searchable(['first_name', 'last_name', 'first_name_en', 'last_name_en'], isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('full_name')
                    ->label(__('Full Name'))
                    ->sortable(['last_name', 'first_name'])
                    ->searchable(['first_name', 'last_name', 'first_name_en', 'last_name_en'], isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('first_name')
                    ->label(__('First Name'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('last_name')
                    ->label(__('Last Name'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('first_name_en')
                    ->label(__('First Name EN'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('last_name_en')
                    ->label(__('Last Name EN'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('normalized_phone')
                    ->label(__('Phone'))
                    ->sortable(['mobile_phone', 'phone'])
                    ->searchable(['mobile_phone', 'phone'], isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('email')
                    ->label(__('Email'))
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('email_verified_at')
                    ->label(__('Email Verified At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('otp')
                    ->label(__('OTP'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('role_id')
                    ->label(__('Role'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('birth_date')
                    ->label(__('Birth Date'))
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('full_address')
                    ->label(__('Full Address'))
                    ->sortable(['address_city', 'address_street'])
                    ->searchable(['address_city', 'address_street'], isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('address_city')
                    ->label(__('Address City'))
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('address_city_en')
                    ->label(__('Address City EN'))
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('address_street')
                    ->label(__('Address Street'))
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('address_street_en')
                    ->label(__('Address Street EN'))
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('address_street_number')
                    ->label(__('Address Street Number'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('house_number')
                    ->label(__('House Number'))
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('address_zip')
                    ->label(__('Address Zip'))
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('country_id')
                    ->label(__('Country'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('country_code')
                    ->label(__('Country Code'))
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('fax')
                    ->label(__('Fax'))
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('social_id_number')
                    ->label(__('Social ID Number'))
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('passport_id')
                    ->label(__('Passport ID'))
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('profile_photo')
                    ->label(__('Profile Photo'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('last_active_date_time')
                    ->label(__('Last Active Date Time'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('language_id')
                    ->label(__('Language'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('is_superadmin')
                    ->label(__('Is Superadmin'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status')
                    ->label(__('Status'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('migration_status')
                    ->label(__('Migration Status'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('data_id')
                    ->label(__('Data ID'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('info_id')
                    ->label(__('Info ID'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('owner_email')
                    ->label(__('Owner Email'))
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('legacyDog.full_name')
                    ->label(__('Legacy Owned Dog'))
                    ->description(fn(PrevUser $record): string|null => $record->sagir_owner_id ?? null)
                    ->url(fn(PrevUser $record): string => PrevDogResource::getUrl('view', ['record', $record->sagir_owner_id]))
                    ->openUrlInNewTab()
                    ->searchable(['SagirID', 'Heb_Name', 'Eng_Name'], isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('sagir_owner_id')
                    ->label(__('Sagir Owner ID'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('is_current_owner')
                    ->label(__('Is Current Owner'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('order_id')
                    ->label(__('Order ID'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('new_sid')
                    ->label(__('New SID'))
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('private_phone_1')
                    ->label(__('Private Phone 1'))
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('private_phone_2')
                    ->label(__('Private Phone 2'))
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('breed_id')
                    ->label(__('Breed'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('user_key')
                    ->label(__('User Key'))
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('is_breed_manager')
                    ->label(__('Is Breed Manager'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('beit_gidul_id')
                    ->label(__('Beit Gidul ID'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('approved_terms')
                    ->label(__('Approved Terms'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('approved_date')
                    ->label(__('Approved Date'))
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('ClubManagerID')
                    ->label(__('Club Manager ID'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('Updated At'))
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
                Filter::make('trashed')
                    ->schema([
                        ToggleButtons::make('trashed')
                            ->label(__('Deleted Status'))
                            ->options([
                                'not_deleted' => __('Not Deleted'),
                                'deleted' => __('Deleted'),
                                'all' => __('All'),
                            ])
                            ->colors([
                                'not_deleted' => 'success',
                                'deleted' => 'danger',
                                'all' => 'gray',
                            ])
                            ->default('not_deleted')
                            ->grouped(),
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
                Filter::make('record_type')
                    ->schema([
                        ToggleButtons::make('record_type')
                            ->label(__('User Type'))
                            ->options([
                                'all' => __('All'),
                                'Native' => 'Native',
                                'Owners' => __('Owners'),
                                'Members' => __('Members'),
                                'without' => __('-without-'),
                            ])
                            ->colors([
                                'all' => 'gray',
                                'Native' => 'success',
                                'Owners' => 'warning',
                                'Members' => 'danger',
                                'without' => 'gray',
                            ])
                            ->default('all')
                            ->grouped(),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query->whereRecordType($data['record_type'] ?? null);
                    })
                    ->columnSpan(2),
                Filter::make('created_at')
                    ->schema([
                        Fieldset::make()
                            ->label(__('Created'))
                            ->schema([
                                DatePicker::make('created_at_from')
                                    ->hiddenLabel()
                                    ->prefix(__('From'))
                                    ->native(false)
                                    ->format('d/m/Y')
                                    ->displayFormat('d/m/Y')
                                    ->locale('he')
                                    ->weekStartsOnSunday()
                                    ->closeOnDateSelection(),
                                DatePicker::make('created_at_to')
                                    ->hiddenLabel()
                                    ->prefix(__('To'))
                                    ->native(false)
                                    ->format('d/m/Y')
                                    ->displayFormat('d/m/Y')
                                    ->locale('he')
                                    ->weekStartsOnSunday()
                                    ->closeOnDateSelection(),
                            ])
                            ->columns(2),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (! empty($data['created_at_from'])) {
                            $query->where('created_at', '>=', $data['created_at_from']);
                        }
                        if (! empty($data['created_at_to'])) {
                            $query->where('created_at', '<=', $data['created_at_to']);
                        }

                        return $query;
                    })
                    ->columnSpan(2),
                Filter::make('updated_at')
                    ->schema([
                        Fieldset::make()
                            ->label(__('Updated'))
                            ->schema([
                                DatePicker::make('updated_at_from')
                                    ->hiddenLabel()
                                    ->prefix(__('From'))
                                    ->native(false)
                                    ->format('d/m/Y')
                                    ->displayFormat('d/m/Y')
                                    ->locale('he')
                                    ->weekStartsOnSunday()
                                    ->closeOnDateSelection(),
                                DatePicker::make('updated_at_to')
                                    ->hiddenLabel()
                                    ->prefix(__('To'))
                                    ->native(false)
                                    ->format('d/m/Y')
                                    ->displayFormat('d/m/Y')
                                    ->locale('he')
                                    ->weekStartsOnSunday()
                                    ->closeOnDateSelection(),
                        ])
                            ->columns(2),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (! empty($data['updated_at_from'])) {
                            $query->where('updated_at', '>=', $data['updated_at_from']);
                        }
                        if (! empty($data['updated_at_to'])) {
                            $query->where('updated_at', '<=', $data['updated_at_to']);
                        }

                        return $query;
                    })
                    ->columnSpan(2),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(7)
            ->recordActions([
                EditAction::make()
                    ->iconButton()
                    ->tooltip(__('Edit')),
                Action::make('send_email')
                    ->label(false)
                    ->icon('heroicon-o-envelope')
                    ->iconButton()
                    ->tooltip(__('Send an email message'))
                    ->color('primary')
                    ->modalHeading(__('Send Email'))
                    ->modalSubmitActionLabel(__('Queue Email'))
                    ->modalIcon('heroicon-o-envelope')
                    ->schema([
                        TextInput::make('subject')
                            ->label(__('Subject'))
                            ->required()
                            ->maxLength(150),
                        RichEditor::make('body')
                            ->label(__('Message'))
                            ->toolbarButtons([
                                'attachFiles',
                                'blockquote',
                                'bold',
                                'bulletList',
                                'codeBlock',
                                'h1',
                                'h2',
                                'h3',
                                'italic',
                                'link',
                                'orderedList',
                                'redo',
                                'strike',
                                'underline',
                                'undo',
                            ])
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('editor-attachments')
                            ->fileAttachmentsVisibility('public')
                            ->columnSpanFull()
                            ->required(),
                    ])
                    ->action(function (PrevUser $record, array $data): void {
                        // Queue mail notification
                        LaravelNotification::send($record, new UserMessageNotification(
                            subject: (string)$data['subject'],
                            body: (string)$data['body'],
                            channels: ['mail'],
                        ));

                        Notification::make()
                            ->title(__('Email queued'))
                            ->body(__('Email queued to :email', ['email' => $record->email ?: __('(no email)')]))
                            ->success()
                            ->send();
                    }),
            ], position: RecordActionsPosition::BeforeColumns)
            ->headerActions([
                ExportAction::make()
                    ->label(__('Export All'))
                    ->icon('fas-file-export')
                    ->color('primary')
                    ->iconPosition('after')
                    ->exporter(PrevUserExporter::class),
            ])
            ->toolbarActions([
                ExportBulkAction::make()
                    ->label(__('Export Selected'))
                    ->icon('fas-file-export')
                    ->color('primary')
                    ->iconPosition('after')
                    ->exporter(PrevUserExporter::class),
                BulkActionGroup::make([
                    // Bulk send (queued) to many PrevUsers
                    BulkAction::make('bulk_send_email')
                        ->label(__('Send Email'))
                        ->icon('heroicon-o-envelope')
                        ->color('primary')
                        ->requiresConfirmation()
                        ->modalHeading(__('Send Email'))
                        ->modalSubmitActionLabel(__('Queue Emails'))
                        ->form([
                            TextInput::make('subject')
                                ->label(__('Subject'))
                                ->required()
                                ->maxLength(150),
                            RichEditor::make('body')
                                ->label(__('Message'))
                                ->toolbarButtons([
                                    'attachFiles',
                                    'blockquote',
                                    'bold',
                                    'bulletList',
                                    'codeBlock',
                                    'h1',
                                    'h2',
                                    'h3',
                                    'italic',
                                    'link',
                                    'orderedList',
                                    'redo',
                                    'strike',
                                    'underline',
                                    'undo',
                                ])
                                ->fileAttachmentsDisk('public')
                                ->fileAttachmentsDirectory('editor-attachments')
                                ->fileAttachmentsVisibility('public')
                                ->columnSpanFull()
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $notification = new UserMessageNotification(
                                subject: (string)$data['subject'],
                                body: (string)$data['body'],
                                channels: ['mail'],
                            );

                            // Queue notifications to the selected owners
                            LaravelNotification::send($records, $notification);

                            Notification::make()
                                ->title(__('Emails queued'))
                                ->body(__('Emails queued to :count owners', ['count' => $records->count()]))
                                ->success()
                                ->send();
                        }),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->paginated([10, 25, 50, 100, 200, 250, 300])
            ->defaultPaginationPageOption(25)
            ->searchOnBlur()
            ->striped()
            ->deferLoading()
            ->recordUrl(false);
    }

    public static function getRelations(): array
    {
        return [
            DogsRelationManager::class,
            ClubsRelationManager::class,
            OwnerFilesRelationManager::class,
            UserRequestsRelationManager::class,
            PaymentsRelationManager::class,
            UserActivitiesRelationManager::class,
            ManagedTasksRelationManager::class,
            RelatedTasksRelationManager::class,
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
            'index' => ListPrevUsers::route('/'),
            'create' => CreatePrevUser::route('/create'),
            'edit' => EditPrevUser::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            UserStats::class,
        ];
    }
}
