<?php

// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use Spatie\Permission\Models\Role;
// use Spatie\Permission\Models\Permission;

// class RoleController extends Controller
// {
//     public function __construct()
//     {
//         // User harus login untuk semua method
//         $this->middleware('auth');

//         // Terapkan permission spesifik untuk setiap method
//         $this->middleware('can:view role')->only(['index', 'show']);
//         $this->middleware('can:create role')->only(['create', 'store']);
//         $this->middleware('can:edit role')->only(['edit', 'update']);
//         $this->middleware('can:delete role')->only(['destroy']);
//     }

//     public function index()
//     {
//         $roles = Role::all()->except([1]);
//         return view('roles.index', compact('roles'));
//     }

//     public function create()
//     {
//         // 1. Ambil semua permission
//         $permissions = Permission::all();

//         // 2. Definisikan aksi standar untuk kolom tabel
//         $actions = ['view', 'create', 'edit', 'delete'];

//         // 3. Kelompokkan permission berdasarkan fitur
//         $permissionsByGroup = [];
//         foreach ($permissions as $permission) {
//             // Pecah nama permission, contoh: "view permohonan" -> $parts[0]="view", $parts[1]="permohonan"
//             $parts = explode(' ', $permission->name, 2);
//             $feature = $parts[1]; // Nama fitur
//             $action = $parts[0];  // Nama aksi

//             if (!isset($permissionsByGroup[$feature])) {
//                 $permissionsByGroup[$feature] = [];
//             }

//             $permissionsByGroup[$feature][] = $action;
//         }

//         return view('roles.create', compact('actions', 'permissionsByGroup'));
//     }

//     public function store(Request $request)
//     {
//         // Validasi input
//         $request->validate([
//             'name' => 'required|unique:roles,name',
//             'permissions' => 'nullable|array', // permissions bisa jadi tidak dipilih sama sekali
//         ]);

//         // Buat role baru
//         $role = Role::create(['name' => $request->name]);

//         // Sync permissions yang dipilih dari form
//         if ($request->has('permissions')) {
//             $role->syncPermissions($request->permissions);
//         }

//         return redirect()->route('roles.index')->with('success', 'Role berhasil dibuat!');
//     }

//     public function edit(Role $role)
//     {
//         // 1. Ambil semua permission
//         $permissions = Permission::all();

//         // 2. Definisikan aksi standar untuk kolom tabel
//         $actions = ['view', 'create', 'edit', 'delete'];

//         // 3. Kelompokkan permission berdasarkan fitur
//         $permissionsByGroup = [];
//         foreach ($permissions as $permission) {
//             // Pecah nama permission, contoh: "view permohonan" -> $parts[0]="view", $parts[1]="permohonan"
//             $parts = explode(' ', $permission->name, 2);
//             $feature = $parts[1]; // Nama fitur
//             $action = $parts[0];  // Nama aksi

//             if (!isset($permissionsByGroup[$feature])) {
//                 $permissionsByGroup[$feature] = [];
//             }

//             $permissionsByGroup[$feature][] = $action;
//         }

//         return view('roles.edit', compact('actions', 'permissionsByGroup', 'role'));
//     }
//     public function update(Role $role, Request $request)
//     {
//         // Validasi input
//         $request->validate([
//             'name' => 'sometimes|string|max:255|unique:roles,name,' . $role->id,
//             'permissions' => 'nullable|array', // permissions bisa jadi tidak dipilih sama sekali
//         ]);

//         // Buat role baru
//         $role->update(['name' => $request->name ?? $role->name]);

//         // Sync permissions yang dipilih dari form
//         if ($request->has('permissions')) {
//             $role->syncPermissions($request->permissions);
//         }

//         return redirect()->route('roles.index')->with('success', 'Role berhasil diupdate!');
//     }

//     public function destroy(Role $role)
//     {
//         $role->delete();
//         return redirect()->route('roles.index')->with('success', 'Role berhasil dihapus!');
//     }
// }

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
            $parts = explode(' ', $permission->name, 2);
            $action = $parts[0];
            $feature = $parts[1] ?? $action; // Fallback jika permission hanya satu kata

            // Kumpulkan semua action untuk dijadikan header kolom
            $allActions[] = $action;

            if (!isset($permissionsByGroup[$feature])) {
                $permissionsByGroup[$feature] = [];
            }
            // Simpan permission utuh untuk pengecekan di view
            $permissionsByGroup[$feature][] = $permission->name;
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
