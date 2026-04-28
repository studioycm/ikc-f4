<?php

namespace App\Filament\Resources\PrevDogs\Pages;

use Filament\Actions\EditAction;
use Filament\Actions\Action;
use App\Filament\Resources\PrevDogs\PrevDogResource;
use Filament\Actions;
use Filament\Resources\Concerns\HasTabs;
use Filament\Resources\Pages\Concerns\HasRelationManagers;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;

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
                ->icon(Heroicon::Share)
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

    protected function resolveRecord(int | string $key): Model
    {
        return parent::resolveRecord($key)->load([
            'breedinghouse.users:id,first_name,last_name,first_name_en,last_name_en', // Nested
            'mother.owners:id,first_name,last_name,first_name_en,last_name_en',       // Nested Fallback
        ]);
    }

}
