<?php

namespace App\Filament\Resources\PrevShows\Pages;

use App\Filament\Resources\PrevShows\PrevShowResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\Concerns\HasRelationManagers;
use Filament\Resources\Pages\EditRecord;

class EditPrevShow extends EditRecord
{
    use HasRelationManagers;

    protected static string $resource = PrevShowResource::class;

    protected static bool $hasRelationManagers = true;

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }

    public function getContentTabLabel(): ?string
    {
        return __('Show');
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
