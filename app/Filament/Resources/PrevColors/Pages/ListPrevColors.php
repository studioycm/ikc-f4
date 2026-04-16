<?php

namespace App\Filament\Resources\PrevColors\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\PrevColors\PrevColorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPrevColors extends ListRecords
{
    protected static string $resource = PrevColorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
