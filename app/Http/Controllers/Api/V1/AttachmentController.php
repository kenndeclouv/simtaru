<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validasi file yang masuk
        $request->validate([
            'attachment' => 'required|file|mimes:jpg,jpeg,png,pdf,docx|max:10240', // Maks 10MB
        ]);

        // 2. Simpan file ke direktori sementara
        // Pastikan kamu sudah menjalankan `php artisan storage:link`
        $path = $request->file('attachment')->store('tmp', 'public');

        // 3. Kembalikan response JSON berisi path file
        return response()->json([
            'success' => true,
            'path' => $path,
            'url' => Storage::url($path) // URL ini bisa untuk preview jika perlu
        ], 201);
    }
}