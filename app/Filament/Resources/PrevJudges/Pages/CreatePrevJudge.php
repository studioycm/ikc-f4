<?php

namespace App\Filament\Resources\PrevJudges\Pages;

    use App\Filament\Resources\PrevJudges\PrevJudgeResource;
    use Filament\Resources\Pages\CreateRecord;

    class CreatePrevJudge extends CreateRecord {
        protected static string $resource = PrevJudgeResource::class;

        protected function getHeaderActions(): array {
        return [

        ];
        }
    }
