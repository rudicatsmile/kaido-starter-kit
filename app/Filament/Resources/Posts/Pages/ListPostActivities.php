<?php

namespace App\Filament\Resources\Posts\Pages;

use App\Filament\Resources\Posts\PostResource;
use pxlrbt\FilamentActivityLog\Pages\ListActivities;

class ListPostActivities extends ListActivities
{
    protected static string $resource = PostResource::class;

    public function mount($record): void
    {
        abort_unless(optional(auth()->user())->hasRole('super_admin'), 403);
        parent::mount($record);
    }
}
