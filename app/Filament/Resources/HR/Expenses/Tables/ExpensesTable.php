<?php

namespace App\Filament\Resources\HR\Expenses\Tables;

use App\Enums\ExpenseStatus;
use App\Models\HR\Expense;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Validation\ValidationException;

class ExpensesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('expense_number')
                    ->label(__('Expense Number'))
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Medium),

                TextColumn::make('employee.name')
                    ->label(__('Employee'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('category')
                    ->label(__('Category'))
                    ->badge(),

                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge(),

                TextColumn::make('total_amount')
                    ->label(__('Total Amount'))
                    ->money('usd')
                    ->sortable()
                    ->summarize(Sum::make()->money('usd')),

                TextColumn::make('project.name')
                    ->label(__('Project'))
                    ->sortable()
                    ->toggleable()
                    ->placeholder(__('No project')),

                TextColumn::make('submitted_at')
                    ->label(__('Submitted At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s')
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    Action::make('approve')
                        ->label(__('Approve'))
                        ->icon(Heroicon::Check)
                        ->color('success')
                        ->visible(fn (Expense $record): bool => $record->status === ExpenseStatus::Submitted)
                        ->requiresConfirmation()
                        ->action(function (Expense $record): void {
                            $record->update([
                                'status' => ExpenseStatus::Approved,
                                'approved_at' => now(),
                            ]);

                            Notification::make()
                                ->title(__('Expense approved'))
                                ->success()
                                ->send();
                        }),
                    Action::make('reject')
                        ->icon(Heroicon::XMark)
                        ->color('danger')
                        ->visible(fn (Expense $record): bool => $record->status === ExpenseStatus::Submitted)
                        ->modalWidth(Width::Medium)
                        ->label(__('Reject'))
                        ->schema([
                            Textarea::make('rejection_reason')
                                ->required()
                                ->label(__('Reason for rejection')),
                        ])
                        ->action(function (Expense $record, array $data): void {
                            $record->update([
                                'status' => ExpenseStatus::Rejected,
                                'notes' => $data['rejection_reason'],
                            ]);

                            Notification::make()
                                ->title(__('Expense rejected'))
                                ->danger()
                                ->send();
                        }),
                    Action::make('submit')
                        ->label(__('Submit'))
                        ->icon(Heroicon::PaperAirplane)
                        ->color('info')
                        ->visible(fn (Expense $record): bool => $record->status === ExpenseStatus::Draft)
                        ->requiresConfirmation()
                        ->before(function (Expense $record): void {
                            if ($record->total_amount <= 0) {
                                Notification::make()
                                    ->title(__('Cannot submit an expense with no amount'))
                                    ->danger()
                                    ->send();

                                throw ValidationException::withMessages([]);
                            }
                        })
                        ->action(function (Expense $record): void {
                            $record->update([
                                'status' => ExpenseStatus::Submitted,
                                'submitted_at' => now(),
                            ]);
                        })
                        ->after(function (Expense $record): void {
                            Notification::make()
                                ->title(__('Expense :number submitted for approval', ['number' => $record->expense_number]))
                                ->success()
                                ->send();
                        }),
                    Action::make('reimburse')
                        ->icon(Heroicon::Banknotes)
                        ->color('primary')
                        ->visible(fn (Expense $record): bool => $record->status === ExpenseStatus::Approved)
                        ->requiresConfirmation()
                        ->action(function (Expense $record): void {
                            $record->update(['status' => ExpenseStatus::Reimbursed]);

                            Notification::make()
                                ->title(__('Expense reimbursed'))
                                ->success()
                                ->send();
                        }),
                    Action::make('flag')
                        ->icon(Heroicon::Flag)
                        ->color('warning')
                        ->modalWidth(Width::Medium)
                        ->label(__('Flag'))
                        ->schema([
                            Textarea::make('flag_reason')
                                ->required()
                                ->label(__('Reason for flagging')),
                        ])
                        ->action(function (Expense $record, array $data): void {
                            $record->update([
                                'status' => ExpenseStatus::Draft,
                                'notes' => $data['flag_reason'],
                            ]);

                            Notification::make()
                                ->title(__('Expense flagged and returned to draft'))
                                ->warning()
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
