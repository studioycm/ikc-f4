<?php

namespace App\Filament\Resources\BreedingInquiries\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\BreedingInquiries\BreedingInquiryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBreedingInquiries extends ListRecords
{
    protected static string $resource = BreedingInquiryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
