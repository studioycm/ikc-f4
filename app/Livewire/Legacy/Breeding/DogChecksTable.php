<?php

namespace App\Livewire\Legacy\Breeding;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use App\Services\Legacy\Breeding\BreedingDogCheckService;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Illuminate\Support\Facades\App;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Reactive;
use Livewire\Component;
use Filament\Support\Icons\Heroicon;
use Filament\Support\Enums\FontWeight;

class DogChecksTable extends Component implements HasActions, HasForms, HasInfolists
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithInfolists;

    #[Reactive]
    public ?string $sagirId = null;

    public string $role = 'female';

    public string $title = '';

    public array $configOverrides = [];

    public string $direction = 'ltr';

    public ?string $selectedActionKey = null;

    public ?string $selectedActionHeading = null;

    public ?string $selectedActionDescription = null;

    protected BreedingDogCheckService $service;

    public function boot(BreedingDogCheckService $service): void
    {
        $this->service = $service;
    }

    public function mount(
        ?string $sagirId = null,
        string  $role = 'female',
        string  $title = '',
        array   $configOverrides = [],
    ): void
    {
        $this->sagirId = $sagirId;
        $this->role = $role;
        $this->title = $title;
        $this->configOverrides = $configOverrides;
        $this->direction = $this->resolveDirectionFromLocale();
    }

    #[Computed]
    public function report(): ?array
    {
        return $this->service->buildBySagirId(
            sagirId: $this->sagirId,
            role: $this->role,
            overrides: $this->configOverrides,
        );
    }

    public function checkDetailsInfolist(string $checkKey): Schema
    {
        $report = $this->report;
        $check = collect($report['checks'] ?? [])->firstWhere('key', $checkKey);

        if (!$check) {
            return Schema::make()
                ->state([])
                ->components([]);
        }

        return Schema::make()
            ->state($check)
            ->components([
                Section::make(__('Check Details'))
                    ->schema([
                        TextEntry::make('label')
                            ->label(__('Check'))
                            ->weight(FontWeight::Bold),
                        TextEntry::make('state_label')
                            ->label(__('Status'))
                            ->badge(),
                        TextEntry::make('value')
                            ->label(__('Value'))
                            ->visible(fn($state): bool => filled($state))
                            ->default('—'),
                    ])
                    ->columns(2),
                Section::make(__('Dog Information'))
                    ->schema([
                        TextEntry::make('dog.name')
                            ->label(__('Dog Name'))
                            ->state(data_get($report, 'dog.name'))
                            ->default('—'),
                        TextEntry::make('dog.sagir_id')
                            ->label(__('Sagir ID'))
                            ->state(data_get($report, 'dog.sagir_id'))
                            ->default('—'),
                    ])
                    ->columns(2),
            ]);
    }

    public function viewDetailsAction(): Action
    {
        return Action::make('viewDetails')
            ->icon(Heroicon::InformationCircle)
            ->modalHeading(__('Check Details'))
            ->modalContent(function (array $arguments): Schema {
                $checkKey = $arguments['checkKey'] ?? null;

                if (!$checkKey) {
                    return Schema::make()->state([])->components([]);
                }

                return $this->checkDetailsInfolist($checkKey);
            })
            ->modalSubmitAction(false)
            ->modalCancelActionLabel(__('Close'));
    }

    public function rowAction(): Action
    {
        return Action::make('rowAction')
            ->modalHeading(fn(array $arguments): string => $arguments['heading'] ?? __('Action'))
            ->modalDescription(fn(array $arguments): string => $arguments['description'] ?? '')
            ->action(function (array $arguments): void {
                $this->selectedActionKey = $arguments['actionKey'] ?? null;
                $this->selectedActionHeading = $arguments['heading'] ?? null;
                $this->selectedActionDescription = $arguments['description'] ?? null;
            });
    }

    public function getTooltipContent(array $check): string
    {
        $content = $check['label'] . ': ' . $check['state_label'];

        if (is_scalar($check['value'] ?? null) && filled($check['value'])) {
            $content .= "\n" . __('Value') . ': ' . $check['value'];
        }

        return $content;
    }

    public function render()
    {
        return view('livewire.legacy.breeding.dog-checks-table');
    }

    protected function resolveDirectionFromLocale(): string
    {
        $locale = App::currentLocale();

        return str_starts_with($locale, 'he') || str_starts_with($locale, 'ar')
            ? 'rtl'
            : 'ltr';
    }
}
