<?php

namespace App\Filament\Pages;

use App\Enums\TypeEnum;
use App\Filament\Widgets\FeaturesOverview;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Support\Facades\Auth;

class Dashboard extends BaseDashboard
{
    protected static ?int $navigationSort = -2;

    protected static ?string $title = 'Welcome';

    public static function getNavigationLabel(): string
    {
        return __('Welcome');
    }

    public function getHeading(): string
    {
        return $this->getWelcome();
    }

    public function getWidgets(): array
    {
        return [
            AccountWidget::class,
            // FilamentInfoWidget::class,
            // FeaturesOverview::class,
        ];
    }

    protected function getWelcome(): string
    {
        $user = Auth::user()->typeUser;

        switch ($user) {
            case TypeEnum::EmployeeManagement:
                $text = __('Welcome to the HR System!');

                break;
            case TypeEnum::EcommerceShop:
                $text = __('Welcome to Our Online Store!');

                break;
            case TypeEnum::Blog:
                $text = __('Welcome to Our Blog!');

                break;
            default:
                $text = __('Welcome!');
        }

        return $text;
    }
}
