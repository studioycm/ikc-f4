<?php

namespace App\Filament\Resources\PrevShows\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class PrevShowClassRelationManager extends RelationManager
{
    protected static string $relationship = 'classes';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Classes');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Minimal; classes are managed in their own resource
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label(__('ID'))->toggleable(),
                TextColumn::make('ClassID')->label(__('Code'))->toggleable(),
                TextColumn::make('ClassName')->label(__('Class Name'))->toggleable(),
                TextColumn::make('show.TitleName')->label(__('Show'))->toggleable(),
                TextColumn::make('arena.GroupName')->label(__('Arena'))->toggleable(),
                TextColumn::make('judge.JudgeNameEN')->label(__('Judge'))->toggleable(),
                TextColumn::make('OrderID')->label(__('Order'))->numeric()->toggleable(),
            ])
            ->headerActions([])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([]);
    }
}
