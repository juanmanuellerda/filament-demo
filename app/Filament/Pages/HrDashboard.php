<?php

namespace App\Filament\Pages;

use App\Enums\TypeEnum;
use App\Filament\Widgets\BudgetBurnRateChart;
use App\Filament\Widgets\DepartmentLeaveLoadChart;
use App\Filament\Widgets\ProjectHealthChart;
use App\Filament\Widgets\UtilizationRateChart;
use App\Filament\Widgets\WorkforceInsightsStats;
use BackedEnum;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

class HrDashboard extends BaseDashboard
{
    protected static string $routePath = 'hr';

    protected static ?string $title = 'HR Dashboard';

    public function getTitle(): string
{
    return __('HR Dashboard');
}

    public static function getNavigationLabel(): string
    {
        return __('HR Dashboard');
    }

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static ?int $navigationSort = 3;

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()->typeUser == TypeEnum::EmployeeManagement;
    }

    public function getWidgets(): array
    {
        return [
            WorkforceInsightsStats::class,
            DepartmentLeaveLoadChart::class,
            ProjectHealthChart::class,
            UtilizationRateChart::class,
            BudgetBurnRateChart::class,
        ];
    }
}