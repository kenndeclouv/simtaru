<?php

/**
 * File ini dibuat secara otomatis oleh perintah MakeFormRequest / make:form-req.
 * Kamu dapat memodifikasi file ini.
 */

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

/**
 * @mixin \Illuminate\Http\Request
 */
class KkprRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(Request $request)
    {
        $rules = [
            'var_type' => 'string|max:255',

            'var_nama' => 'string|max:255',
            'text_alamat' => 'string',
            'var_email' => 'string|max:255',
            'var_no_telp' => 'string|max:255|nullable',
            'var_nama_usaha' => 'string|max:255',

            'text_alamat_usaha' => 'string|required',

            'var_nomor_permohonan' => 'string|max:255|nullable',
            'date_tanggal_permohonan' => 'nullable|date',
            'var_nomor_pengesahan' => 'string|max:255|nullable',
            'date_tanggal_pengesahan' => 'nullable|date',
            'text_catatan' => 'string|nullable',
            'var_url_lampiran' => 'string|max:255|nullable',
            'enum_status' => 'in:pending,approved,rejected',
            'pilihan_redaksi_ids' => 'array|nullable',
        ];

        // Jika methodnya POST (create), tambahkan aturan 'required'
        // if ($this->isMethod('POST')) {
        if ($request->method() === "POST") {
            $rules['var_nama'] .= '|required';
            $rules['text_alamat'] .= '|required';
            $rules['var_nama_usaha'] .= '|required';
            $rules['text_alamat_usaha'] .= '|required';
        }

        // Jika methodnya PUT atau PATCH (update), gunakan 'sometimes'
        // if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
        if ($request->method() === 'PUT' || $request->method() === 'PATCH') {
            // 'sometimes' berarti: jika field ini dikirim, maka validasi
            // 'required' harus dipenuhi. Jika tidak dikirim, abaikan saja.
            $rules['var_nama'] = 'sometimes|' . $rules['var_nama'];
            $rules['text_alamat'] = 'sometimes|' . $rules['text_alamat'];
            $rules['var_nama_usaha'] = 'sometimes|' . $rules['var_nama_usaha'];
            $rules['text_alamat_usaha'] = 'sometimes|' . $rules['text_alamat_usaha'];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'var_type.string' => 'var_type harus berupa string.',
            'var_type.max' => 'var_type tidak boleh lebih dari 255 karakter.',

            'var_nama.string' => 'var_nama harus berupa string.',
            'var_nama.max' => 'var_nama tidak boleh lebih dari 255 karakter.',
            'var_nama.required' => 'var_nama harus diisi.',
            'text_alamat.string' => 'text_alamat harus berupa string.',
            'text_alamat.required' => 'text_alamat harus diisi.',

            'var_email.string' => 'var_email harus berupa string.',
            'var_email.max' => 'var_email tidak boleh lebih dari 255 karakter.',
            'var_no_telp.string' => 'var_no_telp harus berupa string.',
            'var_no_telp.max' => 'var_no_telp tidak boleh lebih dari 255 karakter.',

            'var_nama_usaha.string' => 'var_nama_usaha harus berupa string.',
            'var_nama_usaha.max' => 'var_nama_usaha tidak boleh lebih dari 255 karakter.',
            'var_nama_usaha.required' => 'var_nama_usaha harus diisi.',

            'text_alamat_usaha.string' => 'text_alamat_usaha harus berupa string.',
            'text_alamat_usaha.required' => 'text_alamat_usaha harus diisi.',

            'var_nomor_permohonan.string' => 'var_nomor_permohonan harus berupa string.',
            'var_nomor_permohonan.max' => 'var_nomor_permohonan tidak boleh lebih dari 255 karakter.',
            'var_nomor_pengesahan.string' => 'var_nomor_pengesahan harus berupa string.',
            'var_nomor_pengesahan.max' => 'var_nomor_pengesahan tidak boleh lebih dari 255 karakter.',
            'text_catatan.string' => 'text_catatan harus berupa string.',
            'var_url_lampiran.string' => 'var_url_lampiran harus berupa string.',
            'var_url_lampiran.max' => 'var_url_lampiran tidak boleh lebih dari 255 karakter.',
            'enum_status.in' => 'Pilihan enum_status tidak valid.',
            'pilihan_redaksi_ids.array' => 'pilihan_redaksi_ids harus berupa array.',
            'pilihan_redaksi_ids.nullable' => 'pilihan_redaksi_ids boleh diisi.',
        ];
    }
}
