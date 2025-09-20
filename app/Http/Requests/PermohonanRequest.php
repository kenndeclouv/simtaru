<?php

/**
 * File ini dibuat secara otomatis oleh perintah MakeFormRequest / make:form-req.
 * Kamu dapat memodifikasi file ini.
 */
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PermohonanRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'var_type' => 'string|max:255',
            'var_nik' => 'string|max:20',
            'var_nama' => 'string|max:255|required',
            'text_alamat' => 'string|required',
            'var_provinsi' => 'string|max:255|nullable',
            'var_kabupaten' => 'string|max:255',
            'var_kecamatan' => 'string|max:255|nullable',
            'var_kelurahan' => 'string|max:255|nullable',
            'var_email' => 'string|max:255',
            'var_no_telp' => 'string|max:255|nullable',
            'var_no_ponsel' => 'string|max:255|nullable',
            'var_nama_usaha' => 'string|max:255|required',
            'var_bentuk_usaha' => 'string|max:255|nullable',
            'text_alamat_usaha' => 'string|required',
            'var_kecamatan_usaha' => 'string|max:255',
            'var_kelurahan_usaha' => 'string|max:255',
            'var_rencana_usaha' => 'string|max:255|nullable',
            'dec_rencana_luas_lantai' => 'nullable|numeric',
            'json_geometry' => 'string|nullable',
            'var_nomor_permohonan' => 'string|max:255|nullable',
            'date_tanggal_permohonan' => 'nullable|date',
            'var_nomor_pengesahan' => 'string|max:255|nullable',
            'date_tanggal_pengesahan' => 'nullable|date',
            'text_catatan' => 'string|nullable',
            'var_url_lampiran' => 'string|max:255|nullable',
            'enum_status' => 'in:pending,approved,rejected',
            'pilihan_redaksi_ids' => 'array|nullable',
        ];
    }

    public function messages()
    {
        return [
            'var_type.string' => 'var_type harus berupa string.',
            'var_type.max' => 'var_type tidak boleh lebih dari 255 karakter.',
            'var_nik.string' => 'var_nik harus berupa string.',
            'var_nik.max' => 'var_nik tidak boleh lebih dari 20 karakter.',
            'var_nama.string' => 'var_nama harus berupa string.',
            'var_nama.max' => 'var_nama tidak boleh lebih dari 255 karakter.',
            'var_nama.required' => 'var_nama harus diisi.',
            'text_alamat.string' => 'text_alamat harus berupa string.',
            'text_alamat.required' => 'text_alamat harus diisi.',
            'var_provinsi.string' => 'var_provinsi harus berupa string.',
            'var_provinsi.max' => 'var_provinsi tidak boleh lebih dari 255 karakter.',
            'var_kabupaten.string' => 'var_kabupaten harus berupa string.',
            'var_kabupaten.max' => 'var_kabupaten tidak boleh lebih dari 255 karakter.',
            'var_kecamatan.string' => 'var_kecamatan harus berupa string.',
            'var_kecamatan.max' => 'var_kecamatan tidak boleh lebih dari 255 karakter.',
            'var_kelurahan.string' => 'var_kelurahan harus berupa string.',
            'var_kelurahan.max' => 'var_kelurahan tidak boleh lebih dari 255 karakter.',
            'var_email.string' => 'var_email harus berupa string.',
            'var_email.max' => 'var_email tidak boleh lebih dari 255 karakter.',
            'var_no_telp.string' => 'var_no_telp harus berupa string.',
            'var_no_telp.max' => 'var_no_telp tidak boleh lebih dari 255 karakter.',
            'var_no_ponsel.string' => 'var_no_ponsel harus berupa string.',
            'var_no_ponsel.max' => 'var_no_ponsel tidak boleh lebih dari 255 karakter.',
            'var_nama_usaha.string' => 'var_nama_usaha harus berupa string.',
            'var_nama_usaha.max' => 'var_nama_usaha tidak boleh lebih dari 255 karakter.',
            'var_nama_usaha.required' => 'var_nama_usaha harus diisi.',
            'var_bentuk_usaha.string' => 'var_bentuk_usaha harus berupa string.',
            'var_bentuk_usaha.max' => 'var_bentuk_usaha tidak boleh lebih dari 255 karakter.',
            'text_alamat_usaha.string' => 'text_alamat_usaha harus berupa string.',
            'text_alamat_usaha.required' => 'text_alamat_usaha harus diisi.',
            'var_kecamatan_usaha.string' => 'var_kecamatan_usaha harus berupa string.',
            'var_kecamatan_usaha.max' => 'var_kecamatan_usaha tidak boleh lebih dari 255 karakter.',
            'var_kelurahan_usaha.string' => 'var_kelurahan_usaha harus berupa string.',
            'var_kelurahan_usaha.max' => 'var_kelurahan_usaha tidak boleh lebih dari 255 karakter.',
            'var_rencana_usaha.string' => 'var_rencana_usaha harus berupa string.',
            'var_rencana_usaha.max' => 'var_rencana_usaha tidak boleh lebih dari 255 karakter.',
            'json_geometry.string' => 'json_geometry harus berupa string.',
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