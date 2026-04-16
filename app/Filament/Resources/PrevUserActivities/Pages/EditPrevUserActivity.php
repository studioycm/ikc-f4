<?php

namespace App\Filament\Resources\PrevUserActivities\Pages;

use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use App\Filament\Resources\PrevUserActivities\PrevUserActivityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditPrevUserActivity extends EditRecord
{
    protected static string $resource = PrevUserActivityResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function getTitle(): string|Htmlable
    {
        return __('Edit activity:') . ' ' . ($this->getRecord()->Activity_Type ?: '#' . $this->getRecord()->id);
    }

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
