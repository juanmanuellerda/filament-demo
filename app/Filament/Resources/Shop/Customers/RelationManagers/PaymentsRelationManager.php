<?php

namespace App\Filament\Resources\Shop\Customers\RelationManagers;

use App\Enums\CurrencyCode;
use App\Enums\PaymentMethod;
use App\Filament\Resources\Shop\Orders\OrderResource;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Payments');
    }

    protected static ?string $recordTitleAttribute = 'reference';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('order_id')
                    ->label(__('Order'))
                    ->relationship(
                        'order',
                        'number',
                        fn (Builder $query, RelationManager $livewire) => $query->whereBelongsTo($livewire->ownerRecord)
                    )
                    ->searchable()
                    ->hiddenOn('edit')
                    ->required(),

                TextInput::make('reference')
                    ->label(__('Reference'))
                    ->columnSpan(fn (string $operation) => $operation === 'edit' ? 2 : 1)
                    ->required(),

                TextInput::make('amount')
                    ->label(__('Amount'))
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(99999999.99)
                    ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/'])
                    ->required(),

                Select::make('currency')
                    ->label(__('Currency'))
                    ->options(CurrencyCode::class)
                    ->searchable()
                    ->required(),

                ToggleButtons::make('provider')
                    ->label(__('Provider'))
                    ->inline()
                    ->grouped()
                    ->options([
                        'stripe' => __('Stripe'),
                        'paypal' => __('PayPal'),
                    ])
                    ->required(),

                ToggleButtons::make('method')
                    ->label(__('Method'))
                    ->inline()
                    ->options(PaymentMethod::class)
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order.number')
                    ->label(__('Order number'))
                    ->url(fn ($record) => OrderResource::getUrl('edit', [$record->order]))
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Medium),

                ColumnGroup::make(__('Details'))
                    ->columns([
                        TextColumn::make('reference')
                            ->label(__('Reference'))
                            ->searchable(),

                        TextColumn::make('amount')
                            ->label(__('Amount'))
                            ->sortable()
                            ->money(fn ($record) => $record->currency->value),
                    ]),

                ColumnGroup::make(__('Context'))
                    ->columns([
                        TextColumn::make('provider')
                            ->label(__('Provider'))
                            ->formatStateUsing(fn ($state) => Str::headline($state))
                            ->sortable(),

                        TextColumn::make('method')
                            ->label(__('Method'))
                            ->formatStateUsing(fn ($state) => Str::headline($state))
                            ->sortable(),
                    ]),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->label(__('Add payment')),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->groupedBulkActions([
                DeleteBulkAction::make(),
            ]);
    }
}
