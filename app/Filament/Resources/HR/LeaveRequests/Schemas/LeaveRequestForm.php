<?php

namespace App\Filament\Resources\HR\LeaveRequests\Schemas;

use App\Enums\LeaveStatus;
use App\Enums\LeaveType;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\ToggleButtons;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Carbon;

class LeaveRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Leave Details'))
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        Select::make('employee_id')
                            ->label(__('Employee'))
                            ->relationship('employee', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        ToggleButtons::make('type')
                            ->label(__('Type'))
                            ->options(LeaveType::class)
                            ->inline()
                            ->required()
                            ->columnSpanFull(),

                        DatePicker::make('start_date')
                            ->label(__('Start date'))
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set): void {
                                static::calculateDays($get, $set);
                            }),

                        DatePicker::make('end_date')
                            ->label(__('End date'))
                            ->required()
                            ->minDate(fn (Get $get) => $get('start_date'))
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set): void {
                                static::calculateDays($get, $set);
                            }),

                        TimePicker::make('start_time')
                            ->label(__('Start time (half days)'))
                            ->seconds(false),

                        TimePicker::make('end_time')
                            ->label(__('End time (half days)'))
                            ->seconds(false),

                        TextInput::make('days_requested')
                            ->label(__('Days requested'))
                            ->numeric()
                            ->step(0.5)
                            ->minValue(0)
                            ->maxValue(999.9)
                            ->disabled()
                            ->dehydrated()
                            ->required(),

                        Textarea::make('reason')
                            ->label(__('Reason'))
                            ->required()
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ]),

                Section::make(__('Review'))
                    ->hiddenOn('create')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('notice')
                            ->state(__('Note: Approving this request will deduct days from the employee\'s leave balance.'))
                            ->columnSpanFull(),

                        ToggleButtons::make('status')
                            ->label(__('Status'))
                            ->options(LeaveStatus::class)
                            ->inline()
                            ->required()
                            ->columnSpanFull(),

                        Select::make('approver_id')
                            ->label(__('Approver'))
                            ->relationship('approver', 'name')
                            ->searchable(),

                        Textarea::make('reviewer_notes')
                            ->label(__('Reviewer notes'))
                            ->maxLength(65535),
                    ]),
            ]);
    }

    protected static function calculateDays(Get $get, Set $set): void
    {
        $startDate = $get('start_date');
        $endDate = $get('end_date');

        if ($startDate && $endDate) {
            $days = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;
            $set('days_requested', max(0.5, $days));
        }
    }
}