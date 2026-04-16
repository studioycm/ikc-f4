<?php

namespace App\Filament\Resources\PrevDogImports\Pages;

use Filament\Actions\EditAction;
use App\Filament\Resources\PrevDogImports\PrevDogImportResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPrevDogImport extends ViewRecord
{
    protected static string $resource = PrevDogImportResource::class;

    public function getTitle(): string
    {
        return __('Displaying') . ' ' . __('Imported Dog') . ': ' . ($this->record->dog_name ?: '#' . $this->record->id);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
