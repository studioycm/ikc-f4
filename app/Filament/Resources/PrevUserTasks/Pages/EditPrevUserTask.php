<?php

namespace App\Filament\Resources\PrevUserTasks\Pages;

use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use App\Filament\Resources\PrevUserTasks\PrevUserTaskResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditPrevUserTask extends EditRecord
{
    protected static string $resource = PrevUserTaskResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function getTitle(): string|Htmlable
    {
        return __('Edit task:') . ' ' . ($this->getRecord()->task_name ?: '#' . $this->getRecord()->id);
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
