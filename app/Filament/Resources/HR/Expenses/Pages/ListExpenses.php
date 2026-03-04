<?php

namespace App\Filament\Resources\HR\Expenses\Pages;

use App\Enums\ExpenseStatus;
use App\Filament\Resources\HR\Expenses\ExpenseResource;
use Filament\Actions\CreateAction;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;

class ListExpenses extends ListRecords
{
    use ExposesTableToWidgets;

    protected static string $resource = ExpenseResource::class;

    protected function getActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return ExpenseResource::getWidgets();
    }

    public function getTabs(): array
    {
        return [
            null => Tab::make(__('All')),
            'draft' => Tab::make(__('Draft'))
                ->query(fn ($query) => $query->where('status', ExpenseStatus::Draft)),
            'submitted' => Tab::make(__('Submitted'))
                ->query(fn ($query) => $query->where('status', ExpenseStatus::Submitted)),
            'approved' => Tab::make(__('Approved'))
                ->query(fn ($query) => $query->where('status', ExpenseStatus::Approved)),
            'rejected' => Tab::make(__('Rejected'))
                ->query(fn ($query) => $query->where('status', ExpenseStatus::Rejected)),
            'reimbursed' => Tab::make(__('Reimbursed'))
                ->query(fn ($query) => $query->where('status', ExpenseStatus::Reimbursed)),
        ];
    }
}