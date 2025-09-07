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
            'var_nik' => 'string|max:20|required',
            'var_nama' => 'string|max:255|required',
            'text_alamat' => 'string|required',
            'var_kabupaten' => 'string|max:255|required',
            'var_kecamatan' => 'string|max:255|required',
            'var_kelurahan' => 'string|max:255|required',
            'var_email' => 'string|max:255|required',
            'var_no_telp' => 'string|max:255',
            'var_no_ponsel' => 'string|max:255',
            'var_nama_usaha' => 'string|max:255|required',
            'var_bentuk_usaha' => 'string|max:255|required',
            'text_alamat_usaha' => 'string|required',
            'var_kecamatan_usaha' => 'string|max:255|required',
            'var_kelurahan_usaha' => 'string|max:255|required',
            'var_rencana_usaha' => 'string|max:255|required',
            'dec_rencana_luas_lantai' => '',
            'json_geometry' => 'required',
            'date_tanggal_permohonan' => 'required',
            'pilihan_redaksi_ids' => 'array|required',
        ];
    }

    public function messages()
    {
        return [
            'var_nik.string' => 'var_nik harus berupa string.',
            'var_nik.max' => 'var_nik tidak boleh lebih dari 20 karakter.',
            'var_nik.required' => 'var_nik harus diisi.',
            'var_nama.string' => 'var_nama harus berupa string.',
            'var_nama.max' => 'var_nama tidak boleh lebih dari 255 karakter.',
            'var_nama.required' => 'var_nama harus diisi.',
            'text_alamat.string' => 'text_alamat harus berupa string.',
            'text_alamat.required' => 'text_alamat harus diisi.',
            'var_kabupaten.string' => 'var_kabupaten harus berupa string.',
            'var_kabupaten.max' => 'var_kabupaten tidak boleh lebih dari 255 karakter.',
            'var_kabupaten.required' => 'var_kabupaten harus diisi.',
            'var_kecamatan.string' => 'var_kecamatan harus berupa string.',
            'var_kecamatan.max' => 'var_kecamatan tidak boleh lebih dari 255 karakter.',
            'var_kecamatan.required' => 'var_kecamatan harus diisi.',
            'var_kelurahan.string' => 'var_kelurahan harus berupa string.',
            'var_kelurahan.max' => 'var_kelurahan tidak boleh lebih dari 255 karakter.',
            'var_kelurahan.required' => 'var_kelurahan harus diisi.',
            'var_email.string' => 'var_email harus berupa string.',
            'var_email.max' => 'var_email tidak boleh lebih dari 255 karakter.',
            'var_email.required' => 'var_email harus diisi.',
            'var_no_telp.string' => 'var_no_telp harus berupa string.',
            'var_no_telp.max' => 'var_no_telp tidak boleh lebih dari 255 karakter.',
            'var_no_ponsel.string' => 'var_no_ponsel harus berupa string.',
            'var_no_ponsel.max' => 'var_no_ponsel tidak boleh lebih dari 255 karakter.',
            'var_nama_usaha.string' => 'var_nama_usaha harus berupa string.',
            'var_nama_usaha.max' => 'var_nama_usaha tidak boleh lebih dari 255 karakter.',
            'var_nama_usaha.required' => 'var_nama_usaha harus diisi.',
            'var_bentuk_usaha.string' => 'var_bentuk_usaha harus berupa string.',
            'var_bentuk_usaha.max' => 'var_bentuk_usaha tidak boleh lebih dari 255 karakter.',
            'var_bentuk_usaha.required' => 'var_bentuk_usaha harus diisi.',
            'text_alamat_usaha.string' => 'text_alamat_usaha harus berupa string.',
            'text_alamat_usaha.required' => 'text_alamat_usaha harus diisi.',
            'var_kecamatan_usaha.string' => 'var_kecamatan_usaha harus berupa string.',
            'var_kecamatan_usaha.max' => 'var_kecamatan_usaha tidak boleh lebih dari 255 karakter.',
            'var_kecamatan_usaha.required' => 'var_kecamatan_usaha harus diisi.',
            'var_kelurahan_usaha.string' => 'var_kelurahan_usaha harus berupa string.',
            'var_kelurahan_usaha.max' => 'var_kelurahan_usaha tidak boleh lebih dari 255 karakter.',
            'var_kelurahan_usaha.required' => 'var_kelurahan_usaha harus diisi.',
            'var_rencana_usaha.string' => 'var_rencana_usaha harus berupa string.',
            'var_rencana_usaha.max' => 'var_rencana_usaha tidak boleh lebih dari 255 karakter.',
            'var_rencana_usaha.required' => 'var_rencana_usaha harus diisi.',
            'json_geometry.string' => 'json_geometry harus berupa string.',
            'json_geometry.required' => 'json_geometry harus diisi.',
            'var_nomor_permohonan.string' => 'var_nomor_permohonan harus berupa string.',
            'var_nomor_permohonan.max' => 'var_nomor_permohonan tidak boleh lebih dari 255 karakter.',
            'var_nomor_permohonan.required' => 'var_nomor_permohonan harus diisi.',
            'date_tanggal_permohonan.required' => 'date_tanggal_permohonan harus diisi.',
            'var_nomor_pengesahan.string' => 'var_nomor_pengesahan harus berupa string.',
            'var_nomor_pengesahan.max' => 'var_nomor_pengesahan tidak boleh lebih dari 255 karakter.',
            'pilihan_redaksi_ids.array' => 'pilihan_redaksi_ids harus berupa array.',
            'pilihan_redaksi_ids.required' => 'pilihan_redaksi_ids harus diisi.',
        ];
    }
}
