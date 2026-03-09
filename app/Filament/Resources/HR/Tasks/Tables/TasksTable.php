<?php

namespace App\Filament\Resources\HR\Tasks\Tables;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\HR\Task;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ReplicateAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Notifications\Notification;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

class TasksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('Title'))
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Medium)
                    ->limit(40),

                TextColumn::make('project.name')
                    ->label(__('Project'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('assignee.name')
                    ->label(__('Assigned To'))
                    ->searchable()
                    ->sortable()
                    ->placeholder(__('Unassigned')),

                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge(),

                TextColumn::make('priority')
                    ->label(__('Priority'))
                    ->badge(),

                TextColumn::make('estimated_hours')
                    ->label(__('Estimated Hours'))
                    ->numeric(1)
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('due_date')
                    ->label(__('Due date'))
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options(TaskStatus::class),

                SelectFilter::make('priority')
                    ->label(__('Priority'))
                    ->options(TaskPriority::class),

                SelectFilter::make('project')
                    ->label(__('Project'))
                    ->relationship('project', 'name'),

                SelectFilter::make('assignee')
                    ->label(__('Assignee'))
                    ->relationship('assignee', 'name'),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    Action::make('start')
                        ->label(__('Start'))
                        ->icon(Heroicon::Play)
                        ->color('success')
                        ->visible(fn (Task $record): bool => in_array($record->status, [TaskStatus::Backlog, TaskStatus::Todo]))
                        ->action(function (Task $record): void {
                            $record->update(['status' => TaskStatus::InProgress]);

                            Notification::make()
                                ->title(__('Task started'))
                                ->success()
                                ->send();
                        }),
                    Action::make('send_to_review')
                        ->label(__('Send to Review'))
                        ->icon(Heroicon::Eye)
                        ->color('primary')
                        ->visible(fn (Task $record): bool => $record->status === TaskStatus::InProgress)
                        ->action(function (Task $record): void {
                            $record->update(['status' => TaskStatus::InReview]);

                            Notification::make()
                                ->title(__('Task sent to review'))
                                ->success()
                                ->send();
                        }),
                    Action::make('complete')
                        ->label(__('Complete'))
                        ->icon(Heroicon::CheckCircle)
                        ->color('success')
                        ->modalWidth(Width::Medium)
                        ->visible(fn (Task $record): bool => in_array($record->status, [TaskStatus::InProgress, TaskStatus::InReview]))
                        ->fillForm(fn (Task $record): array => [
                            'actual_hours' => $record->actual_hours,
                        ])
                        ->schema([
                            TextInput::make('actual_hours')
                                ->label(__('Actual Hours'))
                                ->numeric()
                                ->step(0.5)
                                ->minValue(0)
                                ->maxValue(99999.9)
                                ->suffix(__('hours')),
                        ])
                        ->action(function (Task $record, array $data): void {
                            $record->update([
                                'status' => TaskStatus::Completed,
                                'completed_at' => now(),
                                'actual_hours' => $data['actual_hours'],
                            ]);

                            Notification::make()
                                ->title(__('Task completed'))
                                ->success()
                                ->send();
                        }),
                    Action::make('assign')
                        ->label(__('Assign'))
                        ->icon(Heroicon::UserPlus)
                        ->modalWidth(Width::Medium)
                        ->schema([
                            Select::make('assigned_to')
                                ->label(__('Assigned To'))
                                ->relationship('assignee', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),
                        ])
                        ->action(fn (Task $record, array $data) => $record->update($data)),
                    Action::make('set_priority')
                        ->label(__('Set Priority'))
                        ->icon(Heroicon::Flag)
                        ->modalWidth(Width::Medium)
                        ->schema([
                            ToggleButtons::make('priority')
                                ->label(__('Priority'))
                                ->options(TaskPriority::class)
                                ->inline()
                                ->required(),
                        ])
                        ->action(fn (Task $record, array $data) => $record->update($data)),
                    ReplicateAction::make()
                        ->requiresConfirmation()
                        ->excludeAttributes(['id', 'completed_at', 'actual_hours', 'sort']),
                    DeleteAction::make()
                        ->action(function (): void {
                            Notification::make()
                                ->title(__('Now, now, don\'t be cheeky, leave some records for others to play with!'))
                                ->warning()
                                ->send();
                        }),
                ]),
            ])
            ->groupedBulkActions([
                BulkAction::make('set_status')
                    ->label(__('Set Status'))
                    ->icon(Heroicon::ArrowPathRoundedSquare)
                    ->color('primary')
                    ->schema([
                        ToggleButtons::make('status')
                            ->label(__('Status'))
                            ->options(TaskStatus::class)
                            ->inline()
                            ->required(),
                    ])
                    ->action(function (Collection $records, array $data): void {
                        $records->each(fn (Task $record) => $record->update($data));

                        Notification::make()
                            ->title(__('Updated :count tasks to :status', ['count' => $records->count(), 'status' => $data['status']->getLabel()]))
                            ->success()
                            ->send();
                    })
                    ->deselectRecordsAfterCompletion(),
                BulkAction::make('assign')
                    ->label(__('Assign'))
                    ->icon(Heroicon::UserPlus)
                    ->color('info')
                    ->schema([
                        Select::make('assigned_to')
                            ->label(__('Assigned To'))
                            ->relationship('assignee', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])
                    ->action(function (Collection $records, array $data): void {
                        $records->each(fn (Task $record) => $record->update($data));
                    })
                    ->deselectRecordsAfterCompletion(),
                DeleteBulkAction::make()
                    ->action(function (): void {
                        Notification::make()
                            ->title(__('Now, now, don\'t be cheeky, leave some records for others to play with!'))
                            ->warning()
                            ->send();
                    }),
            ])
            ->recordClasses(fn (Task $record) => match (true) {
                $record->status === TaskStatus::Completed => 'opacity-60',
                $record->due_date !== null && $record->due_date->isPast() => 'bg-danger-50 dark:bg-danger-950/50',
                default => null,
            });
    }
}