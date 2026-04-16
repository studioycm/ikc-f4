<?php

namespace App\Filament\Resources\PrevShowPayments\Pages;

use App\Filament\Resources\PrevShowPayments\PrevShowPaymentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPrevShowPayments extends ListRecords
{
    protected static string $resource = PrevShowPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
