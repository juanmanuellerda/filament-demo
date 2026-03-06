<?php

namespace App\Filament\Resources\Shop\Orders\Pages;

use App\Filament\Resources\Shop\Orders\OrderResource;
use Filament\Actions\CreateAction;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;

class ListOrders extends ListRecords
{
    use ExposesTableToWidgets;

    protected static string $resource = OrderResource::class;

    protected function getActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return OrderResource::getWidgets();
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All')->label(__('All')),
            'new' => Tab::make()->label(__('New'))->query(fn ($query) => $query->where('status', 'new')),
            'processing' => Tab::make()->label(__('Processing'))->query(fn ($query) => $query->where('status', 'processing')),
            'shipped' => Tab::make()->label(__('Shipped'))->query(fn ($query) => $query->where('status', 'shipped')),
            'delivered' => Tab::make()->label(__('Delivered'))->query(fn ($query) => $query->where('status', 'delivered')),
            'cancelled' => Tab::make()->label(__('Cancelled'))->query(fn ($query) => $query->where('status', 'cancelled')),
        ];
    }
}
