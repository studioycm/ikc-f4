<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\ExportAction;
use App\Filament\Resources\PrevShowPaymentResource\Pages\ListPrevShowPayments;
use App\Filament\Resources\PrevShowPaymentResource\Pages\CreatePrevShowPayment;
use App\Filament\Resources\PrevShowPaymentResource\Pages\EditPrevShowPayment;
use App\Filament\Exports\PrevShowPaymentExporter;
use App\Filament\Resources\PrevShowPaymentResource\Pages;
use App\Models\PrevShowPayment;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PrevShowPaymentResource extends Resource
{
    protected static ?string $model = PrevShowPayment::class;

    protected static ?string $slug = 'prev-show-payments';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 40;

    public static function getModelLabel(): string
    {
        return __('Show Payment');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Show Payments');
    }

    public static function getNavigationGroup(): string
    {
        return __('Shows Management');
    }

    public static function getNavigationLabel(): string
    {
        return __('Show Payments');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('DataID')
                    ->label(__('ID'))
                    ->required()
                    ->integer(),

                DatePicker::make('ModificationDateTime')
                    ->label(__('Modified')),

                DatePicker::make('CreationDateTime')
                    ->label(__('Created')),

                TextInput::make('SagirID')
                    ->label(__('Sagir ID'))
                    ->numeric(),

                TextInput::make('RegistrationID')
                    ->label(__('Registration'))
                    ->numeric(),

                TextInput::make('DogID')
                    ->label(__('Dog DataID'))
                    ->numeric(),

                TextInput::make('PaymentAmount')
                    ->label(__('Cost'))
                    ->numeric(),

                TextInput::make('Last4Digits')
                    ->label(__('Last 4 Digits')),

                TextInput::make('OwnerSocialID')
                    ->label(__('Owner Social ID')),

                TextInput::make('NameOnCard')
                    ->label(__('Name on Card')),

                TextInput::make('BuyerIP')
                    ->label(__('Buyer IP')),

                TextInput::make('PaymentSubject')
                    ->label(__('Payment Subject')),

                TextInput::make('CartKey')
                    ->label(__('Cart Key')),

                TextInput::make('PaymentStatus')
                    ->label(__('Status')),

                Placeholder::make('created_at')
                    ->label(__('Created Date'))
                    ->content(fn(?PrevShowPayment $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label(__('Last Modified Date'))
                    ->content(fn(?PrevShowPayment $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('DataID')->label(__('ID')),

                TextColumn::make('ModificationDateTime')
                    ->date()
                    ->label(__('Modified')),

                TextColumn::make('CreationDateTime')
                    ->date()
                    ->label(__('Created')),

                TextColumn::make('SagirID')->label(__('Sagir ID')),

                TextColumn::make('RegistrationID')->label(__('Registration')),

                TextColumn::make('DogID')->label(__('Dog DataID')),

                TextColumn::make('PaymentAmount')->label(__('Cost')),

                TextColumn::make('Last4Digits')->label(__('Last 4 Digits')),

                TextColumn::make('OwnerSocialID')->label(__('Owner Social ID')),

                TextColumn::make('NameOnCard')->label(__('Name on Card')),

                TextColumn::make('BuyerIP')->label(__('Buyer IP')),

                TextColumn::make('PaymentSubject')->label(__('Payment Subject')),

                TextColumn::make('CartKey')->label(__('Cart Key')),

                TextColumn::make('PaymentStatus')->label(__('Status')),
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
                ExportBulkAction::make()
                    ->label(__('Export Selected'))
                    ->icon('fas-file-export')
                    ->color('primary')
                    ->iconPosition('after')
                    ->exporter(PrevShowPaymentExporter::class),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                ExportAction::make()
                    ->label(__('Export All'))
                    ->icon('fas-file-export')
                    ->color('primary')
                    ->iconPosition('after')
                    ->exporter(PrevShowPaymentExporter::class),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPrevShowPayments::route('/'),
            'create' => CreatePrevShowPayment::route('/create'),
            'edit' => EditPrevShowPayment::route('/{record}/edit'),
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
