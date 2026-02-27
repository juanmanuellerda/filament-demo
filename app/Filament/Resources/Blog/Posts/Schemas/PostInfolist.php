<?php

namespace App\Filament\Resources\Blog\Posts\Schemas;

use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\SpatieTagsEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PostInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Flex::make([
                            Grid::make(2)
                                ->schema([
                                    Group::make([
                                        TextEntry::make('title')
                                            ->label(__('Title')),
                                        TextEntry::make('slug')
                                            ->label(__('Slug')),
                                        TextEntry::make('published_at')
                                            ->label(__('Publishing date'))
                                            ->badge()
                                            ->date()
                                            ->color('success')
                                            ->placeholder(__('Not published')),
                                    ]),
                                    Group::make([
                                        TextEntry::make('author.name')
                                            ->label(__('Author'))
                                            ->placeholder(__('No author')),
                                        TextEntry::make('postCategory.name')
                                            ->label(__('Category'))
                                            ->placeholder(__('Uncategorized')),
                                        SpatieTagsEntry::make('tags')
                                            ->label(__('Tags')),
                                    ]),
                                ]),
                            SpatieMediaLibraryImageEntry::make('image')
                                ->collection('post-images')
                                ->hiddenLabel()
                                ->grow(false),
                        ])->from('lg'),
                    ]),
                Section::make(__('Content'))
                    ->schema([
                        TextEntry::make('content')
                            ->prose()
                            ->markdown()
                            ->hiddenLabel(),
                    ])
                    ->collapsible(),
            ]);
    }
}
