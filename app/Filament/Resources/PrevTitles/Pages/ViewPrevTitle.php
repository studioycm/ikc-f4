<?php

namespace App\Filament\Resources\PrevTitles\Pages;

use Filament\Actions\EditAction;
use App\Filament\Resources\PrevTitles\PrevTitleResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPrevTitle extends ViewRecord
{
    protected static string $resource = PrevTitleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
