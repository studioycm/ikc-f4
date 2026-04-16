<?php

namespace App\Filament\Resources\PrevTitleResource\Pages;

use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use App\Filament\Resources\PrevTitleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPrevTitle extends EditRecord
{
    protected static string $resource = PrevTitleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
