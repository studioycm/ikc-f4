<?php

namespace App\Filament\Resources\PrevJudges\Pages;

    use App\Filament\Resources\PrevJudges\PrevJudgeResource;
    use Filament\Actions\DeleteAction;
    use Filament\Resources\Pages\EditRecord;

    class EditPrevJudge extends EditRecord {
        protected static string $resource = PrevJudgeResource::class;

        protected function getHeaderActions(): array {
        return [
        DeleteAction::make(),
        ];
        }
    }
