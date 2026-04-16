<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\PrevHairResource\Pages\ListPrevHairs;
use App\Filament\Resources\PrevHairResource\Pages\CreatePrevHair;
use App\Filament\Resources\PrevHairResource\Pages\EditPrevHair;
use App\Filament\Resources\PrevHairResource\Pages;
use App\Models\PrevHair;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PrevHairResource extends Resource
{
    protected static ?string $model = PrevHair::class;

    public static function getModelLabel(): string
    {
        return __('Hair Type');
    }
    public static function getPluralModelLabel(): string
    {
        return __('Hair Types');
    }
    public static function getNavigationGroup(): string
    {
        return __('dog/model/general.labels.navigation_group');
    }
    public static function getNavigationLabel(): string
    {
        return __('Hair Types');
    }

    protected static ?int $navigationSort = 4;

    protected static string | \BackedEnum | null $navigationIcon = 'fas-wind';

//    public static function getNavigationBadge(): ?string
//    {
//        return static::getModel()::count();
//    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('DataID')
                    ->numeric(),
                DateTimePicker::make('ModificationDateTime'),
                DateTimePicker::make('CreationDateTime'),
                TextInput::make('HairNameHE')
                    ->maxLength(200),
                TextInput::make('HairNameEN')
                    ->maxLength(200),
                TextInput::make('Remark')
                    ->maxLength(4000),
                TextInput::make('OldCode')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('DataID')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('ModificationDateTime')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('CreationDateTime')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('HairNameHE')
                    ->searchable(),
                TextColumn::make('HairNameEN')
                    ->searchable(),
                TextColumn::make('Remark')
                    ->searchable(),
                TextColumn::make('OldCode')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
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
            'index' => ListPrevHairs::route('/'),
            'create' => CreatePrevHair::route('/create'),
            'edit' => EditPrevHair::route('/{record}/edit'),
        ];
    }
}
