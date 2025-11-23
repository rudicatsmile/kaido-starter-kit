<?php

namespace App\Filament\Resources\Permissions\Pages;

use App\Filament\Resources\Permissions\PermissionResource;
use Filament\Resources\Pages\CreateRecord;
use Spatie\Permission\PermissionRegistrar;
use App\Models\AuditLog;

class CreatePermission extends CreateRecord
{
    protected static string $resource = PermissionResource::class;

    protected function afterCreate(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        AuditLog::create([
            'subject_type' => \Spatie\Permission\Models\Permission::class,
            'subject_id' => $this->record->id,
            'action' => 'created',
            'changes' => $this->record->only(['name', 'guard_name']),
            'user_id' => optional(auth()->user())->id,
        ]);
    }
}
