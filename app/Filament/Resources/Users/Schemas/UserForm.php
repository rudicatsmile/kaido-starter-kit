<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Name')
                                    ->required(),
                                TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->required(),
                            ]),
                        Grid::make(2)
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('avatar')
                                    ->label('Avatar')
                                    ->collection('avatar')
                                    ->image()
                                    ->imageEditor()
                                    ->maxSize(2 * 1024)
                                    ->columnSpan(1),
                                SpatieMediaLibraryFileUpload::make('attachments')
                                    ->label('Attachments')
                                    ->collection('attachments')
                                    ->multiple()
                                    ->reorderable()
                                    ->maxFiles(5)
                                    ->maxSize(5 * 1024)
                                    ->columnSpan(1),
                            ]),
                        Grid::make(2)
                            ->schema([
                                TextInput::make('password')
                                    ->label('Password')
                                    ->password()
                                    ->revealable()
                                    ->required(fn (string $operation): bool => $operation === 'create')
                                    ->dehydrated(fn ($state) => filled($state))
                                    ->dehydrateStateUsing(fn ($state) => bcrypt($state))
                                    ->autocomplete('new-password'),
                                TextInput::make('password_confirmation')
                                    ->label('Confirm Password')
                                    ->password()
                                    ->revealable()
                                    ->same('password')
                                    ->required(fn (string $operation): bool => $operation === 'create')
                                    ->autocomplete('new-password'),
                            ]),
                        Select::make('roles')
                            ->label('Roles')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload(),
                    ]),
            ]);
    }
}
