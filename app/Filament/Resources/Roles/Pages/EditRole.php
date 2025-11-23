<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use App\Models\User;
use Spatie\Permission\PermissionRegistrar;
use App\Models\AuditLog;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->requiresConfirmation()
                ->before(function ($record) {
                    if (User::role($record->name)->exists()) {
                        throw new \Exception('Role sedang digunakan dan tidak dapat dihapus');
                    }
                }),
        ];
    }

    protected function afterSave(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        AuditLog::create([
            'subject_type' => \Spatie\Permission\Models\Role::class,
            'subject_id' => $this->record->id,
            'action' => 'updated',
            'changes' => $this->record->only(['name']),
            'user_id' => optional(auth()->user())->id,
        ]);
    }

    protected function afterDelete(): void
    {
        AuditLog::create([
            'subject_type' => \Spatie\Permission\Models\Role::class,
            'subject_id' => $this->record->id,
            'action' => 'deleted',
            'changes' => $this->record->only(['name']),
            'user_id' => optional(auth()->user())->id,
        ]);
    }
}
