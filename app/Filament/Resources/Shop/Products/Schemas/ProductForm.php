<?php

namespace App\Filament\Resources\Shop\Products\Schemas;

use App\Filament\Resources\Shop\Brands\RelationManagers\ProductsRelationManager;
use App\Models\Shop\Product;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make()
                            ->schema([
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
                                    ->unique(Product::class, 'slug', ignoreRecord: true),

                                RichEditor::make('description')
                                    ->label(__('Description'))
                                    ->columnSpan('full'),
                            ])
                            ->columns(2),

                        Section::make(__('Images'))
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('media')
                                    ->label(__('Images'))
                                    ->collection('product-images')
                                    ->multiple()
                                    ->maxFiles(5)
                                    ->reorderable()
                                    ->acceptedFileTypes(['image/jpeg'])
                                    ->hiddenLabel(),
                            ])
                            ->collapsible(),

                        Section::make(__('Pricing'))
                            ->schema([
                                TextInput::make('price')
                                    ->label(__('Price'))
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(99999999.99)
                                    ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/'])
                                    ->required(),

                                TextInput::make('old_price')
                                    ->label(__('Compare at price'))
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(99999999.99)
                                    ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/'])
                                    ->required(),

                                TextInput::make('cost')
                                    ->label(__('Cost per item'))
                                    ->helperText(__('Customers won\'t see this price.'))
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(99999999.99)
                                    ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/'])
                                    ->required(),
                            ])
                            ->columns(2),
                        Section::make(__('Inventory'))
                            ->schema([
                                TextInput::make('sku')
                                    ->label(__('SKU (Stock Keeping Unit)'))
                                    ->unique(Product::class, 'sku', ignoreRecord: true)
                                    ->maxLength(255)
                                    ->required(),

                                TextInput::make('barcode')
                                    ->label(__('Barcode (ISBN, UPC, GTIN, etc.)'))
                                    ->unique(Product::class, 'barcode', ignoreRecord: true)
                                    ->maxLength(255)
                                    ->required(),

                                TextInput::make('qty')
                                    ->label(__('Quantity'))
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(18446744073709551615)
                                    ->integer()
                                    ->required(),

                                TextInput::make('security_stock')
                                    ->label(__('Security stock'))
                                    ->helperText(__('The safety stock is the limit stock for your products which alerts you if the product stock will soon be out of stock.'))
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(18446744073709551615)
                                    ->integer()
                                    ->required(),
                            ])
                            ->columns(2),

                        Section::make(__('Shipping'))
                            ->schema([
                                Checkbox::make('backorder')
                                    ->label(__('This product can be returned')),

                                Checkbox::make('requires_shipping')
                                    ->label(__('This product will be shipped')),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('Status'))
                            ->schema([
                                Toggle::make('is_visible')
                                    ->label(__('Visibility'))
                                    ->helperText(__('This product will be hidden from all sales channels.'))
                                    ->default(true),

                                DatePicker::make('published_at')
                                    ->label(__('Publishing date'))
                                    ->default(now())
                                    ->required(),
                            ]),

                        Section::make(__('Associations'))
                            ->schema([
                                Select::make('brand_id')
                                    ->label(__('Brand'))
                                    ->relationship('brand', 'name')
                                    ->searchable()
                                    ->hiddenOn(ProductsRelationManager::class),

                                Select::make('productCategories')
                                    ->label(__('Product category'))
                                    ->relationship('productCategories', 'name')
                                    ->multiple()
                                    ->required(),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }
}
