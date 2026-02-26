<?php

namespace App\Filament\Resources\HR\Employees\Schemas;

use App\Enums\EmploymentType;
use App\Models\HR\Employee;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class EmployeeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make(__('Employee'))
                    ->schema([
                        Tab::make(__('Personal'))
                            ->icon(Heroicon::User)
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('Name'))
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('email')
                                    ->label(__('Email'))
                                    ->required()
                                    ->email()
                                    ->maxLength(255)
                                    ->unique(Employee::class, 'email', ignoreRecord: true),

                                TextInput::make('phone')
                                    ->label(__('Phone'))
                                    ->tel()
                                    ->mask('(999) 999-9999')
                                    ->maxLength(255),

                                DatePicker::make('date_of_birth')
                                    ->label(__('Date of birth'))
                                    ->maxDate(now()),

                                ColorPicker::make('team_color')
                                    ->label(__('Team color'))
                                    ->hex(),

                                CheckboxList::make('skills')
                                    ->label(__('Skills'))
                                    ->options([
                                        'PHP' => __('PHP'),
                                        'Laravel' => __('Laravel'),
                                        'JavaScript' => __('JavaScript'),
                                        'TypeScript' => __('TypeScript'),
                                        'React' => __('React'),
                                        'Vue.js' => __('Vue.js'),
                                        'Python' => __('Python'),
                                        'SQL' => __('SQL'),
                                        'Docker' => __('Docker'),
                                        'AWS' => __('AWS'),
                                    ])
                                    ->columns(5)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),

                        Tab::make(__('Employment'))
                            ->icon(Heroicon::Briefcase)
                            ->schema([
                                Select::make('department_id')
                                    ->label(__('Department'))
                                    ->relationship('department', 'name')
                                    ->searchable()
                                    ->preload(),

                                TextInput::make('job_title')
                                    ->label(__('Job title'))
                                    ->required()
                                    ->maxLength(255),

                                ToggleButtons::make('employment_type')
                                    ->label(__('Employment type'))
                                    ->options(EmploymentType::class)
                                    ->inline()
                                    ->required()
                                    ->live()
                                    ->default(EmploymentType::FullTime)
                                    ->columnSpanFull(),

                                DatePicker::make('hire_date')
                                    ->label(__('Hire date'))
                                    ->required()
                                    ->default(now()),

                                TextInput::make('salary')
                                    ->label(__('Salary'))
                                    ->numeric()
                                    ->prefix('$')
                                    ->minValue(0)
                                    ->maxValue(99999999.99)
                                    ->visible(fn (Get $get): bool => in_array($get('employment_type'), [
                                        EmploymentType::FullTime,
                                        EmploymentType::PartTime,
                                        EmploymentType::FullTime->value,
                                        EmploymentType::PartTime->value,
                                    ])),

                                TextInput::make('hourly_rate')
                                    ->label(__('Hourly rate'))
                                    ->numeric()
                                    ->prefix('$')
                                    ->minValue(0)
                                    ->maxValue(999999.99)
                                    ->visible(fn (Get $get): bool => in_array($get('employment_type'), [
                                        EmploymentType::Contractor,
                                        EmploymentType::Intern,
                                        EmploymentType::Contractor->value,
                                        EmploymentType::Intern->value,
                                    ])),

                                Toggle::make('is_active')
                                    ->label(__('Active'))
                                    ->default(true)
                                    ->columnStart(1),
                            ])
                            ->columns(2),

                        Tab::make(__('Documents & Metadata'))
                            ->icon(Heroicon::DocumentText)
                            ->schema([
                                KeyValue::make('metadata')
                                    ->keyLabel(__('Property'))
                                    ->valueLabel(__('Value'))
                                    ->reorderable()
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
