<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Http\Controllers\Controller;

class RolePermissionController extends Controller
{
    // ─────────────────────────────────────────────
    //  RÔLES
    // ─────────────────────────────────────────────

    public function indexRoles(Request $request)
    {
        $roles = Role::with('permissions')
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->paginate(10);

        return response()->json($roles);
    }

    public function showRole(Role $role)
    {
        $role->load('permissions');
        return response()->json($role);
    }

    public function storeRole(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role = Role::create(['name' => $validated['name'], 'guard_name' => 'web']);

        if (!empty($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        return response()->json([
            'message' => 'Rôle créé avec succès',
            'role'    => $role->load('permissions'),
        ], 201);
    }

    public function updateRole(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name'        => 'sometimes|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        if (isset($validated['name'])) {
            $role->name = $validated['name'];
            $role->save();
        }

        if (isset($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        return response()->json([
            'message' => 'Rôle mis à jour avec succès',
            'role'    => $role->load('permissions'),
        ]);
    }

    public function destroyRole(Role $role)
    {
        // Retirer le rôle de tous les utilisateurs avant suppression
        $role->users()->detach();
        $role->delete();

        return response()->json(['message' => 'Rôle supprimé avec succès']);
    }

    // ─────────────────────────────────────────────
    //  PERMISSIONS
    // ─────────────────────────────────────────────

    public function indexPermissions(Request $request)
    {
        $permissions = Permission::query()
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->paginate(10);

        return response()->json($permissions);
    }

    public function showPermission(Permission $permission)
    {
        return response()->json($permission);
    }

    public function storePermission(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
        ]);

        $permission = Permission::create([
            'name'       => $validated['name'],
            'guard_name' => 'web',
        ]);

        return response()->json([
            'message'    => 'Permission créée avec succès',
            'permission' => $permission,
        ], 201);
    }

    public function updatePermission(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id,
        ]);

        $permission->name = $validated['name'];
        $permission->save();

        return response()->json([
            'message'    => 'Permission mise à jour avec succès',
            'permission' => $permission,
        ]);
    }

    public function destroyPermission(Permission $permission)
    {
        $permission->delete();

        return response()->json(['message' => 'Permission supprimée avec succès']);
    }
}
