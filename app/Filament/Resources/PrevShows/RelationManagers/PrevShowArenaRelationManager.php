<?php

namespace App\Filament\Resources\PrevShows\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class PrevShowArenaRelationManager extends RelationManager
{
    protected static string $relationship = 'arenas';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Arenas');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('GroupName')
                    ->required()
                    ->maxLength(255)
                    ->label(__('Name')),
                TextInput::make('OrderID')
                    ->required()
                    ->numeric()
                    ->label(__('Order')),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function ($query) {
                return $query
                    ->with(['judges']);
            })
            ->columns([
                TextColumn::make('id')->label(__('ID'))->toggleable(),
                TextColumn::make('GroupName')->label(__('Name'))->toggleable(),
                TextColumn::make('judges.JudgeNameHE')
                    ->label(__('Judges'))
                    ->separator('; ')
                    ->toggleable(),
                TextColumn::make('OrderID')->label(__('Order'))->numeric()->toggleable(),
            ])
            ->headerActions([])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([]);
    }
}
