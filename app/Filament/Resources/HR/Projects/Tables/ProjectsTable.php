<?php

namespace App\Filament\Resources\HR\Projects\Tables;

use App\Enums\ProjectStatus;
use App\Enums\TaskPriority;
use App\Models\HR\Project;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\ToggleButtons;
use Filament\Notifications\Notification;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Medium),

                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge(),

                TextColumn::make('priority')
                    ->label(__('Priority'))
                    ->badge(),

                TextColumn::make('department.name')
                    ->label(__('Department'))
                    ->sortable()
                    ->toggleable()
                    ->placeholder(__('No department')),

                TextColumn::make('budget')
                    ->label(__('Budget'))
                    ->money('usd')
                    ->sortable()
                    ->summarize(Sum::make()->money('usd')),

                TextColumn::make('spent')
                    ->label(__('Spent'))
                    ->money('usd')
                    ->sortable()
                    ->summarize(Sum::make()->money('usd')),

                TextColumn::make('progress')
                    ->label(__('Progress'))
                    ->state(fn (Project $record): string => $record->estimated_hours > 0
                        ? number_format(($record->actual_hours / $record->estimated_hours) * 100, 0) . '%'
                        : '0%'),

                TextColumn::make('start_date')
                    ->label(__('Start Date'))
                    ->date()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('end_date')
                    ->label(__('End Date'))
                    ->date()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options(ProjectStatus::class),

                SelectFilter::make('priority')
                    ->label(__('Priority'))
                    ->options(TaskPriority::class),

                SelectFilter::make('department')
                    ->label(__('Department'))
                    ->relationship('department', 'name'),

                TrashedFilter::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    Action::make('change_status')
                        ->label(__('Change Status'))
                        ->icon(Heroicon::ArrowPathRoundedSquare)
                        ->color('primary')
                        ->modalWidth(Width::Medium)
                        ->stickyModalFooter()
                        ->fillForm(fn (Project $record): array => [
                            'status' => $record->status,
                        ])
                        ->schema([
                            ToggleButtons::make('status')
                                ->label(__('Status'))
                                ->options(ProjectStatus::class)
                                ->inline()
                                ->required(),
                        ])
                        ->action(fn (Project $record, array $data) => $record->update($data)),
                    Action::make('put_on_hold')
                        ->label(__('Put On Hold'))
                        ->icon(Heroicon::Pause)
                        ->color('warning')
                        ->visible(fn (Project $record): bool => $record->status === ProjectStatus::Active)
                        ->requiresConfirmation()
                        ->modalHeading(__('Put Project On Hold'))
                        ->modalDescription(__('This will pause all work on this project.'))
                        ->modalIcon(Heroicon::ExclamationTriangle)
                        ->modalIconColor('warning')
                        ->action(function (Project $record): void {
                            $record->update(['status' => ProjectStatus::OnHold]);

                            Notification::make()
                                ->title(__('Project put on hold'))
                                ->warning()
                                ->send();
                        }),
                    Action::make('resume')
                        ->label(__('Resume'))
                        ->icon(Heroicon::Play)
                        ->color('success')
                        ->visible(fn (Project $record): bool => $record->status === ProjectStatus::OnHold)
                        ->action(function (Project $record): void {
                            $record->update(['status' => ProjectStatus::Active]);

                            Notification::make()
                                ->title(__('Project resumed'))
                                ->success()
                                ->send();
                        }),
                    Action::make('complete')
                        ->label(__('Complete'))
                        ->icon(Heroicon::CheckCircle)
                        ->color('success')
                        ->visible(fn (Project $record): bool => in_array($record->status, [ProjectStatus::Active, ProjectStatus::OnHold]))
                        ->requiresConfirmation()
                        ->action(function (Project $record): void {
                            $record->update([
                                'status' => ProjectStatus::Completed,
                                'end_date' => now(),
                            ]);

                            Notification::make()
                                ->title(__('Project completed'))
                                ->success()
                                ->send();
                        }),
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
