<?php

namespace App\Filament\Resources\PrevDogs\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\PrevDogs\Widgets\DogStats;
use App\Enums\Legacy\LegacySagirPrefix;
use App\Filament\Resources\PrevDogs\PrevDogResource;
use App\Models\PrevDog;
use Filament\Actions;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Concerns\HasTabs;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Enums\IconPosition;

class ListPrevDogs extends ListRecords
{
    use ExposesTableToWidgets;
    use HasTabs;

    protected static string $resource = PrevDogResource::class;

    public function getTitle(): string
    {
        return __('Studbook');
    }

    public static function getNavigationLabel(): string
    {
        return __('Studbook');
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
//            Actions\Action::make('pedigree')
//                ->label(__('Manage Pedigree'))
//                ->icon('heroicon-m-share')
//                ->url(PrevDogResource::getUrl('pedigree')),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            DogStats::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int | array
    {
        return 6;
    }


    public function setPage($page, $pageName = 'page'): void
    {
        parent::setPage($page, $pageName);

        $this->dispatch('scroll-to-top');
    }

    public function getTabs(): array
    {
        $count_israeli = PrevDog::where('sagir_prefix', LegacySagirPrefix::ISR->value)->count();
        $count_import = PrevDog::where('sagir_prefix', LegacySagirPrefix::IMP->value)->count();
        $count_external = PrevDog::where('sagir_prefix', LegacySagirPrefix::EXT->value)->count();
        $count_appendix = PrevDog::where('sagir_prefix', LegacySagirPrefix::APX->value)->count();


        return [
            'israeli' => Tab::make()
                ->label(__('ISR Studbook'))
                ->badgeIcon(LegacySagirPrefix::ISR->getIcon())
                ->badgeIconPosition(IconPosition::After)
                ->badgeColor(LegacySagirPrefix::ISR->getColor())
                ->badge(fn (): string => LegacySagirPrefix::ISR->getLabel() . ' (' . $count_israeli . ')')
                ->extraAttributes(['class' => 'fi-badge-larger'])
                ->modifyQueryUsing(function ($query) {
                    return $query->where('sagir_prefix', LegacySagirPrefix::ISR->value);
                }),

            'import' => Tab::make()
                ->label(__('IMP Studbook'))
                ->badgeIcon(LegacySagirPrefix::IMP->getIcon())
                ->badgeIconPosition(IconPosition::After)
                ->badgeColor(LegacySagirPrefix::IMP->getColor())
                ->badge(fn (): string => LegacySagirPrefix::IMP->getLabel() . ' (' . $count_import . ')')
                ->extraAttributes(['class' => 'fi-badge-larger'])
                ->modifyQueryUsing(function ($query) {
                    return $query->where('sagir_prefix', LegacySagirPrefix::IMP->value);
                }),

            'external' => Tab::make()
                ->label(__('EXT Studbook'))
                ->badgeIcon(LegacySagirPrefix::EXT->getIcon())
                ->badgeIconPosition(IconPosition::After)
                ->badgeColor(LegacySagirPrefix::EXT->getColor())
                ->badge(fn (): string => LegacySagirPrefix::EXT->getLabel() . ' (' . $count_external . ')')
                ->extraAttributes(['class' => 'fi-badge-larger'])
                ->modifyQueryUsing(function ($query) {
                    return $query->where('sagir_prefix', LegacySagirPrefix::EXT->value);
                }),

            'appendix' => Tab::make()
                ->label(__('APX Studbook'))
                ->badgeIcon(LegacySagirPrefix::APX->getIcon())
                ->badgeIconPosition(IconPosition::After)
                ->badgeColor(LegacySagirPrefix::APX->getColor())
                ->badge(fn (): string => LegacySagirPrefix::APX->getLabel() . ' (' . $count_appendix . ')')
                ->extraAttributes(['class' => 'fi-badge-larger'])
                ->modifyQueryUsing(function ($query) {
                    return $query->where('sagir_prefix', LegacySagirPrefix::APX->value);
                }),
            'all' => Tab::make()
                ->label(__('Show All'))
                ->extraAttributes(['class' => 'text-xl']),
        ];
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return 'all';
    }
}
