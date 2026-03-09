<?php

namespace App\Filament\Resources\HR\Expenses\Schemas;

use App\Enums\ExpenseCategory;
use App\Enums\ExpenseStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class ExpenseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Details'))
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('expense_number')
                        ->label(__('Expense Number'))
                            ->default(fn () => 'EXP-' . str_pad((string) random_int(1, 999999), 6, '0', STR_PAD_LEFT))
                            ->disabled()
                            ->dehydrated()
                            ->required()
                            ->maxLength(255),

                        Select::make('employee_id')
                            ->relationship('employee', 'name')
                            ->searchable()
                            ->preload()
                            ->label(__('Employee'))
                            ->required(),

                        Select::make('project_id')
                            ->relationship('project', 'name')
                            ->searchable()
                            ->preload()
                            ->label(__('Project')),

                        ToggleButtons::make('category')
                            ->options(ExpenseCategory::class)
                            ->inline()
                            ->label(__('Category'))
                            ->required()
                            ->columnSpanFull(),

                        ToggleButtons::make('status')
                            ->options(ExpenseStatus::class)
                            ->inline()
                            ->label(__('Status'))
                            ->required()
                            ->default(ExpenseStatus::Draft)
                            ->columnSpanFull(),

                        Textarea::make('description')
                            ->label(__('Description'))
                            ->required()
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ]),

                Section::make(__('Line Items'))
                    ->columnSpanFull()
                    ->schema([
                        Repeater::make('expenseLines')
                            ->hiddenLabel()
                            ->relationship()
                            ->defaultItems(1)
                            ->table([
                                TableColumn::make(__('Description')),
                                TableColumn::make(__('Quantity'))
                                    ->width(100),
                                TableColumn::make(__('Unit Price'))
                                    ->width(120),
                                TableColumn::make(__('Amount'))
                                    ->width(120),
                                TableColumn::make(__('Date'))
                                    ->width(150),
                            ])
                            ->schema([
                                TextInput::make('description')
                                    ->label(__('Description'))
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('quantity')
                                    ->label(__('Quantity'))
                                    ->integer()
                                    ->minValue(1)
                                    ->maxValue(2147483647)
                                    ->default(1)
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Get $get, Set $set): void {
                                        $quantity = (int) ($get('quantity') ?? 1);
                                        $unitPrice = (float) ($get('unit_price') ?? 0);
                                        $set('amount', number_format($quantity * $unitPrice, 2, '.', ''));
                                    }),

                                TextInput::make('unit_price')
                                    ->label(__('Unit Price'))
                                    ->numeric()
                                    ->prefix('$')
                                    ->minValue(0)
                                    ->maxValue(99999999.99)
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Get $get, Set $set): void {
                                        $quantity = (int) ($get('quantity') ?? 1);
                                        $unitPrice = (float) ($get('unit_price') ?? 0);
                                        $set('amount', number_format($quantity * $unitPrice, 2, '.', ''));
                                    }),

                                TextInput::make('amount')
                                    ->label(__('Amount'))
                                    ->numeric()
                                    ->prefix('$')
                                    ->minValue(0)
                                    ->maxValue(99999999.99)
                                    ->disabled()
                                    ->dehydrated(),

                                DatePicker::make('date')
                                    ->label(__('Date'))
                                    ->required()
                                    ->default(now()),
                            ])
                            ->columnSpanFull(),
                    ]),

                Section::make(__('Summary'))
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('total_amount')
                            ->label(__('Total Amount'))
                            ->numeric()
                            ->prefix('$')
                            ->minValue(0)
                            ->maxValue(99999999.99)
                            ->disabled()
                            ->dehydrated(),

                        Select::make('currency')
                            ->label(__('Currency'))
                            ->required()
                            ->options([
                                'USD' => 'USD',
                                'EUR' => 'EUR',
                                'GBP' => 'GBP',
                                'CAD' => 'CAD',
                            ])
                            ->default('USD'),

                        FileUpload::make('receipt_path')
                            ->label(__('Receipt'))
                            ->directory('expense-receipts'),

                        Textarea::make('notes')
                            ->label(__('Notes'))
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}