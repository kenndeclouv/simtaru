<?php

// app/Http/Controllers/RoleController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function __construct()
    {
        // User harus login untuk semua method
        $this->middleware('auth');

        // Terapkan permission spesifik untuk setiap method
        $this->middleware('can:view role')->only(['index', 'show']);
        $this->middleware('can:create role')->only(['create', 'store']);
        $this->middleware('can:edit role')->only(['edit', 'update']);
        $this->middleware('can:delete role')->only(['destroy']);
    }

    public function index()
    {
        $roles = Role::all()->except([1]);
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        // 1. Ambil semua permission
        $permissions = Permission::all();

        // 2. Definisikan aksi standar untuk kolom tabel
        $actions = ['view', 'create', 'edit', 'delete'];

        // 3. Kelompokkan permission berdasarkan fitur
        $permissionsByGroup = [];
        foreach ($permissions as $permission) {
            // Pecah nama permission, contoh: "view permohonan" -> $parts[0]="view", $parts[1]="permohonan"
            $parts = explode(' ', $permission->name, 2);
            $feature = $parts[1]; // Nama fitur
            $action = $parts[0];  // Nama aksi

            if (!isset($permissionsByGroup[$feature])) {
                $permissionsByGroup[$feature] = [];
            }

            $permissionsByGroup[$feature][] = $action;
        }

        return view('roles.create', compact('actions', 'permissionsByGroup'));
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'nullable|array', // permissions bisa jadi tidak dipilih sama sekali
        ]);

        // Buat role baru
        $role = Role::create(['name' => $request->name]);

        // Sync permissions yang dipilih dari form
        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('roles.index')->with('success', 'Role berhasil dibuat!');
    }

    public function edit(Role $role)
    {
        // 1. Ambil semua permission
        $permissions = Permission::all();

        // 2. Definisikan aksi standar untuk kolom tabel
        $actions = ['view', 'create', 'edit', 'delete'];

        // 3. Kelompokkan permission berdasarkan fitur
        $permissionsByGroup = [];
        foreach ($permissions as $permission) {
            // Pecah nama permission, contoh: "view permohonan" -> $parts[0]="view", $parts[1]="permohonan"
            $parts = explode(' ', $permission->name, 2);
            $feature = $parts[1]; // Nama fitur
            $action = $parts[0];  // Nama aksi

            if (!isset($permissionsByGroup[$feature])) {
                $permissionsByGroup[$feature] = [];
            }

            $permissionsByGroup[$feature][] = $action;
        }

        return view('roles.edit', compact('actions', 'permissionsByGroup', 'role'));
    }
    public function update(Role $role, Request $request)
    {
        // Validasi input
        $request->validate([
            'name' => 'sometimes|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array', // permissions bisa jadi tidak dipilih sama sekali
        ]);

        // Buat role baru
        $role->update(['name' => $request->name ?? $role->name]);

        // Sync permissions yang dipilih dari form
        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('roles.index')->with('success', 'Role berhasil diupdate!');
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->route('roles.index')->with('success', 'Role berhasil dihapus!');
    }
}
