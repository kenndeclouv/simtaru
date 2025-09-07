<?php

namespace App\Http\Controllers;

use App\Models\KeyStorage;
use Illuminate\Http\Request;

class KeyStorageController extends Controller
{
    public function __construct()
    {
        // User harus login untuk semua method
        $this->middleware('auth');

        // Terapkan permission spesifik untuk setiap method
        $this->middleware('can:view key-storage')->only(['index', 'show']);
        $this->middleware('can:create key-storage')->only(['create', 'store']);
        $this->middleware('can:edit key-storage')->only(['edit', 'update']);
        $this->middleware('can:delete key-storage')->only(['destroy']);
    }

    public function index()
    {
        $keyStorages = KeyStorage::all();
        return view('key_storage.index', compact('keyStorages'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'var_key' => 'required|string|max:255|unique:key_storages,var_key',
            'var_value' => 'required|string|max:255',
            'var_description' => 'nullable|string|max:255',
        ]);

        KeyStorage::create($validated);

        return redirect()->route('key-storages.index')->with('success', 'Key Storage berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $keyStorage = KeyStorage::findOrFail($id);
        return view('key_storage.edit', compact('keyStorage'));
    }

    public function update(Request $request, $id)
    {
        $keyStorage = KeyStorage::findOrFail($id);

        $validated = $request->validate([
            'var_key' => 'required|string|max:255|unique:key_storages,var_key,' . $keyStorage->id,
            'var_value' => 'required|string|max:255',
            'var_description' => 'nullable|string|max:255',
        ]);

        $keyStorage->update($validated);

        return redirect()->route('key-storages.index')->with('success', 'Key Storage berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $keyStorage = KeyStorage::findOrFail($id);
        $keyStorage->delete();

        return redirect()->route('key-storages.index')->with('success', 'Key Storage berhasil dihapus.');
    }
}
