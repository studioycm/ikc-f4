<?php

namespace App\Filament\Resources\BreedingInquiries\Pages;

use Filament\Actions\EditAction;
use App\Filament\Resources\BreedingInquiries\BreedingInquiryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBreedingInquiry extends ViewRecord
{
    protected static string $resource = BreedingInquiryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
