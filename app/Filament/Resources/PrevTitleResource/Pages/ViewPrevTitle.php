<?php

namespace App\Filament\Resources\PrevTitleResource\Pages;

use Filament\Actions\EditAction;
use App\Filament\Resources\PrevTitleResource;
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
