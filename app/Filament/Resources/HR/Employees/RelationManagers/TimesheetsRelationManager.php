<?php

namespace App\Filament\Resources\HR\Employees\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\IconColumn;
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
                TextColumn::make('project.name')
                    ->label(__('Project'))
                    ->sortable()
                    ->weight(FontWeight::Medium),

                TextColumn::make('date')
                    ->label(__('Date'))
                    ->date()
                    ->sortable(),

                TextColumn::make('hours')
                    ->label(__('Hours'))
                    ->numeric(1)
                    ->sortable(),

                IconColumn::make('is_billable')
                    ->label(__('Billable'))
                    ->boolean(),

                TextColumn::make('total_cost')
                    ->label(__('Total cost'))
                    ->money('usd')
                    ->sortable(),
            ])
            ->defaultSort('date', 'desc');
    }
}