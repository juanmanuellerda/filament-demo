<?php

namespace App\Filament\Resources\HR\Projects\Schemas;

use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ProjectInfolist
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
                                TextEntry::make('name')
                                    ->label(__('Name')),
                                TextEntry::make('slug')
                                    ->label(__('Slug')),
                                TextEntry::make('department.name')
                                    ->label(__('Department'))
                                    ->placeholder(__('No department')),
                                TextEntry::make('status')
                                    ->label(__('Status'))
                                    ->badge(),
                                TextEntry::make('priority')
                                    ->label(__('Priority'))
                                    ->badge(),
                                ColorEntry::make('color')
                                    ->label(__('Color'))
                                    ->placeholder(__('No color')),
                                TextEntry::make('start_date')
                                    ->label(__('Start date'))
                                    ->date(),
                                TextEntry::make('end_date')
                                    ->label(__('End date'))
                                    ->date()
                                    ->placeholder(__('No end date')),
                                TextEntry::make('description')
                                    ->label(__('Description'))
                                    ->prose()
                                    ->markdown()
                                    ->columnSpanFull()
                                    ->placeholder(__('No description')),
                            ]),

                        Tab::make(__('Budget'))
                            ->icon(Heroicon::CurrencyDollar)
                            ->columns(2)
                            ->schema([
                                TextEntry::make('budget')
                                    ->label(__('Budget'))
                                    ->money('usd')
                                    ->placeholder('$0.00'),
                                TextEntry::make('spent')
                                    ->label(__('Spent'))
                                    ->money('usd')
                                    ->placeholder('$0.00'),
                                TextEntry::make('estimated_hours')
                                    ->label(__('Estimated hours'))
                                    ->suffix(' ' . __('hours'))
                                    ->placeholder('0'),
                                TextEntry::make('actual_hours')
                                    ->label(__('Actual hours'))
                                    ->suffix(' ' . __('hours'))
                                    ->placeholder('0'),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}