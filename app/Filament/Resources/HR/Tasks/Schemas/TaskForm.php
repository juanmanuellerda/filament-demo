<?php

namespace App\Filament\Resources\HR\Tasks\Schemas;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class TaskForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Task Details'))
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('title')
                            ->label(__('Title'))
                            ->required()
                            ->maxLength(255),

                        Select::make('project_id')
                            ->label(__('Project'))
                            ->relationship('project', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('assigned_to')
                            ->label(__('Assigned To'))
                            ->relationship('assignee', 'name')
                            ->searchable()
                            ->preload(),

                        ToggleButtons::make('status')
                            ->label(__('Status'))
                            ->options(TaskStatus::class)
                            ->inline()
                            ->required()
                            ->live()
                            ->default(TaskStatus::Backlog)
                            ->columnSpanFull(),

                        Radio::make('priority')
                            ->label(__('Priority'))
                            ->options(TaskPriority::class)
                            ->inline()
                            ->required()
                            ->default(TaskPriority::Medium),

                        TextInput::make('estimated_hours')
                            ->label(__('Estimated Hours'))
                            ->numeric()
                            ->step(0.5)
                            ->minValue(0)
                            ->maxValue(99999.9)
                            ->suffix(__('hours')),

                        DatePicker::make('due_date')
                            ->label(__('Due date')),

                        RichEditor::make('description')
                            ->label(__('Description'))
                            ->columnSpanFull(),

                        CheckboxList::make('labels')
                            ->label(__('Labels'))
                            ->options([
                                'bug' => __('Bug'),
                                'feature' => __('Feature'),
                                'enhancement' => __('Enhancement'),
                                'documentation' => __('Documentation'),
                                'design' => __('Design'),
                                'testing' => __('Testing'),
                                'refactor' => __('Refactor'),
                                'urgent' => __('Urgent'),
                            ])
                            ->columns(4)
                            ->columnSpanFull(),

                        DateTimePicker::make('completed_at')
                            ->label(__('Completed At'))
                            ->visible(fn (Get $get): bool => in_array($get('status'), [
                                TaskStatus::Completed,
                                TaskStatus::Completed->value,
                            ])),
                    ]),
            ]);
    }
}
