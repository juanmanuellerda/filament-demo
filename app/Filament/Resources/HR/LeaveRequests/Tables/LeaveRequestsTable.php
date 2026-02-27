<?php

namespace App\Filament\Resources\HR\LeaveRequests\Tables;

use App\Enums\LeaveStatus;
use App\Models\HR\LeaveRequest;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

class LeaveRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee.name')
                    ->label(__('Employee'))
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Medium),

                TextColumn::make('type')
                    ->label(__('Type'))
                    ->badge(),

                SelectColumn::make('status')
                    ->label(__('Status'))
                    ->options(LeaveStatus::class)
                    ->selectablePlaceholder(false)
                    ->rules(['required']),

                TextColumn::make('start_date')
                    ->label(__('Start date'))
                    ->date()
                    ->sortable(),

                TextColumn::make('end_date')
                    ->label(__('End date'))
                    ->date()
                    ->sortable(),

                TextColumn::make('days_requested')
                    ->label(__('Days requested'))
                    ->numeric(1)
                    ->sortable()
                    ->summarize(Sum::make()),

                TextColumn::make('approver.name')
                    ->label(__('Approver'))
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->placeholder(__('Not assigned')),
            ])
            ->defaultSort('start_date', 'desc')
            ->recordActions([
                ActionGroup::make([
                    Action::make('approve')
                        ->label(__('Approve'))
                        ->icon(Heroicon::Check)
                        ->color('success')
                        ->visible(fn (LeaveRequest $record): bool => $record->status === LeaveStatus::Pending)
                        ->requiresConfirmation()
                        ->action(function (LeaveRequest $record): void {
                            $record->update([
                                'status' => LeaveStatus::Approved,
                                'reviewed_at' => now(),
                            ]);

                            Notification::make()
                                ->title(__('Leave request approved'))
                                ->success()
                                ->send();
                        }),
                    Action::make('reject')
                        ->label(__('Reject'))
                        ->icon(Heroicon::XMark)
                        ->color('danger')
                        ->visible(fn (LeaveRequest $record): bool => $record->status === LeaveStatus::Pending)
                        ->modalWidth(Width::Medium)
                        ->modalSubmitActionLabel(__('Reject'))
                        ->schema([
                            Textarea::make('reviewer_notes')
                                ->label(__('Reason for rejection'))
                                ->required(),
                        ])
                        ->action(function (LeaveRequest $record, array $data): void {
                            $record->update([
                                'status' => LeaveStatus::Rejected,
                                'reviewer_notes' => $data['reviewer_notes'],
                                'reviewed_at' => now(),
                            ]);

                            Notification::make()
                                ->title(__('Leave request rejected'))
                                ->danger()
                                ->send();
                        }),
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make()
                        ->action(function (): void {
                            Notification::make()
                                ->title('Now, now, don\'t be cheeky, leave some records for others to play with!')
                                ->warning()
                                ->send();
                        }),
                ]),
            ])
            ->groupedBulkActions([
                BulkAction::make('approve')
                    ->label(__('approve'))
                    ->icon(Heroicon::Check)
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Collection $records): void {
                        $records->each(function (LeaveRequest $record): void {
                            if ($record->status === LeaveStatus::Pending) {
                                $record->update([
                                    'status' => LeaveStatus::Approved,
                                    'reviewed_at' => now(),
                                ]);
                            }
                        });
                    })
                    ->deselectRecordsAfterCompletion(),
                BulkAction::make('reject')
                    ->label(__('Reject'))
                    ->icon(Heroicon::XMark)
                    ->color('danger')
                    ->schema([
                        Textarea::make('reviewer_notes')
                            ->label(__('Reason for rejection'))
                            ->required(),
                    ])
                    ->action(function (Collection $records, array $data): void {
                        $records->each(function (LeaveRequest $record) use ($data): void {
                            if ($record->status === LeaveStatus::Pending) {
                                $record->update([
                                    'status' => LeaveStatus::Rejected,
                                    'reviewer_notes' => $data['reviewer_notes'],
                                    'reviewed_at' => now(),
                                ]);
                            }
                        });
                    })
                    ->deselectRecordsAfterCompletion(),
                DeleteBulkAction::make()
                    ->action(function (): void {
                        Notification::make()
                            ->title('Now, now, don\'t be cheeky, leave some records for others to play with!')
                            ->warning()
                            ->send();
                    }),
            ]);
    }
}
