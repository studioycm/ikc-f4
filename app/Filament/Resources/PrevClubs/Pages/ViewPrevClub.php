<?php

namespace App\Filament\Resources\PrevClubs\Pages;

use App\Filament\Resources\PrevClubs\PrevClubResource;
use App\Models\PrevClub;
use Filament\Actions\Concerns\HasInfolist;
use Filament\Resources\Concerns\HasTabs;
use Filament\Resources\Pages\Concerns\HasRelationManagers;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;

class ViewPrevClub extends ViewRecord
{

    protected static string $resource = PrevClubResource::class;

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }

    public function getContentTabLabel(): ?string
    {
        return __('Club');
    }
}
