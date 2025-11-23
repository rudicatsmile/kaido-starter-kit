<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use Filament\Resources\Pages\CreateRecord;
use Spatie\Permission\PermissionRegistrar;
use App\Models\AuditLog;
use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;
    public Collection $permissions;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->permissions = collect($data)
            ->filter(fn (mixed $permission, string $key): bool => ! in_array($key, ['name', 'guard_name', 'select_all']))
            ->values()
            ->flatten()
            ->unique();

        return Arr::only($data, ['name', 'guard_name']);
    }

    protected function afterCreate(): void
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
            'action' => 'created',
            'changes' => $this->record->only(['name']),
            'user_id' => optional(auth()->user())->id,
        ]);
    }
}
