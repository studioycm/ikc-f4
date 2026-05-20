<?php

namespace App\Filament\Pages;

use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use JsonException;
use JsonSerializable;
use Spatie\Activitylog\Models\Activity as ActivityLogModel;
use Stringable;
use Traversable;
use UnitEnum;

class ActivityLog extends Page implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithTable;

    private const EMPTY_VALUE_PLACEHOLDER = '—';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected string $view = 'filament.pages.activity-log';

    public static function getNavigationLabel(): string
    {
        return __('Activity Log');
    }

    public static function getNavigationGroup(): string
    {
        return __('Reports Management');
    }

    public function getTitle(): string
    {
        return __('Activity Log');
    }

    protected static ?string $slug = 'activities';

    public function table(Table $table): Table
    {
        return $table
            ->query(ActivityLogModel::query()->with(['causer', 'subject'])->latest())
            ->columns([
                TextColumn::make('created_at')
                    ->label('Time')
                    ->dateTime('M j, H:i')
                    ->sortable(),
                TextColumn::make('event')
                    ->label(__('Event'))
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        'viewed' => 'info',    // Added for page visits
                        'searched' => 'gray',   // Added for filtered requests
                        default => 'gray',
                    })
                    ->placeholder('N/A'),

                TextColumn::make('subject_name')
                    ->label('Subject / Page')
                    ->getStateUsing(function (ActivityLogModel $record): string|HtmlString {
                        // 1. If it's a real Model (User, Order), show the class name
                        if ($record->subject_type) {
                            $class = class_basename($record->subject_type);
                            $name = $record->subject?->name ?? "#{$record->subject_id}";

                            return new HtmlString("<b>{$class}</b>: {$name}");
                        }

                        // 2. If it's a Page Visit (subject is null), read our custom property
                        $page = $record->properties['page'] ?? null;
                        if ($page) {
                            return new HtmlString("🖥️ <b>Page:</b> {$page}");
                        }

                        return 'System';
                    })
                    ->placeholder('System'),

                TextColumn::make('causer_name')
                    ->label('Causer')
                    ->getStateUsing(function (ActivityLogModel $record): ?string {
                        if (! $record->causer) {
                            return 'System';
                        }

                        // For User models, use the name accessor
                        if ($record->causer instanceof User) {
                            return $record->causer->name;
                        }

                        // Fallback for other models
                        return $record->causer->name ?? "#{$record->causer_id}";
                    })
                    ->placeholder('System')
                    ->sortable(),
                TextColumn::make('description')
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();

                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }

                        return $state;
                    }),
            ])
            ->filters([
                SelectFilter::make('subject_type')
                    ->label('Subject Type')
                    ->options(fn (): array => ActivityLogModel::query()
                        ->whereNotNull('subject_type')
                        ->select('subject_type')
                        ->distinct()
                        ->pluck('subject_type')
                        ->filter()
                        ->mapWithKeys(fn (string $type): array => match ($type) {
                            'filament_page' => [$type => 'Admin Panel View Log'],
                            default => [$type => class_basename($type)]
                        })
                        ->toArray()
                    )
                    ->searchable()
                    ->preload(),

                SelectFilter::make('causer_id')
                    ->label('Causer User')
                    ->options(fn (): array => User::query()
                        ->whereIn('id', ActivityLogModel::query()
                            ->where('causer_type', User::class)
                            ->whereNotNull('causer_id')
                            ->select('causer_id')
                            ->distinct()
                        )
                        ->orderBy('name')
                        ->get(['id', 'name'])
                        ->pluck('name', 'id')
                        ->toArray()
                    )
                    ->searchable()
                    ->preload()
                    ->query(function ($query, $state): void {
                        if (filled($state['value'])) {
                            $query
                                ->where('causer_type', User::class)
                                ->where('causer_id', $state['value']);
                        }
                    }),
            ])
            ->recordActions([
                Action::make('viewProperties')
                    ->label(__('Details'))
                    ->icon(Heroicon::OutlinedEye)
                    ->modalHeading(__('Activity Details'))
                    ->modalWidth('5xl')
                    ->schema(function (ActivityLogModel $record): array {
                        $activityDetails = self::prepareActivityDetails($record);

                        return [
                            Grid::make(['default' => 1])
                                ->schema([
                                    // HEADER: Who, When, Subject
                                    Section::make()
                                        ->schema([
                                            TextEntry::make('causer.name')
                                                ->label('User')
                                                ->icon(Heroicon::User)
                                                ->weight(FontWeight::Bold),

                                            TextEntry::make('created_at')
                                                ->label('Date')
                                                ->icon(Heroicon::Calendar)
                                                ->dateTime('d M Y H:i:s'),

                                            TextEntry::make('event')
                                                ->badge()
                                                ->color(fn (?string $state): string => match ($state) {
                                                    'created' => 'success',
                                                    'updated' => 'warning',
                                                    'deleted' => 'danger',
                                                    'viewed' => 'info',
                                                    'searched' => 'gray',
                                                    default => 'gray',
                                                }),

                                            TextEntry::make('subject_type')
                                                ->label(__('Type'))
                                                ->badge()
                                                ->formatStateUsing(function (?string $state): string {
                                                    if (! $state) {
                                                        return 'Admin Page';
                                                    }

                                                    return Str::title(Str::replace('Prev', '', class_basename($state)));
                                                }),

                                            TextEntry::make('subject.name')
                                                ->label(__('Subject'))
                                                ->getStateUsing(function () use ($record) {
                                                    return $record->subject?->name
                                                        ?? $record->properties['page']
                                                        ?? $record->properties['virtual_subject_name']
                                                        ?? 'System';
                                                }),
                                        ])
                                        ->columns(5),

                                    // BODY TABS: Separates Data differences from advanced Request properties
                                    Tabs::make('Details')
                                        ->tabs([
                                            // TAB 1: Database Changes
                                            Tab::make('Model Changes')
                                                ->icon(Heroicon::OutlinedDocumentDuplicate)
                                                ->visible(fn (): bool => $activityDetails['comparisonRows'] !== [])
                                                ->schema([
                                                    RepeatableEntry::make('comparison_table')
                                                        ->hiddenLabel()
                                                        ->state($activityDetails['comparisonRows'])
                                                        ->table([
                                                            TableColumn::make('Attribute')->width('200px'),
                                                            TableColumn::make('Previous Value'),
                                                            TableColumn::make('New Value'),
                                                            TableColumn::make('Changed'),
                                                        ])
                                                        ->schema([
                                                            TextEntry::make('attribute')->weight(FontWeight::Bold),

                                                            TextEntry::make('old_value')
                                                                ->html()
                                                                ->formatStateUsing(fn (mixed $state): HtmlString => self::renderActivityValue($state)),

                                                            TextEntry::make('new_value')
                                                                ->html()
                                                                ->formatStateUsing(fn (mixed $state): HtmlString => self::renderActivityValue($state)),

                                                            IconEntry::make('changed')->boolean(),
                                                        ]),
                                                ]),

                                            // TAB 2: URL Query parameters
                                            Tab::make('URL Query & Filters')
                                                ->icon(Heroicon::OutlinedMagnifyingGlass)
                                                ->visible(fn (): bool => $activityDetails['queryData'] !== [])
                                                ->schema([
                                                    TextEntry::make('query_json')
                                                        ->hiddenLabel()
                                                        ->html()
                                                        ->getStateUsing(fn (): string => self::formatActivityValue($activityDetails['queryData']))
                                                        ->formatStateUsing(fn (mixed $state): HtmlString => self::renderActivityValue($state)),
                                                ]),

                                            // TAB 3: Post Form Submissions
                                            Tab::make('Request Payload')
                                                ->icon(Heroicon::OutlinedArrowUpTray)
                                                ->visible(fn (): bool => $activityDetails['payloadData'] !== [])
                                                ->schema([
                                                    TextEntry::make('payload_json')
                                                        ->hiddenLabel()
                                                        ->html()
                                                        ->getStateUsing(fn (): string => self::formatActivityValue($activityDetails['payloadData']))
                                                        ->formatStateUsing(fn (mixed $state): HtmlString => self::renderActivityValue($state)),
                                                ]),

                                            // TAB 4: General Request Metadata
                                            Tab::make('Network Metadata')
                                                ->icon(Heroicon::OutlinedComputerDesktop)
                                                ->schema([
                                                    KeyValueEntry::make('properties')
                                                        ->hiddenLabel()
                                                        ->state($activityDetails['baseProperties']),
                                                ]),
                                        ]),
                                ]),
                        ];
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelAction(fn ($action) => $action->label('Close')->color('primary')),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([100]);
    }

    /**
     * @return array{
     *     comparisonRows: list<array{attribute: string, old_value: string, new_value: string, changed: bool}>,
     *     baseProperties: array<string, string>,
     *     queryData: array<string, mixed>,
     *     payloadData: array<string, mixed>
     * }
     */
    protected static function prepareActivityDetails(ActivityLogModel $record): array
    {
        $changes = self::arrayFromLogValue($record->attribute_changes);
        $properties = self::arrayFromLogValue($record->properties);

        $oldValues = self::formatActivityValues($changes['old'] ?? []);
        $newValues = self::formatActivityValues($changes['attributes'] ?? []);

        return [
            'comparisonRows' => self::makeAttributeComparisonRows($oldValues, $newValues),
            'baseProperties' => self::formatActivityValues(Arr::except($properties, [
                'query',
                'payload',
                'virtual_subject_name',
                'page',
            ])),
            'queryData' => self::arrayFromLogValue($properties['query'] ?? []),
            'payloadData' => self::arrayFromLogValue($properties['payload'] ?? []),
        ];
    }

    /**
     * @param  array<string|int, string>  $oldValues
     * @param  array<string|int, string>  $newValues
     * @return list<array{attribute: string, old_value: string, new_value: string, changed: bool}>
     */
    protected static function makeAttributeComparisonRows(array $oldValues, array $newValues): array
    {
        $keys = array_unique(array_merge(array_keys($oldValues), array_keys($newValues)));

        return collect($keys)
            ->map(fn (string|int $key): array => [
                'attribute' => (string) $key,
                'old_value' => $oldValues[$key] ?? self::EMPTY_VALUE_PLACEHOLDER,
                'new_value' => $newValues[$key] ?? self::EMPTY_VALUE_PLACEHOLDER,
                'changed' => ($oldValues[$key] ?? self::EMPTY_VALUE_PLACEHOLDER) !== ($newValues[$key] ?? self::EMPTY_VALUE_PLACEHOLDER),
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<string|int, string>
     */
    protected static function formatActivityValues(mixed $values): array
    {
        return collect(self::arrayFromLogValue($values))
            ->map(fn (mixed $value): string => self::formatActivityValue($value))
            ->all();
    }

    protected static function formatActivityValue(mixed $value): string
    {
        if ($value === null || $value === '') {
            return self::EMPTY_VALUE_PLACEHOLDER;
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if ($value instanceof BackedEnum) {
            return (string) $value->value;
        }

        if ($value instanceof UnitEnum) {
            return $value->name;
        }

        if (is_scalar($value) || $value instanceof Stringable) {
            return (string) $value;
        }

        if ($value instanceof Arrayable) {
            return self::formatActivityValue($value->toArray());
        }

        if ($value instanceof JsonSerializable) {
            return self::formatActivityValue($value->jsonSerialize());
        }

        if ($value instanceof Traversable) {
            return self::formatActivityValue(iterator_to_array($value));
        }

        if (is_object($value)) {
            $value = get_object_vars($value);
        }

        if (is_array($value)) {
            if ($value === []) {
                return self::EMPTY_VALUE_PLACEHOLDER;
            }

            try {
                return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
            } catch (JsonException) {
                return self::EMPTY_VALUE_PLACEHOLDER;
            }
        }

        return self::EMPTY_VALUE_PLACEHOLDER;
    }

    protected static function renderActivityValue(mixed $state): HtmlString
    {
        $value = is_string($state) ? $state : self::formatActivityValue($state);

        if ($value === '' || $value === self::EMPTY_VALUE_PLACEHOLDER) {
            return new HtmlString('<span class="text-gray-400 font-medium">'.self::EMPTY_VALUE_PLACEHOLDER.'</span>');
        }

        $escapedValue = e($value);

        if (self::isJsonValue($value)) {
            return new HtmlString("<pre class='max-w-full overflow-x-auto rounded border border-gray-200 bg-gray-50 p-2 text-left text-xs whitespace-pre-wrap text-gray-800 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-200'><code>{$escapedValue}</code></pre>");
        }

        return new HtmlString("<span class='text-sm font-medium text-gray-900 dark:text-gray-100'>{$escapedValue}</span>");
    }

    protected static function isJsonValue(string $value): bool
    {
        $value = trim($value);

        return $value !== ''
            && Str::startsWith($value, ['{', '['])
            && json_validate($value);
    }

    /**
     * @return array<string|int, mixed>
     */
    protected static function arrayFromLogValue(mixed $value): array
    {
        if ($value instanceof Arrayable) {
            return $value->toArray();
        }

        if ($value instanceof JsonSerializable) {
            $value = $value->jsonSerialize();
        }

        if ($value instanceof Traversable) {
            return iterator_to_array($value);
        }

        if (is_object($value)) {
            return get_object_vars($value);
        }

        return is_array($value) ? $value : [];
    }
}
