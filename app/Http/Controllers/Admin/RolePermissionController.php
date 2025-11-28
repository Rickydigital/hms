<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class RolePermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage roles');
    }

    public function index()
    {
        $roles = Role::with('permissions')->paginate(15);
        $permissions = Permission::all()->pluck('name', 'id');
        return view('admin.roles.index', compact('roles', 'permissions'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|unique:roles,name']);
        $role = Role::create(['name' => $request->name]);
        $role->syncPermissions($request->permissions ?? []);
        return back()->with('success', 'Role created successfully');
    }

    public function update(Request $request, Role $role)
    {
        $request->validate(['name' => 'required|unique:roles,name,'.$role->id]);
        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permissions ?? []);
        return back()->with('success', 'Role updated');
    }
}