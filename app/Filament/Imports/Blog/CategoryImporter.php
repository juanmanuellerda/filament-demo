<?php

namespace App\Filament\Imports\Blog;

use App\Models\Blog\PostCategory;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class CategoryImporter extends Importer
{
    protected static ?string $model = PostCategory::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->label(__('Name'))
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->example('Category A'),
            ImportColumn::make('slug')
                ->label(__('Slug'))
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->example('category-a'),
            ImportColumn::make('description')
                ->label(__('Description'))
                ->example('This is the description for Category A.'),
            ImportColumn::make('is_visible')
                ->label(__('Visibility'))
                ->requiredMapping()
                ->boolean()
                ->rules(['required', 'boolean'])
                ->example('yes'),
            ImportColumn::make('seo_title')
                ->label(__('SEO title'))
                ->rules(['max:60'])
                ->example('Awesome Category A'),
            ImportColumn::make('seo_description')
                ->label(__('SEO description'))
                ->rules(['max:160'])
                ->example('Wow! It\'s just so amazing.'),
        ];
    }

    public function resolveRecord(): ?PostCategory
    {
        return PostCategory::firstOrNew([
            'slug' => $this->data['slug'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = __('Your blog category import has completed and :count :rows imported.', [
            'count' => number_format($import->successful_rows),
            'rows' => str('row')->plural($import->successful_rows),
        ]);

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . __(':count :rows failed to import.', [
                'count' => number_format($failedRowsCount),
                'rows' => str('row')->plural($failedRowsCount),
            ]);
        }

        return $body;
    }
}
