<?php

/**
 * File ini dibuat secara otomatis oleh perintah MakeFormRequest / make:form-req.
 * Kamu dapat memodifikasi file ini.
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class UserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(Request $request)
    {
        $userId = $request->route('user')?->id ?? $request->user()?->id ?? 'NULL';

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $userId,
            'password' => 'nullable|min:8|confirmed|regex:/^(?=.*[A-Z]).+$/',
            'role' => 'required|exists:roles,name',
        ];

        if ($request->method() === 'PUT' || $request->method() === 'PATCH') {
            $rules['name'] = 'sometimes|string|max:255';
            $rules['email'] = 'nullable|email';
            $rules['password'] = 'nullable|min:8|confirmed|regex:/^(?=.*[A-Z]).+$/';
            $rules['role'] = 'nullable|exists:roles,name';
        }
        return $rules;
    }

    public function messages()
    {
        return [
            'role_id.integer' => 'role_id harus berupa integer.',
            'role_id.exists' => 'Pilihan role_id tidak valid.',
            'username.string' => 'username harus berupa string.',
            'username.max' => 'username tidak boleh lebih dari 255 karakter.',
            'username.required' => 'username harus diisi.',
            'username.unique' => 'username telah digunakan.',
            'name.string' => 'name harus berupa string.',
            'name.max' => 'name tidak boleh lebih dari 255 karakter.',
            'name.required' => 'name harus diisi.',
            'email.string' => 'email harus berupa string.',
            'email.max' => 'email tidak boleh lebih dari 255 karakter.',
            'email.required' => 'email harus diisi.',
            'email.unique' => 'email telah digunakan.',
            'password.string' => 'password harus berupa string.',
            'password.max' => 'password tidak boleh lebih dari 255 karakter.',
            'password.required' => 'password harus diisi.',
            'photo.string' => 'photo harus berupa string.',
            'is_active.integer' => 'is_active harus berupa integer.',
            'remember_token.string' => 'remember_token harus berupa string.',
            'remember_token.max' => 'remember_token tidak boleh lebih dari 100 karakter.',
        ];
    }
}
