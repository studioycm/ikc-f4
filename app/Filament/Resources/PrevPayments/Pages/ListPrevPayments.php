<?php

namespace App\Filament\Resources\PrevPayments\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\PrevPayments\PrevPaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPrevPayments extends ListRecords
{
    protected static string $resource = PrevPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
