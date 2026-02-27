<?php

namespace App\Filament\Resources\Blog\Authors\Schemas;

use App\Models\Blog\Author;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AuthorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('Name'))
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->label(__('Email address'))
                    ->required()
                    ->maxLength(255)
                    ->email()
                    ->unique(Author::class, 'email', ignoreRecord: true),

                RichEditor::make('bio')
                    ->label(__('Bio'))
                    ->columnSpan('full'),

                TextInput::make('github_handle')
                    ->label(__('GitHub handle'))
                    ->maxLength(255),

                TextInput::make('twitter_handle')
                    ->label(__('Twitter handle'))
                    ->maxLength(255),
            ]);
    }
}
