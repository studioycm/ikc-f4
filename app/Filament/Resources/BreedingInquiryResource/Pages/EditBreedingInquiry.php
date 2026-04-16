<?php

namespace App\Filament\Resources\BreedingInquiryResource\Pages;

use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use App\Filament\Resources\BreedingInquiryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBreedingInquiry extends EditRecord
{
    protected static string $resource = BreedingInquiryResource::class;

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
