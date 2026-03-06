<?php

namespace App\Filament\Resources\HR\LeaveRequests\Pages;

use App\Enums\LeaveStatus;
use App\Filament\Resources\HR\LeaveRequests\LeaveRequestResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;

class ListLeaveRequests extends ListRecords
{
    protected static string $resource = LeaveRequestResource::class;

    protected function getActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('All')),
            'pending' => Tab::make(__('Pending'))
                ->query(fn ($query) => $query->where('status', LeaveStatus::Pending)),
            'approved' => Tab::make(__('Approved'))
                ->query(fn ($query) => $query->where('status', LeaveStatus::Approved)),
            'rejected' => Tab::make(__('Rejected'))
                ->query(fn ($query) => $query->where('status', LeaveStatus::Rejected)),
            'taken' => Tab::make(__('Taken'))
                ->query(fn ($query) => $query->where('status', LeaveStatus::Taken)),
        ];
    }
}
