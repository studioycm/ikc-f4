<?php

namespace App\Filament\Resources\PrevUsers\Pages;

use App\Filament\Resources\PrevUsers\PrevUserResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPrevUser extends ViewRecord
{
    protected static string $resource = PrevUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }

    public function getContentTabLabel(): ?string
    {
        return __('User');
    }
}
