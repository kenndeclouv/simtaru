<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Handle user registration.
     */
    public function register(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed', // 'confirmed' butuh field 'password_confirmation'
        ]);

        // 2. Buat user baru
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // 3. Buat token untuk user yang baru daftar
        $token = $user->createToken('auth_token')->plainTextToken;

        // 4. Kasih response JSON
        return response()->json([
            'message'       => 'Registrasi berhasil',
            'access_token'  => $token,
            'token_type'    => 'Bearer',
            'user'          => $user,
        ], 201);
    }

    /**
     * Handle user login.
     */
    public function login(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 2. Coba otentikasi
        if (!Auth::attempt($request->only('email', 'password'))) {
            // Kalau gagal, kasih pesan error
            return response()->json([
                'message' => 'Email atau password salah'
            ], 401); // 401 Unauthorized
        }

        // 3. Kalau berhasil, ambil data user
        $user = User::where('email', $request->email)->firstOrFail();

        // 4. Buat token baru
        $token = $user->createToken('auth_token')->plainTextToken;

        // 5. Kasih response JSON
        return response()->json([
            'message'       => 'Login berhasil!',
            'access_token'  => $token,
            'token_type'    => 'Bearer',
            'user'          => $user,
        ]);
    }

    /**
     * Handle user logout.
     */
    public function logout(Request $request)
    {
        // Hapus token yang sedang dipakai untuk request ini
        $request->user()->currentAccessToken()->can('delete', $request->user());

        return response()->json([
            'message' => 'Berhasil logout'
        ]);
    }
}
