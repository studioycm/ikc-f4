<?php

namespace App\Filament\Resources\PrevUserRequests\Pages;

use Filament\Actions\EditAction;
use App\Filament\Resources\PrevUserRequests\PrevUserRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPrevUserRequest extends ViewRecord
{
    protected static string $resource = PrevUserRequestResource::class;

    public function getTitle(): string
    {
        $topic_label = $this->getRecord()->topic->getLabel();
        return __('Displaying') . " " . __('User Request') . ": " . ($topic_label ?? '#' . $this->record->id);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
