<?php

namespace App\Filament\Resources\Permissions\Pages;

use App\Filament\Resources\Permissions\PermissionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Spatie\Permission\PermissionRegistrar;
use App\Models\AuditLog;

class EditPermission extends EditRecord
{
    protected static string $resource = PermissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        AuditLog::create([
            'subject_type' => \Spatie\Permission\Models\Permission::class,
            'subject_id' => $this->record->id,
            'action' => 'updated',
            'changes' => $this->record->only(['name', 'guard_name']),
            'user_id' => optional(auth()->user())->id,
        ]);
    }

    protected function afterDelete(): void
    {
        AuditLog::create([
            'subject_type' => \Spatie\Permission\Models\Permission::class,
            'subject_id' => $this->record->id,
            'action' => 'deleted',
            'changes' => $this->record->only(['name', 'guard_name']),
            'user_id' => optional(auth()->user())->id,
        ]);
    }
}
