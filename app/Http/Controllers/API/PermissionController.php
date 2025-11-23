<?php

namespace App\Http\Controllers\API;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionController extends BaseController
{
    public function index()
    {
        return Permission::paginate(20);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name'],
            'guard_name' => ['sometimes', 'string'],
        ]);

        $permission = Permission::create([
            'name' => $data['name'],
            'guard_name' => $data['guard_name'] ?? 'web',
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        AuditLog::create([
            'subject_type' => Permission::class,
            'subject_id' => $permission->id,
            'action' => 'created',
            'changes' => ['name' => $permission->name],
            'user_id' => optional($request->user())->id,
        ]);

        return response()->json($permission, 201);
    }

    public function show(Permission $permission)
    {
        return $permission;
    }

    public function update(Request $request, Permission $permission)
    {
        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('permissions', 'name')->ignore($permission->id)],
            'guard_name' => ['sometimes', 'string'],
        ]);

        $original = ['name' => $permission->name, 'guard_name' => $permission->guard_name];

        $permission->fill($data);
        $permission->save();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        AuditLog::create([
            'subject_type' => Permission::class,
            'subject_id' => $permission->id,
            'action' => 'updated',
            'changes' => ['original' => $original, 'new' => $permission->only(['name', 'guard_name'])],
            'user_id' => optional($request->user())->id,
        ]);

        return $permission;
    }

    public function destroy(Request $request, Permission $permission)
    {
        $snapshot = ['name' => $permission->name];
        $permission->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        AuditLog::create([
            'subject_type' => Permission::class,
            'subject_id' => $permission->id,
            'action' => 'deleted',
            'changes' => $snapshot,
            'user_id' => optional($request->user())->id,
        ]);

        return response()->noContent();
    }
}

