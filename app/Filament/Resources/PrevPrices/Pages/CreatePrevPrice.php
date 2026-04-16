<?php

namespace App\Filament\Resources\PrevPrices\Pages;

use App\Filament\Resources\PrevPrices\PrevPriceResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePrevPrice extends CreateRecord
{
    protected static string $resource = PrevPriceResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
