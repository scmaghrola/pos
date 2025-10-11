<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class UserPermissionController extends Controller
{
    public function edit($userId)
    {
        $user = User::findOrFail($userId);
        $permissions = Permission::all()->groupBy(function ($perm) {
            return explode('.', $perm->name)[0]; // group by module
        });

        return view('pos.users.permission', compact('user', 'permissions'));
    }

    public function update(Request $request, $userId)
    {
        $user = User::findOrFail($userId);

        $user->syncPermissions($request->permissions ?? []); // update all selected permissions

        return response()->json(['success' => true, 'message' => 'Permissions updated successfully!']);
    }

    
}
