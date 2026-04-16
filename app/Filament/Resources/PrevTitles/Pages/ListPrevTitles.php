<?php

namespace App\Filament\Resources\PrevTitles\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\PrevTitles\PrevTitleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPrevTitles extends ListRecords
{
    protected static string $resource = PrevTitleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
