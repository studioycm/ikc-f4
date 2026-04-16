<?php

namespace App\Filament\User\Resources\BreedingInquiries\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use App\Filament\User\Resources\BreedingInquiries\BreedingInquiryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

class EditBreedingInquiry extends EditRecord
{
    protected static string $resource = BreedingInquiryResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['puppies'] = collect($data['puppies'] ?? [])
            ->map(function ($puppy) {
                $puppy['uuid'] = $puppy['uuid'] ?? (string)Str::uuid();
                return $puppy;
            })
            ->toArray();

        return $data;
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
