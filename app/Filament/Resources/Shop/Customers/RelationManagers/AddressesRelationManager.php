<?php

namespace App\Filament\Resources\Shop\Customers\RelationManagers;

use App\Enums\CountryCode;
use Filament\Actions\AttachAction;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class AddressesRelationManager extends RelationManager
{
    protected static string $relationship = 'addresses';

    protected static ?string $recordTitleAttribute = 'full_address';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Addresses');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('street')
                    ->label(__('Street address')),

                TextInput::make('zip')
                    ->label(__('Zip / Postal code')),

                TextInput::make('city')
                    ->label(__('City')),

                TextInput::make('state')
                    ->label(__('State / Province')),

                Select::make('country')
                    ->label(__('Country'))
                    ->options(CountryCode::class)
                    ->searchable(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('street')
                    ->label(__('Street address'))
                    ->weight(FontWeight::Medium),

                TextColumn::make('zip')
                    ->label(__('Zip / Postal code')),

                TextColumn::make('city')
                    ->label(__('City')),

                TextColumn::make('country')
                    ->label(__('Country')),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                AttachAction::make(),
                CreateAction::make()
                ->label(__('Add address')),
            ])
            ->recordActions([
                EditAction::make(),
                DetachAction::make(),
                DeleteAction::make(),
            ])
            ->groupedBulkActions([
                DetachBulkAction::make(),
                DeleteBulkAction::make(),
            ]);
    }
}