<?php

namespace App\Filament\Resources\PrevDogs\Pages;

use App\Enums\Legacy\LegacySagirPrefix;
use App\Filament\Resources\PrevDogs\PrevDogResource;
use App\Filament\Resources\PrevDogs\Widgets\DogStats;
use App\Models\PrevDog;
use Filament\Actions;
use Filament\Actions\CreateAction;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Enums\IconPosition;

class ListPrevDogs extends ListRecords
{
    use ExposesTableToWidgets;

    protected static string $resource = PrevDogResource::class;

    public function getTitle(): string
    {
        $activeTab = match ($this->activeTab) {
            'israeli' => __('Israeli'),
            'import' => __('Import'),
            'appendix' => __('Appendix'),
            'external' => __('External'),
            'all' => __('Displaying').' '.__('All'),
        };

        return __('Studbook').': '.$activeTab;
    }

    public static function getNavigationLabel(): string
    {
        return __('Studbook');
    }

    protected function getHeaderActions(): array
    {
        return [
            //            CreateAction::make(),
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

    public function getHeaderWidgetsColumns(): int|array
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

        return [
            'israeli' => Tab::make()
                ->label(false)
                ->badgeIcon(LegacySagirPrefix::ISR->getIcon())
                ->badgeIconPosition(IconPosition::After)
                ->badgeColor(LegacySagirPrefix::ISR->getColor())
                ->badge(fn (): string => __('Studbook').' '.LegacySagirPrefix::ISR->description().' ('.PrevDog::query()->where('sagir_prefix', LegacySagirPrefix::ISR->value)->count().')')
                ->deferBadge()
                ->extraAttributes(['class' => 'fi-badge-larger'])
                ->modifyQueryUsing(function ($query) {
                    return $query->where('sagir_prefix', LegacySagirPrefix::ISR->value);
                }),

            'import' => Tab::make()
                ->label(false)
                ->badgeIcon(LegacySagirPrefix::IMP->getIcon())
                ->badgeIconPosition(IconPosition::After)
                ->badgeColor(LegacySagirPrefix::IMP->getColor())
                ->badge(fn (): string => __('Studbook').' '.LegacySagirPrefix::IMP->description().' ('.PrevDog::query()->where('sagir_prefix', LegacySagirPrefix::IMP->value)->count().')')
                ->deferBadge()
                ->extraAttributes(['class' => 'fi-badge-larger'])
                ->modifyQueryUsing(function ($query) {
                    return $query->where('sagir_prefix', LegacySagirPrefix::IMP->value);
                }),

            'external' => Tab::make()
                ->label(false)
                ->badgeIcon(LegacySagirPrefix::EXT->getIcon())
                ->badgeIconPosition(IconPosition::After)
                ->badgeColor(LegacySagirPrefix::EXT->getColor())
                ->badge(fn (): string => __('Studbook').' '.LegacySagirPrefix::EXT->description().' ('.PrevDog::query()->where('sagir_prefix', LegacySagirPrefix::EXT->value)->count().')')
                ->deferBadge()
                ->extraAttributes(['class' => 'fi-badge-larger'])
                ->modifyQueryUsing(function ($query) {
                    return $query->where('sagir_prefix', LegacySagirPrefix::EXT->value);
                }),

            'appendix' => Tab::make()
                ->label(false)
                ->badgeIcon(LegacySagirPrefix::APX->getIcon())
                ->badgeIconPosition(IconPosition::After)
                ->badgeColor(LegacySagirPrefix::APX->getColor())
                ->badge(fn (): string => __('Studbook').' '.LegacySagirPrefix::APX->description().' ('.PrevDog::query()->where('sagir_prefix', LegacySagirPrefix::APX->value)->count().')')
                ->deferBadge()
                ->extraAttributes(['class' => 'fi-badge-larger'])
                ->modifyQueryUsing(function ($query) {
                    return $query->where('sagir_prefix', LegacySagirPrefix::APX->value);
                }),
            'all' => Tab::make()
                ->label(__('Show All'))
                ->extraAttributes(['class' => 'text-xl']),

        ];
    }

    public function getDefaultActiveTab(): int|null|string
    {
        return 'all';
    }
}
