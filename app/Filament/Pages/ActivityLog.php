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
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Arr;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Activity as ActivityLogModel;

class ActivityLog extends Page implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithTable;

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
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        default => 'gray',
                    })
                    ->placeholder('N/A'),
                TextColumn::make('subject_name')
                    ->label('Subject')
                    ->getStateUsing(function (ActivityLogModel $record): ?string {
                        if (! $record->subject_type) {
                            return null;
                        }

                        $subjectType = class_basename($record->subject_type);

                        if (! $record->subject) {
                            return "{$subjectType} ID {$record->subject_id}";
                        }

                        // Resolve the field name from protected/public $activitySubjectName
                        // and show its attribute
                        if (property_exists($record->subject, 'activitySubjectName')) {
                            $fieldName = (function () {
                                return $this->activitySubjectName;
                            })->call($record->subject);

                            $value = $record->subject->getAttribute($fieldName);
                            if (filled($value)) {
                                return "{$subjectType}: {$value}";
                            }
                        }

                        // Fallback to checking for 'name' property
                        if (isset($record->subject->name)) {
                            return "{$subjectType}: {$record->subject->name}";
                        }

                        // Final fallback
                        return "{$subjectType} ID {$record->subject_id}";
                    })
                    ->placeholder('N/A'),
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
                        ->mapWithKeys(fn (string $type): array => [$type => class_basename($type)])
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
                    ->icon('heroicon-o-eye')
                    ->modalHeading(__('Activity Details'))
                    ->modalWidth('5xl')
                    ->schema(function (ActivityLogModel $record): array {

                        // HELPER: recursively convert nested arrays to strings
                        $formatValue = function ($value) {
                            if (is_array($value) || is_object($value)) {
                                return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                            }
                            return (string) $value;
                        };

                        // 1. Get Raw Data (Spatie v5)
                        $changes = $record->attribute_changes ?? [];

                        // 2. Process "Old" and "New" to ensure they are flat text arrays
                        $old = collect($changes['old'] ?? [])
                            ->map($formatValue)
                            ->toArray();

                        $new = collect($changes['attributes'] ?? [])
                            ->map($formatValue)
                            ->toArray();

                        return [
                            Grid::make([
                                'default' => 1,
                            ])
                                ->schema([
                                    // HEADER: Who and When
                                    Section::make()
                                        ->schema([
                                            TextEntry::make('causer.name')
                                                ->label('User')
                                                ->icon('heroicon-m-user')
                                                ->weight(FontWeight::Bold),

                                            TextEntry::make('created_at')
                                                ->label('Date')
                                                ->icon('heroicon-m-calendar')
                                                ->dateTime('d M Y H:i:s'),

                                            TextEntry::make('event')
                                                ->badge()
                                                ->color(fn (string $state): string => match ($state) {
                                                    'created' => 'success',
                                                    'updated' => 'warning',
                                                    'deleted' => 'danger',
                                                    default => 'gray',
                                                }),

                                            TextEntry::make('')
                                        ])
                                        ->columns(3),

                                    Section::make('Data Comparison')
                                        ->schema([
                                            RepeatableEntry::make('comparison_table')
                                                ->hiddenLabel()
                                                ->state(function () use ($old, $new): array {
                                                    // Combine keys from both old and new
                                                    $keys = array_unique(array_merge(array_keys($old), array_keys($new)));

                                                    return collect($keys)->map(fn ($key) => [
                                                        'attribute' => $key,
                                                        'old_value' => $old[$key],
                                                        'new_value' => $new[$key],
                                                        'changed' => $old[$key] != $new[$key],
                                                    ])->toArray();
                                                })
                                                // Use the v4 table() method for the header labels
                                                ->table([
                                                    TableColumn::make('Attribute')
                                                        ->width('200px'),
                                                    TableColumn::make('Previous Value'),
                                                    TableColumn::make('New Value'),
                                                    TableColumn::make('Changed'),
                                                ])
                                                // Match the keys in state to the schema entries
                                                ->schema([
                                                    TextEntry::make('attribute')
                                                        ->weight(FontWeight::Bold),

                                                    TextEntry::make('old_value'),

                                                    TextEntry::make('new_value'),

                                                    IconEntry::make('changed')
                                                        ->boolean(),
                                                ])
                                        ])
                                        ->collapsible()
                                        ->visible(fn () => !empty($new) || !empty($old)),

                                    // FOOTER: Metadata (IPs, etc)
                                    Section::make('Metadata')
                                        ->schema([
                                            KeyValueEntry::make('properties')
                                                ->label('')
                                                ->state(collect($record->properties)->map($formatValue)->toArray()),
                                        ])
                                        ->collapsible()
                                        ->collapsed(),
                                ]),
                        ];
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelAction(fn ($action) => $action->label('Close')->color('primary')),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([100]);
    }
}
