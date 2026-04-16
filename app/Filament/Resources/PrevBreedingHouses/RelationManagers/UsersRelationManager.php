<?php

namespace App\Filament\Resources\PrevBreedingHouses\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Actions\AttachAction;
use Filament\Forms\Components\Select;
use Filament\Actions\ViewAction;
use Filament\Actions\DetachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DetachBulkAction;
use App\Filament\Resources\PrevUsers\PrevUserResource;
use App\Models\PrevUser;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    protected static ?string $recordTitleAttribute = 'first_name';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Users');
    }

    public function form(Schema $schema): Schema
    {
        return PrevUserResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')->label(__('Name'))->searchable(['first_name', 'last_name', 'first_name_en', 'last_name_en']),
                TextColumn::make('email')->label(__('Email'))->toggleable(),
                TextColumn::make('mobile_phone')->label(__('Phone'))->toggleable(),
                TextColumn::make('pivot.created_at')->dateTime()->label(__('Linked At')),
                TextColumn::make('pivot.updated_at')->dateTime()->label(__('Updated At')),
            ])
            ->headerActions([
                AttachAction::make()
                    ->label(__('Attach User'))
//                    ->preloadRecordSelect()
                    ->recordSelect(function (Select $select) {
                        return $select
                            ->searchable(['first_name', 'last_name', 'first_name_en', 'last_name_en'])
                            ->getSearchResultsUsing(fn(string $search) => PrevUser::selectOptions($search))
                            ->getOptionLabelUsing(fn($value) => PrevUser::query()->find($value)?->name);
                    }),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label(__('View User'))
                    ->url(fn(PrevUser $record) => PrevUserResource::getUrl('edit', ['record' => $record]))
                    ->openUrlInNewTab(),
                DetachAction::make()->label(__('Detach')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                ]),
            ]);
    }
}
