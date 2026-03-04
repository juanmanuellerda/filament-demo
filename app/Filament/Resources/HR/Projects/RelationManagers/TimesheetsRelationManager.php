<?php

namespace App\Filament\Resources\HR\Projects\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class TimesheetsRelationManager extends RelationManager
{
    protected static string $relationship = 'timesheets';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Timesheets');
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee.name')
                    ->label(__('Employee'))
                    ->sortable()
                    ->weight(FontWeight::Medium),

                TextColumn::make('date')
                    ->label(__('Date'))
                    ->date()
                    ->sortable(),

                TextColumn::make('hours')
                    ->label(__('Hours'))
                    ->numeric(1)
                    ->sortable()
                    ->summarize(Sum::make()->label(__('Total hours'))),

                IconColumn::make('is_billable')
                    ->label(__('Billable'))
                    ->boolean(),

                TextColumn::make('total_cost')
                    ->label(__('Total cost'))
                    ->money('usd')
                    ->sortable()
                    ->summarize(Sum::make()->money('usd')->label(__('Total cost'))),
            ])
            ->defaultSort('date', 'desc');
    }
}
