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
            'var_provinsi' => 'string|max:255|nullable',
            'var_kabupaten' => 'string|max:255',
            'var_kecamatan' => 'string|max:255|nullable',
            'var_kelurahan' => 'string|max:255|nullable',

            'var_nomor_permohonan' => 'string|max:255|nullable',
            'date_tanggal_permohonan' => 'nullable|date',
            'var_nomor_pengesahan' => 'string|max:255|nullable',
            'date_tanggal_pengesahan' => 'nullable|date',
            'text_catatan' => 'string|nullable',
            'var_url_lampiran' => 'string|max:255|nullable',
            'enum_status' => 'in:pending,approved,rejected',
            'pilihan_redaksi_ids' => 'array|nullable',

            'json_geometry' => 'string|nullable',

            'var_npwp_pemohon_atau_badan_usaha' => 'string|max:255|nullable',
            'var_jenis_kegiatan' => 'string|max:255|nullable',

            'var_fotocopy_ktp_attachment' => 'string|max:255|nullable',
            'var_fotocopy_npwp_attachment' => 'string|max:255|nullable',
            'var_foto_lokasi_rencana_kegiatan_attachment' => 'string|max:255|nullable',
            'var_titik_koordinat_attachment' => 'string|max:255|nullable',
            'var_sitr_attachment' => 'string|max:255|nullable',
            'var_lp2b_attachment' => 'string|max:255|nullable',
            'var_bukti_penguasaan_tanah_attachment' => 'string|max:255|nullable',
            'var_rencana_teknis_bangunan_attachment' => 'string|max:255|nullable',
            'var_ptp_kkpr_nonberusaha_attachment' => 'string|max:255|nullable',
            'var_akta_pendirian_badan_attachment' => 'string|max:255|nullable',

            'user_id' => 'integer|nullable|exists:users,id',
            'user_request_tte_id' => 'integer|nullable|exists:users,id',
            'request_tte_date' => 'nullable|date',
            'approved_date' => 'nullable|date',
            'var_penandatangan' => 'string|max:255|nullable',
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

            $rules['user_id'] = 'sometimes|' . $rules['user_id'];
            $rules['user_request_tte_id'] = 'sometimes|' . $rules['user_request_tte_id'];
            $rules['request_tte_date'] = 'sometimes|' . $rules['request_tte_date'];
            $rules['approved_date'] = 'sometimes|' . $rules['approved_date'];
            $rules['var_penandatangan'] = 'sometimes|' . $rules['var_penandatangan'];
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

            'var_provinsi.string' => 'var_provinsi harus berupa string.',
            'var_provinsi.max' => 'var_provinsi tidak boleh lebih dari 255 karakter.',
            'var_kabupaten.string' => 'var_kabupaten harus berupa string.',
            'var_kabupaten.max' => 'var_kabupaten tidak boleh lebih dari 255 karakter.',
            'var_kecamatan.string' => 'var_kecamatan harus berupa string.',
            'var_kecamatan.max' => 'var_kecamatan tidak boleh lebih dari 255 karakter.',
            'var_kelurahan.string' => 'var_kelurahan harus berupa string.',
            'var_kelurahan.max' => 'var_kelurahan tidak boleh lebih dari 255 karakter.',

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

            'user_id.integer' => 'user_id harus berupa angka.',
            'user_id.exists' => 'user_id tidak valid.',
            'user_request_tte_id.integer' => 'user_request_tte_id harus berupa angka.',
            'user_request_tte_id.exists' => 'user_request_tte_id tidak valid.',
            'request_tte_date.date' => 'request_tte_date harus berupa tanggal.',
            'approved_date.date' => 'approved_date harus berupa tanggal.',
            'var_penandatangan.string' => 'var_penandatangan harus berupa string.',
            'var_penandatangan.max' => 'var_penandatangan tidak boleh lebih dari 255 karakter.',
        ];
    }
}

