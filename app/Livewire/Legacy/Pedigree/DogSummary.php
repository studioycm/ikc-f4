<?php

namespace App\Livewire\Legacy\Pedigree;

use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Support\Enums\TextSize;
use App\Models\PrevDog;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Filament\Support\Enums\FontWeight;

class DogSummary extends Component implements HasForms, HasInfolists, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithInfolists;

    public ?int $subjectId = null;

    public ?PrevDog $subject = null;

    public function mount(?int $subjectId = null): void
    {
        $this->subjectId = $subjectId;
        if ($subjectId) {
            $this->subject = PrevDog::with(['breed', 'color', 'owners', 'titles'])->find($subjectId);
        }
    }

    public function summary(Schema $schema): Schema
    {
        $d = $this->subject;

        if (!$d) {
            return $schema->components([
                TextEntry::make('placeholder')
                    ->label(false)
                    ->state(__('Select a dog to view its summary')),
            ]);
        }

        return $schema->record($d)->components([
            Grid::make(8)->schema([
                TextEntry::make('full_name')
                    ->hiddenLabel()
                    ->size(TextSize::Large)
                    ->weight(FontWeight::Bold)
                    ->columnSpan(3),
                TextEntry::make('isbr')
                    ->label(__('I.S.B.R'))
                    ->state(fn() => $d->sagir_prefix?->code() . ' - ' . $d->SagirID . ' | ' . ($d->ImportNumber ?: '—'))
                    ->columnSpan(2),
                TextEntry::make('DnaID')->label(__('DNA')),
                TextEntry::make('Chip')->label(__('Chip')),
                TextEntry::make('id')->label(__('ID')),
                TextEntry::make('BirthDate')->label(__('D.O.B'))->date('Y-m-d'),
                TextEntry::make('breed.BreedName')->label(__('Breed')),
                TextEntry::make('color.ColorNameHE')->label(__('Color')),
                TextEntry::make('owners')
                    ->label(__('Owners'))
                    ->state(fn() => $d->owners?->pluck('name')->implode(', ') ?: '—')
                    ->columnSpan(2),
                TextEntry::make('titles')
                    ->label(__('Titles'))
                    ->state(fn() => $d->titles?->pluck('name')->implode(', ') ?: '—')
                    ->limit(30)
                    ->tooltip(fn() => $d->titles?->pluck('name')->implode(', '))
                    ->columnSpan(2),
            ]),
        ]);
    }

    public function render(): View
    {
        return view('livewire.legacy.pedigree.dog-summary');
    }
}
