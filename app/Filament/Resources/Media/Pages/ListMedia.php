<?php

namespace App\Filament\Resources\Media\Pages;

use App\Filament\Resources\Media\MediaResource;
use Filament\Resources\Pages\ListRecords;

class ListMedia extends ListRecords
{
    protected static string $resource = MediaResource::class;

    public function mount(): void
    {
        abort_unless(optional(auth()->user())->hasRole('super_admin') || optional(auth()->user())->can('media.viewAny'), 403);
        parent::mount();
    }
}

