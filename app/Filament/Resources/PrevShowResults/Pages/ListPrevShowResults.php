<?php

namespace App\Filament\Resources\PrevShowResults\Pages;

use App\Filament\Resources\PrevShowResults\PrevShowResultResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPrevShowResults extends ListRecords
{
    protected static string $resource = PrevShowResultResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
