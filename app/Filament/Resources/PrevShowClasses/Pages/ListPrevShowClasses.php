<?php

namespace App\Filament\Resources\PrevShowClasses\Pages;

use App\Filament\Resources\PrevShowClasses\PrevShowClassResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPrevShowClasses extends ListRecords
{
    protected static string $resource = PrevShowClassResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
