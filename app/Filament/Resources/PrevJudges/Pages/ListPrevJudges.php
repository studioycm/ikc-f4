<?php

namespace App\Filament\Resources\PrevJudges\Pages;

use App\Filament\Resources\PrevJudges\PrevJudgeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPrevJudges extends ListRecords
{
    protected static string $resource = PrevJudgeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
