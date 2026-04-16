<?php

namespace App\Filament\Resources\PrevPayments\Pages;

use App\Filament\Resources\PrevPayments\PrevPaymentResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePrevPayment extends CreateRecord
{
    protected static string $resource = PrevPaymentResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
