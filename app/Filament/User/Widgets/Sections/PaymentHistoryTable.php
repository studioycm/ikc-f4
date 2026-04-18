<?php

namespace App\Filament\User\Widgets\Sections;

use Filament\Tables\Columns\TextColumn;
use Filament\Actions\ViewAction;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use App\Filament\User\Widgets\Concerns\InteractsWithCurrentPrevUser;
use App\Models\PrevPayment;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Support\Icons\Heroicon;

class PaymentHistoryTable extends BaseWidget
{
    use InteractsWithCurrentPrevUser;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $prevUserId = $this->getCurrentPrevUserId();
        $prevUserEmail = $this->getCurrentPrevUser()->email;

        return $table
            ->query(
                PrevPayment::query()
                    ->when(
                        blank($prevUserId) && blank($prevUserEmail),
                        fn($query) => $query->whereRaw('1 = 0'),
                        fn($query) => $query->where('email', '=', $prevUserEmail)
                            ->orWhere('created_by', $prevUserId)
                    )
                    ->with(['club:id,Name', 'breed:id,BreedName', 'dog:id,SagirID,Heb_Name,Eng_Name'])
                    ->orderByDesc('payment_date_time')
            )
            ->columns([
                TextColumn::make('id')
                    ->label(__('ID'))
                    ->sortable(),
                TextColumn::make('approval_number')
                    ->label(__('Approval Number'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('desc')
                    ->label(__('Description'))
                    ->wrap()
                    ->toggleable(),
                TextColumn::make('amount')
                    ->label(__('Cost'))
                    ->money(currency: 'ILS')
                    ->sortable(),
                TextColumn::make('club.Name')
                    ->label(__('Club'))
                    ->toggleable(),
                TextColumn::make('breed.BreedName')
                    ->label(__('Breed'))
                    ->toggleable(),
                TextColumn::make('dog.SagirID')
                    ->label(__('Dog'))
                    ->description(fn(PrevPayment $record): ?string => $record->dog?->full_name)
                    ->toggleable(),
                TextColumn::make('payment_date_time')
                    ->label(__('Paid at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->modalHeading(fn(PrevPayment $record): string => __('Payment #:id', ['id' => $record->id]))
                    ->schema(fn(Schema $schema): Schema => $schema->components([
                        Section::make(__('Payment Details'))
                            ->schema([
                                TextEntry::make('approval_number')->label(__('Approval Number')),
                                TextEntry::make('desc')->label(__('Description')),
                                TextEntry::make('amount')->label(__('Cost'))->money(currency: 'ILS'),
                                TextEntry::make('payment_date_time')->label(__('Paid At'))->dateTime(),
                                TextEntry::make('club.Name')->label(__('Club')),
                                TextEntry::make('breed.BreedName')->label(__('Breed')),
                                TextEntry::make('dog.SagirID')->label(__('Dog')),
                                TextEntry::make('dog.full_name')->label(__('Dog Name')),
                            ])
                            ->columns(2),
                    ])),
            ])
            ->heading(__('Payment History'))
            ->description(__('Review your latest payments, approvals, and linked dogs.'))
            ->defaultSort('payment_date_time', 'desc')
            ->paginated([5, 10, 15, 'all'])
            ->defaultPaginationPageOption(5)
            ->emptyStateHeading(__('No Payments Found'))
            ->emptyStateDescription(__('Payments linked to your account will appear here.'))
            ->emptyStateIcon(Heroicon::OutlinedBanknotes);
    }
}
