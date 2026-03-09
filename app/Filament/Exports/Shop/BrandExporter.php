<?php

namespace App\Filament\Exports\Shop;

use App\Models\Shop\Brand;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class BrandExporter extends Exporter
{
    protected static ?string $model = Brand::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id'),
            ExportColumn::make('name')
                ->label(__('Name')),
            ExportColumn::make('slug')
                ->label(__('Slug')),
            ExportColumn::make('website')
                ->label(__('Website')),
            ExportColumn::make('created_at')
                ->label(__('Created at')),
            ExportColumn::make('updated_at')
                ->label(__('Last modified at')),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = __('Your brand export has completed and') . ' ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' ' . __('exported') . '.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' ' . __('failed to export') . '.';
        }

        return $body;
    }
}
