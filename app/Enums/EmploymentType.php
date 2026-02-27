<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;

enum EmploymentType: string implements HasColor, HasIcon, HasLabel
{
    case FullTime = 'full_time';

    case PartTime = 'part_time';

    case Contractor = 'contractor';

    case Intern = 'intern';

    public function getLabel(): string
    {
        return match ($this) {
            self::FullTime => __('Full Time'),
            self::PartTime => __('Part Time'),
            self::Contractor => __('Contractor'),
            self::Intern => __('Intern'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::FullTime => 'success',
            self::PartTime => 'info',
            self::Contractor => 'warning',
            self::Intern => 'gray',
        };
    }

    public function getIcon(): Heroicon
    {
        return match ($this) {
            self::FullTime => Heroicon::UserGroup,
            self::PartTime => Heroicon::Clock,
            self::Contractor => Heroicon::Briefcase,
            self::Intern => Heroicon::AcademicCap,
        };
    }
}