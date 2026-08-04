<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(): View
    {
        $roles = Role::query()
            ->where('guard_name', 'admin')
            ->with('permissions')
            ->latest()
            ->get();

        return view('admin.pages.role.index', compact('roles'));
    }

    public function create(): View
    {
        $permissions = Permission::query()
            ->where('guard_name', 'admin')
            ->orderBy('name')
            ->get();

        return view('admin.pages.role.create', compact('permissions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:roles,name'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ], [
            'name.required' => 'وارد کردن نام نقش الزامی است.',
            'name.unique' => 'این نقش قبلاً ایجاد شده است.',
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => 'admin',
        ]);

        $permissions = Permission::query()
            ->where('guard_name', 'admin')
            ->whereIn('id', $validated['permissions'] ?? [])
            ->get();

        $role->syncPermissions($permissions);

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'نقش جدید با موفقیت ایجاد شد.');
    }

    public function edit(Role $role): View
    {
        abort_unless($role->guard_name === 'admin', 404);

        $permissions = Permission::query()
            ->where('guard_name', 'admin')
            ->orderBy('name')
            ->get();

        $rolePermissionIds = $role->permissions()
            ->pluck('id')
            ->all();

        return view('admin.pages.role.edit',
            compact('role', 'permissions', 'rolePermissionIds')
        );
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        abort_unless($role->guard_name === 'admin', 404);

        $validated = $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);

        $permissions = Permission::query()
            ->where('guard_name', 'admin')
            ->whereIn('id', $validated['permissions'] ?? [])
            ->get();

        $role->syncPermissions($permissions);

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'دسترسی‌های نقش با موفقیت به‌روزرسانی شد.');
    }
}
