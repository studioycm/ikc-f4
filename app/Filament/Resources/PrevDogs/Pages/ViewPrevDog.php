<?php

namespace App\Filament\Resources\PrevDogs\Pages;

use Filament\Actions\EditAction;
use Filament\Actions\Action;
use App\Filament\Resources\PrevDogs\PrevDogResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPrevDog extends ViewRecord
{
    protected static string $resource = PrevDogResource::class;

    public function getTitle(): string
    {
        return __('Displaying') . " " . __('dog/model/general.labels.singular') . ': ' . $this->record->full_name . ' #' . $this->record->SagirID;
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            Action::make('pedigree')
                ->label(__('Manage Pedigree'))
                ->icon('heroicon-m-share')
                ->url(PrevDogResource::getUrl('pedigree', ['record' => $this->record])),
        ];
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }

    public function getContentTabLabel(): ?string
    {
        return __('dog/model/general.labels.singular');
    }
}
