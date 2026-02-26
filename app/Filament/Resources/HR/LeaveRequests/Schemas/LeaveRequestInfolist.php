<?php

namespace App\Filament\Resources\HR\LeaveRequests\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LeaveRequestInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Leave Details'))
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('employee.name')
                            ->label(__('Employee')),
                        TextEntry::make('type')
                            ->label(__('Type'))
                            ->badge(),
                        TextEntry::make('status')
                            ->label(__('Status'))
                            ->badge(),
                        TextEntry::make('start_date')
                            ->label(__('Start date'))
                            ->date(),
                        TextEntry::make('end_date')
                            ->label(__('End date'))
                            ->date(),
                        TextEntry::make('days_requested')
                            ->label(__('Days requested'))
                            ->suffix(' ' . __('days')),
                        TextEntry::make('start_time')
                            ->label(__('Start time (half days)'))
                            ->time('H:i')
                            ->placeholder(__('N/A')),
                        TextEntry::make('end_time')
                            ->label(__('End time (half days)'))
                            ->time('H:i')
                            ->placeholder(__('N/A')),
                        TextEntry::make('reason')
                            ->label(__('Reason'))
                            ->columnSpanFull(),
                    ]),

                Section::make(__('Review'))
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('approver.name')
                            ->label(__('Approver'))
                            ->placeholder(__('Not yet assigned')),
                        TextEntry::make('reviewed_at')
                            ->label(__('Reviewed at'))
                            ->dateTime()
                            ->placeholder(__('Not yet reviewed')),
                        TextEntry::make('reviewer_notes')
                            ->label(__('Reviewer notes'))
                            ->placeholder(__('No notes'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
