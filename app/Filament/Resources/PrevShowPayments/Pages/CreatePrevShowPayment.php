<?php

namespace App\Filament\Resources\PrevShowPayments\Pages;

use App\Filament\Resources\PrevShowPayments\PrevShowPaymentResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePrevShowPayment extends CreateRecord
{
    protected static string $resource = PrevShowPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
