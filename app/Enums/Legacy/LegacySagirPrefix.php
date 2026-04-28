<?php

namespace App\Enums\Legacy;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum LegacySagirPrefix: int implements HasColor, HasIcon, HasLabel
{
    case ISR = 1;
    case IMP = 2;
    case APX = 3;
    case EXT = 4;
    case NUL = 5;

    public function code(): string
    {
        return $this->name;
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::ISR => 'ISR',
            self::IMP => 'IMP',
            self::APX => 'APX',
            self::EXT => 'EXT',
            self::NUL => 'NUL',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::ISR => 'blue',
            self::IMP => 'purple',
            self::APX => 'yellow',
            self::EXT => 'green',
            self::NUL => 'red',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::ISR => 'fas-star-of-david',
            self::IMP => 'fas-globe',
            self::APX => 'fas-notes-medical',
            self::EXT => 'fas-file-export',
            self::NUL => 'fas-exclamation-circle',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::ISR => __('Israeli'),
            self::IMP => __('Import'),
            self::APX => __('Appendix'),
            self::EXT => __('External'),
            self::NUL => __('No prefix'),
        };
    }
}
