<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum TypeEnum: string implements HasLabel
{
    case EcommerceShop = 'e-commerce_shop';
    case Blog = 'blog';
    case EmployeeManagement = 'employee_management';
    case Demo = 'demo';

    public function getLabel(): string
    {
        return __(str($this->name)->snake(' ')->ucfirst()->value);
    }
}