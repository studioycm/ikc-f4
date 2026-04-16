<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\PrevColorResource\Pages\ListPrevColors;
use App\Filament\Resources\PrevColorResource\Pages\CreatePrevColor;
use App\Filament\Resources\PrevColorResource\Pages\EditPrevColor;
use App\Filament\Resources\PrevColorResource\Pages;
use App\Models\PrevColor;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

// use App\Filament\Exports\DogExporter;
// use App\Filament\Imports\DogImporter;
// use Filament\Tables\Actions\ExportAction;
// use Filament\Tables\Actions\ImportAction;

class PrevColorResource extends Resource
{
    protected static ?string $model = PrevColor::class;

    public static function getModelLabel(): string
    {
        return __('Color');
    }
    public static function getPluralModelLabel(): string
    {
        return __('Colors');
    }
    public static function getNavigationGroup(): string
    {
        return __('dog/model/general.labels.navigation_group');
    }
    public static function getNavigationLabel(): string
    {
        return __('Colors');
    }

    protected static ?int $navigationSort = 3;

    protected static string | \BackedEnum | null $navigationIcon = 'fab-delicious';

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
                TextInput::make('ColorNameHE')
                    ->maxLength(200),
                TextInput::make('ColorNameEN')
                    ->maxLength(200),
                TextInput::make('Remark')
                    ->maxLength(4000),
                TextInput::make('OldCode')
                    ->numeric(),
                TextInput::make('status')
                    ->maxLength(50),
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
                TextColumn::make('ColorNameHE')
                    ->searchable(),
                TextColumn::make('ColorNameEN')
                    ->searchable(),
                TextColumn::make('Remark')
                    ->searchable(),
                TextColumn::make('OldCode')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->searchable(),
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
            'index' => ListPrevColors::route('/'),
            'create' => CreatePrevColor::route('/create'),
            'edit' => EditPrevColor::route('/{record}/edit'),
        ];
    }
}
