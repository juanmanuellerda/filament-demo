<?php

namespace App\Filament\Resources\HR\Departments\Schemas;

use App\Models\HR\Department;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class DepartmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('name')
                    ->label(__('Name'))
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (string $operation, $state, Set $set): void {
                        if ($operation !== 'create') {
                            return;
                        }

                        $set('slug', Str::slug($state));
                    }),

                TextInput::make('slug')
                    ->label(__('Slug'))
                    ->disabled()
                    ->dehydrated()
                    ->required()
                    ->maxLength(255)
                    ->unique(Department::class, 'slug', ignoreRecord: true),

                Select::make('parent_id')
                    ->label(__('Parent department'))
                    ->relationship('parent', 'name', fn ($query) => $query->whereNull('parent_id'))
                    ->searchable()
                    ->preload()
                    ->columnSpanFull(),

                Textarea::make('description')
                    ->label(__('Description'))
                    ->rows(3)
                    ->maxLength(65535)
                    ->columnSpanFull(),

                TextInput::make('budget')
                    ->label(__('Budget'))
                    ->required()
                    ->numeric()
                    ->prefix('$')
                    ->minValue(0)
                    ->maxValue(9999999999.99)
                    ->default(0),

                TextInput::make('headcount_limit')
                    ->label(__('Headcount limit'))
                    ->required()
                    ->integer()
                    ->minValue(0)
                    ->maxValue(2147483647)
                    ->default(0),

                ColorPicker::make('color')
                    ->label(__('Color')),

                Toggle::make('is_active')
                    ->label(__('Active'))
                    ->default(true)
                    ->columnStart(1),
            ]);
    }
}
