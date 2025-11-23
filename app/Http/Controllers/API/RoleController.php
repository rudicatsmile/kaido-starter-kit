<?php

namespace App\Http\Controllers\API;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RoleController extends BaseController
{
    public function index()
    {
        return Role::with('permissions')->paginate(20);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'permissions' => ['array'],
            'permissions.*' => ['string', Rule::exists('permissions', 'name')],
        ]);

        $role = Role::create(['name' => $data['name'], 'guard_name' => 'web']);
        if (!empty($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        AuditLog::create([
            'subject_type' => Role::class,
            'subject_id' => $role->id,
            'action' => 'created',
            'changes' => ['name' => $role->name, 'permissions' => $role->permissions->pluck('name')],
            'user_id' => optional($request->user())->id,
        ]);

        return response()->json($role->load('permissions'), 201);
    }

    public function show(Role $role)
    {
        return $role->load('permissions');
    }

    public function update(Request $request, Role $role)
    {
        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('roles', 'name')->ignore($role->id)],
            'permissions' => ['array'],
            'permissions.*' => ['string', Rule::exists('permissions', 'name')],
        ]);

        $original = ['name' => $role->name, 'permissions' => $role->permissions->pluck('name')->all()];

        if (array_key_exists('name', $data)) {
            $role->name = $data['name'];
        }
        $role->save();
        if (array_key_exists('permissions', $data)) {
            $role->syncPermissions($data['permissions']);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $changes = ['name' => $role->name, 'permissions' => $role->permissions->pluck('name')->all(), 'original' => $original];
        AuditLog::create([
            'subject_type' => Role::class,
            'subject_id' => $role->id,
            'action' => 'updated',
            'changes' => $changes,
            'user_id' => optional($request->user())->id,
        ]);

        return $role->load('permissions');
    }

    public function destroy(Request $request, Role $role)
    {
        if (User::role($role->name)->exists()) {
            return response()->json(['message' => 'Role sedang digunakan dan tidak dapat dihapus'], 422);
        }

        $snapshot = ['name' => $role->name, 'permissions' => $role->permissions->pluck('name')->all()];
        $role->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        AuditLog::create([
            'subject_type' => Role::class,
            'subject_id' => $role->id,
            'action' => 'deleted',
            'changes' => $snapshot,
            'user_id' => optional($request->user())->id,
        ]);

        return response()->noContent();
    }
}

