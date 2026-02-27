<?php

namespace App\Filament\Exports\Blog;

use App\Models\Blog\Author;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class AuthorExporter extends Exporter
{
    protected static ?string $model = Author::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label(__('ID')),
            ExportColumn::make('name')
                ->label(__('Name')),
            ExportColumn::make('email')
                ->label(__('Email address')),
            ExportColumn::make('github_handle')
                ->label(__('GitHub handle')),
            ExportColumn::make('twitter_handle')
                ->label(__('Twitter handle')),
            ExportColumn::make('created_at')
                ->label(__('Created at')),
            ExportColumn::make('updated_at')
                ->label(__('Last modified at')),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = __('Your author export has completed and :count :rows exported.', [
            'count' => number_format($export->successful_rows),
            'rows' => str('row')->plural($export->successful_rows),
        ]);

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . __(':count :rows failed to export.', [
                'count' => number_format($failedRowsCount),
                'rows' => str('row')->plural($failedRowsCount),
            ]);
        }

        return $body;
    }
}
