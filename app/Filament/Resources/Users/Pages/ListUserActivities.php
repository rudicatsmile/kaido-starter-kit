<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use pxlrbt\FilamentActivityLog\Pages\ListActivities;

class ListUserActivities extends ListActivities
{
    protected static string $resource = UserResource::class;

    public function mount($record): void
    {
        abort_unless(optional(auth()->user())->hasRole('super_admin'), 403);
        parent::mount($record);
    }
}
