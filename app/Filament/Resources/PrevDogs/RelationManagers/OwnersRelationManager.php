<?php

namespace App\Filament\Resources\PrevDogs\RelationManagers;

use Filament\Tables\Columns\TextColumn;
use Filament\Actions\AttachAction;
use Filament\Forms\Components\Select;
use Filament\Actions\ViewAction;
use Filament\Schemas\Components\Grid;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\DetachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DetachBulkAction;
use App\Filament\Resources\PrevUsers\PrevUserResource;
use App\Models\PrevUser;
use Filament\Forms;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Filament\Support\Icons\Heroicon;

class OwnersRelationManager extends RelationManager
{
    protected static string $relationship = 'owners';

    protected static ?string $recordTitleAttribute = 'first_name';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Owners');
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->defaultSort('dogs2users.created_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label(__('Owner'))
                    ->description(fn(PrevUser $record) => $record->id)
                    ->searchable(),
                TextColumn::make('mobile_phone')
                    ->label(__('Phone'))
                    ->copyable()
                    ->copyMessage(fn($state) => __('Phone number') . " $state " . __('copied to clipboard'))
                    ->copyMessageDuration(1000)
                    ->icon(Heroicon::OutlinedPhone)
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('email')
                    ->label(__('Email'))
                    ->copyable()
                    ->copyMessage(fn($state) => __('Email') . " $state " . __('copied to clipboard'))
                    ->copyMessageDuration(1000)
                    ->icon(Heroicon::OutlinedEnvelope)
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('ownership.status')
                    ->label(__('Status'))
                    ->badge(),
                TextColumn::make('ownership.created_at')
                    ->dateTime()
                    ->label(__('Linked At')),
                TextColumn::make('ownership.updated_at')
                    ->dateTime()
                    ->label(__('Updated At')),
            ])
            ->filters([])
            ->headerActions([
                AttachAction::make()
                    ->label(__('Attach Owner'))
                    ->preloadRecordSelect()
                    ->recordSelect(function (Select $select) {
                        return $select
                            ->searchable()
                            ->getSearchResultsUsing(fn(string $search) => PrevUser::selectOptions($search))
                            ->getOptionLabelUsing(fn($value) => PrevUser::query()->find($value)?->name);
                    })
                    ->form([
                        Select::make('status')
                            ->label(__('Status'))
                            ->options([
                                'current' => 'Current',
                                'old' => 'Old',
                                null => 'Unknown',
                            ])
                            ->default('current')
                            ->required(),
                    ]),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label(__('View Owner'))
                    // Show a simple modal instead of navigating to a non-existent View page
                    ->schema([
                        Grid::make(3)->schema([
                            TextEntry::make('id')->label(__('ID')),
                            TextEntry::make('name')->label(__('Name')),
                            TextEntry::make('mobile_phone')->label(__('Phone')),
                            TextEntry::make('email')->label(__('Email')),
                            TextEntry::make('address_city')->label(__('City')),
                            TextEntry::make('address_street')->label(__('Street')),
                        ]),
                    ])
                    ->modalHeading(fn(PrevUser $record) => $record->name)
                    ->modalSubmitAction(false)
                    ->extraModalFooterActions([
                        Action::make('editOwner')
                            ->label(__('Edit Owner'))
                            ->icon(Heroicon::OutlinedPencilSquare)
                            ->url(fn(PrevUser $record) => PrevUserResource::getUrl('edit', ['record' => $record]))
                            ->openUrlInNewTab(),
                    ]),

                EditAction::make('edit-ownership')
                    ->label(__('Edit Ownership'))
                    ->schema([
                        Select::make('status')
                            ->label(__('Status'))
                            ->options([
                                'current' => 'Current',
                                'old' => 'Old',
                                null => 'Unknown',
                            ])
                            ->required(),
                    ]),
                DetachAction::make()
                    ->label(__('Detach')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                ]),
            ]);
    }
}
