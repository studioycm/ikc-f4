<?php

namespace App\Filament\Resources\PrevShowResults\Pages;

use Filament\Actions\EditAction;
use App\Filament\Resources\PrevShowResults\PrevShowResultResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPrevShowResult extends ViewRecord
{
    protected static string $resource = PrevShowResultResource::class;

    public function getTitle(): string
    {
        return __('Displaying') . " " . __('Result') . ': ' . $this->record->resultDog->full_name . " | " . $this->record->show->TitleName;
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }

}
