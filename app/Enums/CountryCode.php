<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum CountryCode: string implements HasLabel
{
    case Us = 'us';

    case Gb = 'gb';

    case De = 'de';

    case Fr = 'fr';

    case Ca = 'ca';

    case Au = 'au';

    case Nl = 'nl';

    case Br = 'br';

    case Jp = 'jp';

    case In = 'in';

    public function getLabel(): string
    {
        return match ($this) {
            self::Us => __('United States'),
            self::Gb => __('United Kingdom'),
            self::De => __('Germany'),
            self::Fr => __('France'),
            self::Ca => __('Canada'),
            self::Au => __('Australia'),
            self::Nl => __('Netherlands'),
            self::Br => __('Brazil'),
            self::Jp => __('Japan'),
            self::In => __('India'),
        };
    }
}
