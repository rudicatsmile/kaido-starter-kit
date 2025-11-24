<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use App\Models\User;
use Spatie\Permission\PermissionRegistrar;
use App\Models\AuditLog;
use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;
    public Collection $permissions;

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

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->permissions = collect($data)
            ->filter(fn (mixed $permission, string $key): bool => ! in_array($key, ['name', 'guard_name', 'select_all']))
            ->values()
            ->flatten()
            ->unique();

        return Arr::only($data, ['name', 'guard_name']);
    }

    protected function afterSave(): void
    {
        $permissionModels = collect();
        $this->permissions->each(function (string $permission) use ($permissionModels): void {
            $permissionModels->push(Utils::getPermissionModel()::firstOrCreate([
                'name' => $permission,
                'guard_name' => $this->data['guard_name'],
            ]));
        });

        $this->record->syncPermissions($permissionModels);

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
