<?php

namespace App\Filament\Resources\HR\Employees\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LeaveRequestsRelationManager extends RelationManager
{
    protected static string $relationship = 'leaveRequests';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')
                    ->label(__('Type'))
                    ->badge()
                    ->weight(FontWeight::Medium),

                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge(),

                TextColumn::make('start_date')
                    ->label(__('Start date'))
                    ->date()
                    ->sortable(),

                TextColumn::make('end_date')
                    ->label(__('End date'))
                    ->date()
                    ->sortable(),

                TextColumn::make('days_requested')
                    ->label(__('Days requested'))
                    ->numeric(1)
                    ->sortable(),            ])
            ->defaultSort('start_date', 'desc');
    }
}
