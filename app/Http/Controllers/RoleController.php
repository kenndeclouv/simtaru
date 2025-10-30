<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:view role')->only(['index', 'show']);
        $this->middleware('can:create role')->only(['create', 'store']);
        $this->middleware('can:edit role')->only(['edit', 'update']);
        $this->middleware('can:delete role')->only(['destroy']);
    }

    /**
     * Helper private untuk mengambil dan mengelompokkan data permission.
     */
    private function getPermissionsData()
    {
        $permissions = Permission::all();
        $permissionsByGroup = [];
        $allActions = [];

        foreach ($permissions as $permission) {
            $name = $permission->name;

            // UBAH LOGIKA DARI SINI
            // Kita cari posisi spasi TERAKHIR
            $lastSpacePos = strrpos($name, ' ');

            if ($lastSpacePos !== false) {
                // Semua sebelum spasi terakhir adalah ACTION (e.g., "view", "view any")
                $action = substr($name, 0, $lastSpacePos);
                // Kata terakhir adalah FEATURE (e.g., "permohonan")
                $feature = substr($name, $lastSpacePos + 1);
            } else {
                // Kalo nggak ada spasi (e.g., "dashboard" doang)
                $action = $name;
                $feature = $name;
            }
            // SAMPAI SINI

            // Kumpulkan semua action untuk dijadikan header kolom
            $allActions[] = $action;

            if (!isset($permissionsByGroup[$feature])) {
                $permissionsByGroup[$feature] = [];
            }

            // Simpan permission utuh untuk pengecekan di view
            // Kita pakai $action sebagai KEY biar gampang dicari di view
            $permissionsByGroup[$feature][$action] = $permission->name;
        }

        // Buang action yang duplikat dan urutkan
        $uniqueActions = array_unique($allActions);
        sort($uniqueActions);

        return [
            'actions' => $uniqueActions,
            'permissionsByGroup' => $permissionsByGroup,
        ];
    }

    public function index()
    {
        // Mengecualikan role "Super Admin" dari daftar agar tidak bisa diedit/dihapus
        $roles = Role::where('name', '!=', 'Super Admin')->get();
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $data = $this->getPermissionsData();
        return view('roles.create', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'nullable|array',
        ]);

        $role = Role::create(['name' => $request->name]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('roles.index')->with('success', 'Role berhasil dibuat!');
    }

    public function edit(Role $role)
    {
        // Super Admin tidak boleh diedit
        if ($role->name === 'Super Admin') {
            abort(403, 'Super Admin role cannot be edited.');
        }

        $data = $this->getPermissionsData();
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('roles.edit', array_merge($data, ['role' => $role, 'rolePermissions' => $rolePermissions]));
    }

    public function update(Request $request, Role $role)
    {
        // Super Admin tidak boleh diupdate
        if ($role->name === 'Super Admin') {
            abort(403, 'Super Admin role cannot be updated.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
        ]);

        $role->update(['name' => $request->name]);

        // syncPermissions akan menghapus permission lama dan menambah yang baru
        $role->syncPermissions($request->permissions ?? []);

        return redirect()->route('roles.index')->with('success', 'Role berhasil diupdate!');
    }

    public function destroy(Role $role)
    {
        // Super Admin tidak boleh dihapus
        if ($role->name === 'Super Admin') {
            abort(403, 'Super Admin role cannot be deleted.');
        }

        $role->delete();
        return redirect()->route('roles.index')->with('success', 'Role berhasil dihapus!');
    }
}
