<?php

namespace App\Filament\Resources\HR\Timesheets\Tables;

use App\Models\HR\Timesheet;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\Summarizers\Average;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Columns\Summarizers\Range;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class TimesheetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee.name')
                    ->label(__('Employee'))
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Medium)
                    ->summarize(Count::make()),

                TextColumn::make('project.name')
                    ->label(__('Project'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('task.title')
                    ->label(__('Task'))
                    ->searchable()
                    ->limit(30)
                    ->toggleable()
                    ->placeholder(__('No task')),

                TextColumn::make('date')
                    ->label(__('Date'))
                    ->date()
                    ->sortable()
                    ->summarize(Range::make()->minimalDateTimeDifference()),

                TextColumn::make('hours')
                    ->label(__('Hours'))
                    ->numeric(1)
                    ->sortable()
                    ->summarize([
                        Sum::make()->label(__('Total')),
                        Average::make()->label(__('Avg')),
                    ]),

                IconColumn::make('is_billable')
                    ->label(__('Billable'))
                    ->boolean()
                    ->toggleable(),

                TextColumn::make('hourly_rate')
                    ->label(__('Hourly Rate'))
                    ->money('usd')
                    ->sortable()
                    ->toggleable()
                    ->copyable(),

                TextColumn::make('total_cost')
                    ->label(__('Total cost'))
                    ->money('usd')
                    ->sortable()
                    ->summarize(Sum::make()->money('usd')),
            ])
            ->defaultSort('date', 'desc')
            ->groups([
                Group::make('employee.name')
                    ->label(__('Employee'))
                    ->collapsible(),
                Group::make('project.name')
                    ->label(__('Project'))
                    ->collapsible(),
                Group::make('date')
                    ->label(__('Date'))
                    ->date()
                    ->collapsible(),
            ])
            ->filters([
                SelectFilter::make('employee')
                    ->label(__('Employee'))
                    ->relationship('employee', 'name'),

                SelectFilter::make('project')
                    ->label(__('Project'))
                    ->relationship('project', 'name'),

                TernaryFilter::make('is_billable')
                    ->label(__('Billable')),

                Filter::make('date_range')
                    ->label(__('Date Range'))
                    ->schema([
                        DatePicker::make('from')
                            ->label(__('From')),
                        DatePicker::make('until')
                            ->label(__('Until')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['from'] ?? null) {
                            $indicators['from'] = __('From') . ' ' . Carbon::parse($data['from'])->toFormattedDateString();
                        }
                        if ($data['until'] ?? null) {
                            $indicators['until'] = __('Until') . ' ' . Carbon::parse($data['until'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
            ])
            ->recordActions([
                Action::make('toggle_billable')
                    ->iconButton()
                    ->icon(fn (Timesheet $record): Heroicon => $record->is_billable ? Heroicon::CurrencyDollar : Heroicon::NoSymbol)
                    ->color(fn (Timesheet $record): string => $record->is_billable ? 'success' : 'gray')
                    ->disabled(fn (Timesheet $record): bool => $record->date->isBefore(now()->subDays(7)))
                    ->action(fn (Timesheet $record) => $record->update(['is_billable' => ! $record->is_billable])),
                EditAction::make(),
            ])
            ->groupedBulkActions([
                BulkAction::make('mark_billable')
                    ->label(__('Mark Billable'))
                    ->icon(Heroicon::CurrencyDollar)
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Collection $records): void {
                        $records->each(fn (Timesheet $record) => $record->update(['is_billable' => true]));
                    })
                    ->deselectRecordsAfterCompletion(),
                BulkAction::make('mark_non_billable')
                    ->label(__('Mark Non-Billable'))
                    ->icon(Heroicon::NoSymbol)
                    ->color('gray')
                    ->requiresConfirmation()
                    ->action(function (Collection $records): void {
                        $records->each(fn (Timesheet $record) => $record->update(['is_billable' => false]));
                    })
                    ->deselectRecordsAfterCompletion(),
                DeleteBulkAction::make()
                    ->action(function (): void {
                        Notification::make()
                            ->title(__('Now, now, don\'t be cheeky, leave some records for others to play with!'))
                            ->warning()
                            ->send();
                    }),
            ]);
    }
}