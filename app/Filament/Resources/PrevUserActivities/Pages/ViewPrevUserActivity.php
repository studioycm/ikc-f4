<?php

namespace App\Filament\Resources\PrevUserActivities\Pages;

use Filament\Actions\EditAction;
use App\Filament\Resources\PrevUserActivities\PrevUserActivityResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPrevUserActivity extends ViewRecord
{
    protected static string $resource = PrevUserActivityResource::class;

    public function getTitle(): string
    {
        return __('Displaying') . ' ' . __('User Activity') . ': ' . ($this->record->Activity_Type ?: '#' . $this->record->id);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
