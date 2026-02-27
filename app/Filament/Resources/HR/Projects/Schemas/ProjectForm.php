<?php

namespace App\Filament\Resources\HR\Projects\Schemas;

use App\Enums\ProjectStatus;
use App\Enums\TaskPriority;
use App\Models\HR\Employee;
use App\Models\HR\Project;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make(__('Project'))
                    ->schema([
                        Tab::make(__('Overview'))
                            ->icon(Heroicon::InformationCircle)
                            ->columns(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('Name'))
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (string $operation, $state, Set $set): void {
                                        if ($operation !== 'create') {
                                            return;
                                        }

                                        $set('slug', Str::slug($state));
                                    }),

                                TextInput::make('slug')
                                    ->label(__('Slug'))
                                    ->disabled()
                                    ->dehydrated()
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(Project::class, 'slug', ignoreRecord: true),

                                RichEditor::make('description')
                                    ->label(__('Description'))
                                    ->columnSpanFull(),

                                Select::make('department_id')
                                    ->label(__('Department'))
                                    ->relationship('department', 'name')
                                    ->searchable()
                                    ->preload(),

                                ToggleButtons::make('status')
                                    ->label(__('Status'))
                                    ->options(ProjectStatus::class)
                                    ->inline()
                                    ->required()
                                    ->default(ProjectStatus::Planning),

                                ToggleButtons::make('priority')
                                    ->label(__('Priority'))
                                    ->options(TaskPriority::class)
                                    ->inline()
                                    ->required()
                                    ->default(TaskPriority::Medium),

                                ColorPicker::make('color')
                                    ->label(__('Color')),

                                DatePicker::make('start_date')
                                    ->label(__('Start Date'))
                                    ->required(),

                                DatePicker::make('end_date')
                                    ->label(__('End Date'))
                                    ->minDate(fn (Get $get) => $get('start_date')),
                            ]),

                        Tab::make(__('Plan'))
                            ->icon(Heroicon::ClipboardDocumentList)
                            ->schema([
                                Builder::make('plan')
                                    ->hiddenLabel()
                                    ->blocks([
                                        Block::make('milestone')
                                            ->label(__('Milestone'))
                                            ->icon(Heroicon::Flag)
                                            ->schema([
                                                TextInput::make('title')
                                                    ->label(__('Title'))
                                                    ->required(),
                                                DatePicker::make('target_date')
                                                    ->label(__('Target Date'))
                                                    ->required(),
                                                Textarea::make('description')
                                                    ->label(__('Description'))
                                                    ->rows(2),
                                            ]),

                                        Block::make('task_group')
                                            ->label(__('Task Group'))
                                            ->icon(Heroicon::ListBullet)
                                            ->schema([
                                                TextInput::make('title')
                                                    ->label(__('Title'))
                                                    ->required(),
                                                Select::make('assignee')
                                                    ->label(__('Assignee'))
                                                    ->options(fn () => Employee::pluck('name', 'id')),
                                                TagsInput::make('tasks')
                                                    ->placeholder(__('Add tasks')),
                                            ]),

                                        Block::make('checkpoint')
                                            ->label(__('Checkpoint'))
                                            ->icon(Heroicon::CheckCircle)
                                            ->schema([
                                                TextInput::make('title')
                                                    ->label(__('Title'))
                                                    ->required(),
                                                DatePicker::make('date')
                                                    ->label(__('Date'))
                                                    ->required(),
                                                Select::make('status')
                                                    ->label(__('Status'))
                                                    ->options([
                                                        'pending' => __('Pending'),
                                                        'passed' => __('Passed'),
                                                        'failed' => __('Failed'),
                                                    ]),
                                            ]),
                                    ])
                                    ->columnSpanFull(),
                            ]),

                        Tab::make(__('Budget'))
                            ->icon(Heroicon::CurrencyDollar)
                            ->columns(2)
                            ->schema([
                                TextInput::make('budget')
                                    ->label(__('Budget'))
                                    ->required()
                                    ->numeric()
                                    ->prefix('$')
                                    ->minValue(0)
                                    ->maxValue(9999999999.99)
                                    ->default(0),

                                TextInput::make('spent')
                                    ->label(__('Spent'))
                                    ->numeric()
                                    ->prefix('$')
                                    ->minValue(0)
                                    ->maxValue(9999999999.99)
                                    ->disabled()
                                    ->dehydrated()
                                    ->required()
                                    ->default(0),

                                TextInput::make('estimated_hours')
                                    ->label(__('Estimated Hours'))
                                    ->numeric()
                                    ->suffix(' ' . __('hours'))
                                    ->minValue(0)
                                    ->maxValue(9999999.9)
                                    ->required()
                                    ->default(0),

                                TextInput::make('actual_hours')
                                    ->label(__('Actual Hours'))
                                    ->numeric()
                                    ->suffix(' ' . __('hours'))
                                    ->minValue(0)
                                    ->maxValue(9999999.9)
                                    ->disabled()
                                    ->dehydrated()
                                    ->required()
                                    ->default(0),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
