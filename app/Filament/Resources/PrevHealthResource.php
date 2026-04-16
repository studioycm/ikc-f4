<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use App\Filament\Resources\PrevHealthResource\Pages\ListPrevHealths;
use App\Filament\Resources\PrevHealthResource\Pages\CreatePrevHealth;
use App\Filament\Resources\PrevHealthResource\Pages\EditPrevHealth;
use App\Filament\Resources\PrevHealthResource\Pages;
use App\Models\PrevHealth;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PrevHealthResource extends Resource
{
    protected static ?string $model = PrevHealth::class;

    protected static string | \BackedEnum | null $navigationIcon = 'fas-stethoscope';

    protected static ?int $navigationSort = 10;

    public static function getNavigationGroup(): string
    {
        return __('dog/model/general.labels.navigation_group');
    }

    public static function getNavigationLabel(): string
    {
        return __('Health Records');
    }

    public static function getModelLabel(): string
    {
        return __('Health Record');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Health Records');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('DataID')
                    ->required()
                    ->integer(),

                TextInput::make('type'),

                DatePicker::make('ModificationDateTime'),

                DatePicker::make('CreationDateTime'),

                TextInput::make('SagirID')
                    ->numeric(),

                DatePicker::make('TestDate'),

                TextInput::make('TestFile'),

                TextInput::make('Notes'),

                TextInput::make('ImageResultID'),

                Checkbox::make('show_in_paper'),

                Placeholder::make('created_at')
                    ->label(__('Created Date'))
                    ->content(fn(?PrevHealth $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label(__('Last Modified Date'))
                    ->content(fn(?PrevHealth $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('DataID'),

                TextColumn::make('type'),

                TextColumn::make('ModificationDateTime')
                    ->date(),

                TextColumn::make('CreationDateTime')
                    ->date(),

                TextColumn::make('SagirID'),

                TextColumn::make('TestDate')
                    ->date(),

                TextColumn::make('TestFile'),

                TextColumn::make('Notes'),

                TextColumn::make('ImageResultID'),

                TextColumn::make('show_in_paper'),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPrevHealths::route('/'),
            'create' => CreatePrevHealth::route('/create'),
            'edit' => EditPrevHealth::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
