<?php

namespace App\Filament\Resources\PrevDogs\RelationManagers;

use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
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
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class HealthRecordsRelationManager extends RelationManager
{
    protected static string $relationship = 'healthRecords';

    protected static ?string $recordTitleAttribute = 'type';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Health Records');
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('DataID')->label(__('ID'))->numeric(decimalPlaces: 0, thousandsSeparator: ''),
                TextColumn::make('type')->label(__('Type'))->searchable(),
                TextColumn::make('TestDate')->label(__('Test Date'))->date(),
                TextColumn::make('TestFile')
                    ->label(__('File'))
                    ->formatStateUsing(fn () => 'Open File') // Shows "Open File" instead of the long filename
                    ->icon(Heroicon::ArrowTopRightOnSquare)
                    ->color('primary')
                    ->url(function ($state) {
                        if (blank($state)) return null;

                        // Best practice: Generate the signed URL directly here
                        return Storage::disk('s3')->temporaryUrl(
                            $state,
                            now()->addMinutes(120)
                        );
                    })
                    ->openUrlInNewTab(),
                TextColumn::make('Notes')->label(__('Notes')),
                TextColumn::make('show_in_paper')->label(__('Show in Paper'))->badge(),
                TextColumn::make('created_at')->label(__('Created'))->since()->toggleable(),
                TextColumn::make('updated_at')->label(__('Updated'))->since()->toggleable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label(__('Add Health Record'))
                    ->schema([
                        TextInput::make('DataID')->label(__('ID'))->numeric()->required(),
                        TextInput::make('type')->label(__('Type'))->maxLength(255),
                        DatePicker::make('TestDate')->label(__('Test Date')),
                        TextInput::make('TestFile')->label(__('Test File'))->maxLength(255),
                        TextInput::make('Notes')->label(__('Notes'))->maxLength(1000),
                        TextInput::make('ImageResultID')->label(__('Image Result'))->maxLength(255),
                        Checkbox::make('show_in_paper')->label(__('Show in Paper')),
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
                        TextInput::make('ImageResultID')->label(__('Image Result'))->maxLength(255),
                        Checkbox::make('show_in_paper')->label(__('Show in Paper')),
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
