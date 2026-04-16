<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use App\Filament\Resources\BreedingInquiryResource\Pages\ListBreedingInquiries;
use App\Filament\Resources\BreedingInquiryResource\Pages\CreateBreedingInquiry;
use App\Filament\Resources\BreedingInquiryResource\Pages\ViewBreedingInquiry;
use App\Filament\Resources\BreedingInquiryResource\Pages\EditBreedingInquiry;
use App\Filament\Resources\BreedingInquiryResource\Pages;
use App\Models\BreedingInquiry;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BreedingInquiryResource extends Resource
{
    protected static ?string $model = BreedingInquiry::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-text';

    protected static ?int $navigationSort = 3;

    public static function getModelLabel(): string
    {
        return __('Breeding Inquiry');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Breeding Inquiries');
    }

    public static function getNavigationGroup(): string
    {
        return __('Breedings Management');
    }

    public static function getNavigationLabel(): string
    {
        return __('Breeding Inquiries');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                TextInput::make('prev_user_id')
                    ->required()
                    ->numeric(),
                TextInput::make('female_sagir_id')
                    ->required()
                    ->numeric(),
                TextInput::make('male_sagir_id')
                    ->numeric(),
                TextInput::make('litter_report_name')
                    ->maxLength(255),
                DatePicker::make('breeding_date'),
                DatePicker::make('birthing_date'),
                TextInput::make('puppies'),
                TextInput::make('status')
                    ->required()
                    ->maxLength(255)
                    ->default('draft'),
                DateTimePicker::make('submitted_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('prev_user_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('female_sagir_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('male_sagir_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('litter_report_name')
                    ->searchable(),
                TextColumn::make('breeding_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('birthing_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('status')
                    ->searchable(),
                TextColumn::make('submitted_at')
                    ->dateTime()
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
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
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
            'index' => ListBreedingInquiries::route('/'),
            'create' => CreateBreedingInquiry::route('/create'),
            'view' => ViewBreedingInquiry::route('/{record}'),
            'edit' => EditBreedingInquiry::route('/{record}/edit'),
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
