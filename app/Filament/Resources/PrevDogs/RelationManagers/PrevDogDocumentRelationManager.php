<?php

namespace App\Filament\Resources\PrevDogs\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Filament\Support\Icons\Heroicon;

class PrevDogDocumentRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    protected static ?string $recordTitleAttribute = 'type';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Documents');
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label(__('ID'))->numeric(decimalPlaces: 0, thousandsSeparator: ''),
                TextColumn::make('type')->label(__('Type'))->searchable(),
                TextColumn::make('TestDate')->label(__('Test Date'))->date(),
                TextColumn::make('judge_name')->label(__('Judge'))->toggleable(),
                TextColumn::make('grade')->label(__('Grade'))->toggleable(),
                IconColumn::make('result')
                    ->label(__('Result'))
                    ->boolean()
                    ->trueIcon(Heroicon::OutlinedCheckCircle)
                    ->falseIcon(Heroicon::OutlinedXCircle)
                    ->trueColor('success')
                    ->falseColor('gray'),
                TextColumn::make('location')->label(__('Location'))->toggleable(),
                TextColumn::make('created_at')->label(__('Created'))->since()->toggleable(),
                TextColumn::make('updated_at')->label(__('Updated'))->since()->toggleable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label(__('Add Document'))
                    ->schema([
                        TextInput::make('type')->label(__('Type'))->maxLength(255),
                        DatePicker::make('TestDate')->label(__('Test Date')),
                        TextInput::make('TestFile')->label(__('Test File'))->maxLength(255),
                        TextInput::make('Notes')->label(__('Notes'))->maxLength(1000),
                        Checkbox::make('is_maag')->label(__('Maag?')),
                        DatePicker::make('maag_date')->label(__('Maag Date')),
                        TextInput::make('judge_name')->label(__('Judge'))->maxLength(255),
                        Checkbox::make('result')->label(__('Result')),
                        TextInput::make('grade')->label(__('Grade'))->maxLength(255),
                        TextInput::make('location')->label(__('Location'))->maxLength(255),
                    ])
                    ->mutateDataUsing(function (array $data): array {
                        $data['SagirID'] = $this->getOwnerRecord()->SagirID;

                        return $data;
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->label(__('Edit'))
                    ->schema([
                        TextInput::make('type')->label(__('Type'))->maxLength(255),
                        DatePicker::make('TestDate')->label(__('Test Date')),
                        TextInput::make('TestFile')->label(__('Test File'))->maxLength(255),
                        TextInput::make('Notes')->label(__('Notes'))->maxLength(1000),
                        Checkbox::make('is_maag')->label(__('Maag?')),
                        DatePicker::make('maag_date')->label(__('Maag Date')),
                        TextInput::make('judge_name')->label(__('Judge'))->maxLength(255),
                        Checkbox::make('result')->label(__('Result')),
                        TextInput::make('grade')->label(__('Grade'))->maxLength(255),
                        TextInput::make('location')->label(__('Location'))->maxLength(255),
                    ]),
                DeleteAction::make()->label(__('Delete')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
