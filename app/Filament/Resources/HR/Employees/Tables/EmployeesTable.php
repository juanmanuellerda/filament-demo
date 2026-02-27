<?php

namespace App\Filament\Resources\HR\Employees\Tables;

use App\Enums\EmploymentType;
use App\Models\HR\Employee;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ToggleButtons;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

class EmployeesTable
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

                TextColumn::make('email')
                    ->label(__('Email'))
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('department.name')
                    ->label(__('Department'))
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->placeholder(__('No department')),

                TextColumn::make('job_title')
                    ->label(__('Job title'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('employment_type')
                    ->label(__('Employment type'))
                    ->badge(),

                TextColumn::make('salary')
                    ->label(__('Salary'))
                    ->money('usd')
                    ->sortable()
                    ->toggleable(),

                ColorColumn::make('team_color')
                    ->label(__('Team color'))
                    ->toggleable()
                    ->toggledHiddenByDefault(),

                TextColumn::make('hire_date')
                    ->label(__('Hire date'))
                    ->date()
                    ->sortable()
                    ->toggleable(),

                IconColumn::make('is_active')
                    ->label(__('Active'))
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('employment_type')
                    ->label(__('Employment type'))
                    ->options(EmploymentType::class),

                SelectFilter::make('department')
                    ->label(__('Department'))
                    ->relationship('department', 'name'),

                TernaryFilter::make('is_active')
                    ->label(__('Is active')),

                TrashedFilter::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('view_profile')
                        ->label('View profile')
                        ->icon(Heroicon::Eye)
                        ->color('primary')
                        ->slideOver()
                        ->schema([
                            TextEntry::make('name')
                                ->label(__('Name')),
                            TextEntry::make('email')
                                ->label(__('Email')),
                            TextEntry::make('phone')
                                ->label(__('Phone')),
                            TextEntry::make('department.name')
                                ->label(__('Department'))
                                ->placeholder(__('No department')),
                            TextEntry::make('job_title')
                                ->label(__('Job title')),
                            TextEntry::make('employment_type')
                                ->label(__('Employment type'))
                                ->badge(),
                            TextEntry::make('salary')
                                ->label(__('Salary'))
                                ->money('usd'),
                            TextEntry::make('hire_date')
                                ->label(__('Hire date'))
                                ->date(),
                            IconEntry::make('is_active')
                                ->label(__('Active'))
                                ->boolean(),
                        ])
                        ->modalSubmitAction(false),
                    EditAction::make(),
                    Action::make('toggle_active')
                        ->icon(fn (Employee $record): Heroicon => $record->is_active ? Heroicon::XMark : Heroicon::Check)
                        ->label(fn (Employee $record): string => $record->is_active ? __('Deactivate') : __('Activate'))
                        ->color(fn (Employee $record): string => $record->is_active ? 'danger' : 'success')
                        ->requiresConfirmation()
                        ->action(fn (Employee $record) => $record->update(['is_active' => ! $record->is_active])),
                    Action::make('change_department')
                        ->label('Change department')
                        ->icon(Heroicon::BuildingOffice2)
                        ->color('primary')
                        ->modalWidth(Width::Medium)
                        ->modalSubmitActionLabel(__('Save'))
                        ->fillForm(fn (Employee $record): array => [
                            'department_id' => $record->department_id,
                        ])
                        ->schema([
                            Select::make('department_id')
                                ->relationship('department', 'name')
                                ->label(__('Department'))
                                ->searchable()
                                ->preload()
                                ->required(),
                        ])
                        ->action(fn (Employee $record, array $data) => $record->update($data)),
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
                BulkAction::make('change_department')
                    ->label('Change department')
                    ->icon(Heroicon::BuildingOffice2)
                    ->color('primary')
                    ->schema([
                        Select::make('department_id')
                            ->relationship('department', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])
                    ->action(function (Collection $records, array $data): void {
                        $records->each(fn (Employee $record) => $record->update($data));
                    })
                    ->deselectRecordsAfterCompletion(),
                BulkAction::make('toggle_active')
                    ->label('Toggle active')
                    ->icon(Heroicon::Power)
                    ->color('warning')
                    ->schema([
                        ToggleButtons::make('is_active')
                            ->options([
                                '1' => __('Active'),
                                '0' => __('Inactive'),
                            ])
                            ->inline()
                            ->required(),
                    ])
                    ->action(function (Collection $records, array $data): void {
                        $records->each(fn (Employee $record) => $record->update(['is_active' => (bool) $data['is_active']]));
                    })
                    ->deselectRecordsAfterCompletion(),
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
