<?php

namespace App\Filament\Resources\Blog\Posts\Schemas;

use App\Models\Blog\Post;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\SpatieTagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextInput::make('title')
                            ->label(__('Title'))
                            ->required()
                            ->live(onBlur: true)
                            ->maxLength(255)
                            ->afterStateUpdated(fn (string $operation, $state, Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),

                        TextInput::make('slug')
                            ->label(__('Slug'))
                            ->disabled()
                            ->dehydrated()
                            ->required()
                            ->maxLength(255)
                            ->unique(Post::class, 'slug', ignoreRecord: true),

                        RichEditor::make('content')
                            ->label(__('Content'))
                            ->required()
                            ->columnSpan('full'),

                        Select::make('author_id')
                            ->label(__('Author'))
                            ->relationship('author', 'name')
                            ->searchable()
                            ->required(),

                        Select::make('post_category_id')
                            ->label(__('Category'))
                            ->relationship('postCategory', 'name')
                            ->searchable()
                            ->required(),

                        DatePicker::make('published_at')
                            ->label(__('Publishing date')),

                        SpatieTagsInput::make('tags')
                            ->label(__('Tags')),
                    ])
                    ->columns(2),

                Section::make(__('Image'))
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('image')
                            ->collection('post-images')
                            ->hiddenLabel()
                            ->acceptedFileTypes(['image/jpeg']),
                    ])
                    ->collapsible(),
            ]);
    }
}
