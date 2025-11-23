<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use Filament\Resources\Pages\CreateRecord;
use Spatie\Permission\PermissionRegistrar;
use App\Models\AuditLog;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    protected function afterCreate(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        AuditLog::create([
            'subject_type' => \Spatie\Permission\Models\Role::class,
            'subject_id' => $this->record->id,
            'action' => 'created',
            'changes' => $this->record->only(['name']),
            'user_id' => optional(auth()->user())->id,
        ]);
    }
}
