<?php

namespace App\Filament\Resources\Media;

use App\Filament\Resources\Media\Pages\ListMedia;
use App\Filament\Resources\Media\Tables\MediaTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Spatie\MediaLibrary\MediaCollections\Models\Media as MediaModel;

class MediaResource extends Resource
{
    protected static ?string $model = MediaModel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function getNavigationLabel(): string
    {
        return 'Media';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Resource';
    }

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        return (bool) $user && ($user->hasRole('super_admin') || $user->can('media.viewAny'));
    }

    public static function table(Table $table): Table
    {
        return MediaTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMedia::route('/'),
        ];
    }
}

