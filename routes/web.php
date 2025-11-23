<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['web', 'auth'])
    ->get('/admin/grant-impersonate-me', function () {
        $user = Auth::user();
        $permission = Permission::firstOrCreate(['name' => 'users.impersonate', 'guard_name' => 'web']);
        if (!$user->can('users.impersonate')) {
            $user->givePermissionTo($permission);
        }
        return redirect()->back();
    });
