<?php

namespace App\Filament\Resources\Shop\Customers\Tables;

use App\Models\Shop\Customer;
use Filament\Actions\Action;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class CustomersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable(isIndividual: true)
                    ->sortable()
                    ->weight(FontWeight::Medium),
                TextColumn::make('email')
                    ->label(__('Email address'))
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->sortable(),
                TextColumn::make('country')
                    ->label(__('Country'))
                    ->getStateUsing(fn ($record): ?string => $record->addresses->first()?->country?->getLabel()),
                TextColumn::make('phone')
                    ->label(__('Phone'))
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                Action::make('send_email')
                    ->label(__('Send email'))
                    ->icon(Heroicon::Envelope)
                    ->color('info')
                    ->modalWidth(Width::Large)
                    ->fillForm(fn (Customer $record): array => [
                        'to' => $record->email,
                    ])
                    ->schema([
                        TextInput::make('to')
                            ->email()
                            ->disabled()
                            ->dehydrated(),
                        TextInput::make('subject')
                            ->required(),
                        RichEditor::make('body')
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->action(function (Customer $record): void {
                        Notification::make()
                            ->title(__('Email sent to :name', ['name' => $record->name]))
                            ->success()
                            ->send();
                    }),
                EditAction::make(),
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