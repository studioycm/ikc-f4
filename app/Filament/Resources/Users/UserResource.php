<?php /** @noinspection PhpMultipleClassDeclarationsInspection */

namespace App\Filament\Resources\Users;

use BackedEnum;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Auth\Notifications\VerifyEmail;
use Filament\Forms\Components\RichEditor;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages;
use App\Models\PrevUser;
use App\Models\User;
use App\Notifications\UserMessageNotification;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification as LaravelNotification;
use Illuminate\Support\Str;
use Filament\Support\Icons\Heroicon;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with('prevUser');
    }

    public static function getModelLabel(): string
    {
        return __('System User');
    }

    public static function getPluralModelLabel(): string
    {
        return __('System Users');
    }

    public static function getNavigationGroup(): string
    {
        return __('Authorisation Management');
    }

    public static function getNavigationLabel(): string
    {
        return __('System Users');
    }

    protected static ?int $navigationSort = 98;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedUsers;

    //    public static function getNavigationBadge(): ?string
    //    {
    //        return static::getModel()::count();
    //    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('Name'))
                    ->required(),
                TextInput::make('email')
                    ->label(__('Email'))
                    ->email()
                    ->required(),
                Select::make('prev_user_id')
                    ->label(__('Legacy User'))
                    ->nullable()
                    ->placeholder(__('—'))
                    ->searchable()
//                    ->unique()
                    ->getSearchResultsUsing(fn(string $search): array => PrevUser::selectOptions($search, 50))
                    ->getOptionLabelUsing(function ($value) {
                        if (!$value) {
                            return null;
                        }

                        // Optimization: Select only columns needed for the 'search_label' accessor
                        return PrevUser::query()
                            ->select(['id', 'first_name', 'last_name', 'first_name_en', 'last_name_en', 'mobile_phone', 'phone', 'email'])
                            ->find($value)
                            ?->search_label;
                    }),
                DateTimePicker::make('email_verified_at')
                    ->label(__('Verified At'))
                    ->native(false)
                    ->displayFormat('d/m/Y H:i'),
                Forms\Components\Toggle::make('edit_password')
                    ->label(__('Edit Password'))
                    ->default(fn(string $operation, ?User $record): bool => $operation === 'create')
                    ->live(),
                TextInput::make('password')
                    ->label(__('Password'))
                    ->password()
                    ->hidden(fn(string $operation, Get $get, ?User $record): bool => $operation === 'edit' && (auth()->id() === $record?->id || $get('edit_password') === false))
                    ->revealable(),
                Select::make('roles')
                    ->label(__('Roles'))
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                return $query
                    ->with([
                        'prevUser' => function ($q) {
                            // This runs entirely on the Legacy DB, so it works perfectly.
                            // It adds a 'dogs_count' attribute to the prevUser model.
                            $q->withCount('dogs');
                        },
                    ]);
            })
            ->columns([
                TextColumn::make('id')
                    ->label(__('ID'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('name')
                    ->label(__('Name'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('email')
                    ->label(__('Email'))
                    ->icon(Heroicon::OutlinedEnvelope)
                    ->iconColor('warning')
                    ->sortable()
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Email address copied')
                    ->copyMessageDuration(1500),
                TextColumn::make('prevUser.name')
                    ->label(__('Legacy User'))
                    ->placeholder('-')
                    ->sortable(false)
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        // Step A: Search the Legacy DB first
                        // We reuse the 'searchName' scope you already have in PrevUser model!
                        $matchingLegacyIds = PrevUser::query()->searchName($search)
                            ->pluck('id')
                            ->toArray(); // Important: convert to array for whereIn

                        // Step B: Filter the System DB using those IDs
                        // If no matches found in legacy, passing empty array returns no results (correct)
                        return $query->whereIn('prev_user_id', $matchingLegacyIds);
                    })
                    ->description(function (User $record) {
                        $legacy = $record->prevUser;

                        if (!$legacy) {
                            return null;
                        }

                        // Combine Phone and Email, filtering out empty values
                        return collect([$legacy->normalised_phone, $legacy->email, "#{$legacy->id}"])
                            ->filter()
                            ->join(' • '); // Separator dot
                    })
                    ->sortable(false)
                    ->toggleable(),
                TextColumn::make('prevUser.dogs_count')
                    ->label(__('dog/model/general.labels.plural'))
                    ->badge()
                    ->color(fn($state) => $state > 0 ? 'success' : 'gray')
                    ->default(0)
                    ->toggleable(),
                IconColumn::make('email_verified_at')
                    ->label(__('Verified'))
                    ->boolean()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('roles.name')
                    ->label(__('Role'))
                    ->badge()
                    ->formatStateUsing(fn ($state): string => Str::headline($state))
                    ->color(fn (string $state): string => match ($state) {
                        'super_admin' => 'success',
                        'panel_user' => 'warning',
                        'admin' => 'danger',
                        default => 'info',
                    })
                    ->sortable()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label(__('Created'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('Updated'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->searchPlaceholder(__('Search (ID, Name)'))
            ->searchOnBlur()
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('email_verification')
                    ->label(__('Verify'))
                    ->button()
                    ->tooltip(__('Send email verification link'))
                    ->color(Color::generateV3Palette('#ec8200'))
                    ->icon(Heroicon::OutlinedShieldCheck)
                    ->action(function (User $user) {
                        $notification = app(VerifyEmail::class);
                        $notification->url = Filament::getVerifyEmailUrl($user);
                        $user->notify($notification);
                        Notification::make()
                            ->title(__('Email verification link sent'))
                            ->body(__('Email verification link sent to :email<br>:url', [
                                'email' => $user->email,
                                'url' => $notification->url,
                            ]))
                            ->success()
                            ->icon(Heroicon::OutlinedShieldCheck)
                            ->iconColor('primary')
                            ->persistent()
                            ->send();
                    }),
                Action::make('email_verified')
                    ->label(__('Verified'))
                    ->button()
                    ->tooltip(__('Mark as verified'))
                    ->color(Color::generateV3Palette('#10b138'))
                    ->icon(Heroicon::OutlinedCheck)
                    ->action(function (User $user) {
                        $user->markEmailAsVerified();
                    }),
                Action::make('send_db_notice')
                    ->label(__('Notify'))
                    ->button()
                    ->tooltip(__('Send a database notification to this user'))
                    ->color('gray')
                    ->icon(Heroicon::OutlinedBell)
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
                    ->action(function (User $record, array $data): void {
                        $record->notify(new UserMessageNotification(
                            subject: (string)$data['subject'],
                            body: (string)$data['body'],
                            channels: ['database'],
                        ));

                        Notification::make()
                            ->title(__('Notification created'))
                            ->body(__('A database notification was created for :email', ['email' => $record->email]))
                            ->success()
                            ->send();
                    }),
                Action::make('send_email')
                    ->label(__('Send Email'))
                    ->button()
                    ->tooltip(__('Send an email to this user'))
                    ->color('primary')
                    ->icon(Heroicon::OutlinedEnvelope)
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
                    ->action(function (User $record, array $data): void {
                        LaravelNotification::sendNow($record, new UserMessageNotification(
                            subject: (string)$data['subject'],
                            body: (string)$data['body'],
                            channels: ['mail'],
                        ));

                        Notification::make()
                            ->title(__('Email sent'))
                            ->body(__('Email sent to :email', ['email' => $record->email]))
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('bulk_send_email')
                        ->label(__('Send Email'))
                        ->icon(Heroicon::OutlinedEnvelope)
                        ->color('primary')
                        ->requiresConfirmation()
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
                        ->action(function (Collection $records, array $data): void {
                            $notification = new UserMessageNotification(
                                subject: (string)$data['subject'],
                                body: (string)$data['body'],
                                channels: ['mail'],
                            );

                            LaravelNotification::sendNow($records, $notification);

                            Notification::make()
                                ->title(__('Emails sent'))
                                ->body(__('Email sent to :count users', ['count' => $records->count()]))
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
