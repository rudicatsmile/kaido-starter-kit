<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use STS\FilamentImpersonate\Actions\Impersonate;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Actions\Action as TableAction;
use Illuminate\Support\HtmlString;
use App\Models\User;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('avatar')
                    ->label('Avatar')
                    ->collection('avatar')
                    ->conversion('thumb')
                    ->circular(),
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                BadgeColumn::make('roles.name')
                    ->label('Roles')
                    ->separator(', '),
                TextColumn::make('attachments_count')
                    ->label('Attachments')
                    ->state(fn(User $record): int => $record->getMedia('attachments')->count())
                    ->sortable(false),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
                Impersonate::make()
                    ->guard('web')
                    ->redirectTo('/'),
                TableAction::make('attachments')
                    ->label('Attachments')
                    ->visible(fn(User $record): bool => $record->getMedia('attachments')->isNotEmpty())
                    ->modalHeading('Attachments')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->modalContent(function (User $record): HtmlString {
                        $links = $record->getMedia('attachments')
                            ->map(fn($m) => '<a class="text-primary-600 underline" target="_blank" href="' . $m->getUrl() . '">' . e($m->file_name) . '</a>')
                            ->implode('<br>');
                        return new HtmlString($links ?: 'No attachments');
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
