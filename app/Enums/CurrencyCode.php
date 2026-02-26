<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum CurrencyCode: string implements HasLabel
{
    case Usd = 'usd';

    case Eur = 'eur';

    case Gbp = 'gbp';

    case Cad = 'cad';

    case Aud = 'aud';

    case Jpy = 'jpy';

    case Brl = 'brl';

    case Inr = 'inr';

    public function getLabel(): string
    {
        return match ($this) {
            self::Usd => __('US Dollar'),
            self::Eur => __('Euro'),
            self::Gbp => __('British Pound'),
            self::Cad => __('Canadian Dollar'),
            self::Aud => __('Australian Dollar'),
            self::Jpy => __('Japanese Yen'),
            self::Brl => __('Brazilian Real'),
            self::Inr => __('Indian Rupee'),
        };
    }
}
