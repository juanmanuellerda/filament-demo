<?php

namespace App\Filament\Resources\Blog\Posts\Pages;

use App\Filament\Resources\Blog\Posts\PostResource;
use BackedEnum;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;

class ManagePostComments extends ManageRelatedRecords
{
    protected static string $resource = PostResource::class;

    protected static string $relationship = 'comments';

    public static function getNavigationLabel(): string
        {
            return __('Comments');
        }

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedChatBubbleLeftEllipsis;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label(__('Title'))
                    ->required(),

                Select::make('customer_id')
                    ->label(__('Customer'))
                    ->relationship('customer', 'name')
                    ->searchable()
                    ->required(),

                Toggle::make('is_visible')
                    ->label(__('Public visibility'))
                    ->default(true),

                RichEditor::make('content')
                    ->label(__('Content'))
                    ->required(),
            ])
            ->columns(1);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                TextEntry::make('title')
                    ->label(__('Title'))
                    ->placeholder(__('Untitled')),
                TextEntry::make('customer.name')
                    ->label(__('Customer'))
                    ->placeholder(__('No customer')),
                IconEntry::make('is_visible')
                    ->label(__('Public visibility')),
                TextEntry::make('content')
                    ->label(__('Content'))
                    ->markdown()
                    ->placeholder(__('No content')),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('title')
                    ->label(__('Title'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('customer.name')
                    ->label(__('Customer'))
                    ->searchable()
                    ->sortable(),

                IconColumn::make('is_visible')
                    ->label(__('Public visibility'))
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->label(__('Add comment')),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->action(function (): void {
                        Notification::make()
                            ->title(__('Now, now, don\'t be cheeky, leave some records for others to play with!'))
                            ->warning()
                            ->send();
                    }),
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