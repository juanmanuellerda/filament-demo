<?php

namespace App\Filament\Resources\HR\Expenses\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ExpenseInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Expense Details'))
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('expense_number')
                            ->label(__('Expense Number')),
                        TextEntry::make('employee.name')
                            ->label(__('Employee')),
                        TextEntry::make('category')
                            ->badge()
                            ->label(__('Category')),
                        TextEntry::make('status')
                            ->badge()
                            ->label(__('Status')),
                        TextEntry::make('total_amount')
                            ->money('usd')
                            ->label(__('Total Amount')),
                        TextEntry::make('currency')
                            ->label(__('Currency')),
                        TextEntry::make('project.name')
                            ->placeholder(__('No project'))
                            ->label(__('Project')),
                        TextEntry::make('submitted_at')
                            ->dateTime()
                            ->placeholder(__('Not submitted'))
                            ->label(__('Submitted At')),
                        TextEntry::make('approved_at')
                            ->dateTime()
                            ->placeholder(__('Not approved'))
                            ->label(__('Approved At')),
                        TextEntry::make('description')
                            ->columnSpanFull(),
                        TextEntry::make('notes')
                            ->label(__('Notes'))
                            ->placeholder(__('No notes'))
                            ->columnSpanFull(),
                    ]),

                Section::make(__('Line Items'))
                    ->columnSpanFull()
                    ->schema([
                        RepeatableEntry::make('expenseLines')
                            ->hiddenLabel()
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
                                TextEntry::make(__('Description')),
                                TextEntry::make('quantity'),
                                TextEntry::make('unit_price')
                                    ->money('usd'),
                                TextEntry::make('amount')
                                    ->money('usd'),
                                TextEntry::make('date')
                                    ->date(),
                            ]),
                    ]),
            ]);
    }
}