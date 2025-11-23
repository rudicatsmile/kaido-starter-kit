<?php

namespace App\Filament\Resources\Media\Tables;

use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MediaTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('preview')
                    ->label('Preview')
                    ->getStateUsing(fn($record) => method_exists($record, 'getUrl') ? $record->getUrl() : null)
                    ->circular(),
                TextColumn::make('file_name')
                    ->label('File')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('collection_name')
                    ->label('Collection')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('model_type')
                    ->label('Model')
                    ->sortable(),
                TextColumn::make('model_id')
                    ->label('Model ID')
                    ->sortable(),
                TextColumn::make('size')
                    ->label('Size (KB)')
                    ->state(fn($record) => number_format(($record->size ?? 0) / 1024, 1))
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Uploaded')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('collection_name')
                    ->options(fn() => \Spatie\MediaLibrary\MediaCollections\Models\Media::query()
                        ->select('collection_name')
                        ->distinct()
                        ->pluck('collection_name', 'collection_name')
                        ->filter()),
            ])
            ->recordActions([
                DeleteAction::make(),
            ]);
    }
}

