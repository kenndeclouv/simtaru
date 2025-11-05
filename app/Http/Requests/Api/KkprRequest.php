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

        if ($request->method() === "POST") {
            $rules['var_nama'] .= '|required';
            $rules['text_alamat'] .= '|required';
            $rules['var_nama_usaha'] .= '|required';
            $rules['text_alamat_usaha'] .= '|required';
        }

        if ($request->method() === 'PUT' || $request->method() === 'PATCH') {
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

            'var_nik.string' => 'var_nik harus berupa string.',
            'var_nik.max' => 'var_nik tidak boleh lebih dari 20 karakter.',

            'var_nama.string' => 'var_nama harus berupa string.',
            'var_nama.max' => 'var_nama tidak boleh lebih dari 255 karakter.',
            'var_nama.required' => 'var_nama harus diisi.',
            'var_nama.sometimes' => 'var_nama harus diisi jika dikirim.',

            'text_alamat.string' => 'text_alamat harus berupa string.',
            'text_alamat.required' => 'text_alamat harus diisi.',
            'text_alamat.sometimes' => 'text_alamat harus diisi jika dikirim.',

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
            'var_nama_usaha.sometimes' => 'var_nama_usaha harus diisi jika dikirim.',

            'var_bentuk_usaha.string' => 'var_bentuk_usaha harus berupa string.',
            'var_bentuk_usaha.max' => 'var_bentuk_usaha tidak boleh lebih dari 255 karakter.',

            'text_alamat_usaha.string' => 'text_alamat_usaha harus berupa string.',
            'text_alamat_usaha.required' => 'text_alamat_usaha harus diisi.',
            'text_alamat_usaha.sometimes' => 'text_alamat_usaha harus diisi jika dikirim.',

            'var_kecamatan_usaha.string' => 'var_kecamatan_usaha harus berupa string.',
            'var_kecamatan_usaha.max' => 'var_kecamatan_usaha tidak boleh lebih dari 255 karakter.',

            'var_kelurahan_usaha.string' => 'var_kelurahan_usaha harus berupa string.',
            'var_kelurahan_usaha.max' => 'var_kelurahan_usaha tidak boleh lebih dari 255 karakter.',

            'var_rencana_usaha.string' => 'var_rencana_usaha harus berupa string.',
            'var_rencana_usaha.max' => 'var_rencana_usaha tidak boleh lebih dari 255 karakter.',

            'dec_rencana_luas_lantai.numeric' => 'dec_rencana_luas_lantai harus berupa angka.',

            'json_geometry.string' => 'json_geometry harus berupa string.',

            'var_nomor_permohonan.string' => 'var_nomor_permohonan harus berupa string.',
            'var_nomor_permohonan.max' => 'var_nomor_permohonan tidak boleh lebih dari 255 karakter.',

            'date_tanggal_permohonan.date' => 'date_tanggal_permohonan harus berupa tanggal.',

            'var_nomor_pengesahan.string' => 'var_nomor_pengesahan harus berupa string.',
            'var_nomor_pengesahan.max' => 'var_nomor_pengesahan tidak boleh lebih dari 255 karakter.',

            'date_tanggal_pengesahan.date' => 'date_tanggal_pengesahan harus berupa tanggal.',

            'text_catatan.string' => 'text_catatan harus berupa string.',

            'var_url_lampiran.string' => 'var_url_lampiran harus berupa string.',
            'var_url_lampiran.max' => 'var_url_lampiran tidak boleh lebih dari 255 karakter.',

            'enum_status.in' => 'Pilihan enum_status tidak valid.',

            'pilihan_redaksi_ids.array' => 'pilihan_redaksi_ids harus berupa array.',
            'pilihan_redaksi_ids.nullable' => 'pilihan_redaksi_ids boleh diisi.',

            'var_npwp_pemohon_atau_badan_usaha.string' => 'var_npwp_pemohon_atau_badan_usaha harus berupa string.',
            'var_npwp_pemohon_atau_badan_usaha.max' => 'var_npwp_pemohon_atau_badan_usaha tidak boleh lebih dari 255 karakter.',

            'var_jenis_kegiatan.string' => 'var_jenis_kegiatan harus berupa string.',
            'var_jenis_kegiatan.max' => 'var_jenis_kegiatan tidak boleh lebih dari 255 karakter.',

            'var_fotocopy_ktp_attachment.string' => 'var_fotocopy_ktp_attachment harus berupa string.',
            'var_fotocopy_ktp_attachment.max' => 'var_fotocopy_ktp_attachment tidak boleh lebih dari 255 karakter.',

            'var_fotocopy_npwp_attachment.string' => 'var_fotocopy_npwp_attachment harus berupa string.',
            'var_fotocopy_npwp_attachment.max' => 'var_fotocopy_npwp_attachment tidak boleh lebih dari 255 karakter.',

            'var_foto_lokasi_rencana_kegiatan_attachment.string' => 'var_foto_lokasi_rencana_kegiatan_attachment harus berupa string.',
            'var_foto_lokasi_rencana_kegiatan_attachment.max' => 'var_foto_lokasi_rencana_kegiatan_attachment tidak boleh lebih dari 255 karakter.',

            'var_titik_koordinat_attachment.string' => 'var_titik_koordinat_attachment harus berupa string.',
            'var_titik_koordinat_attachment.max' => 'var_titik_koordinat_attachment tidak boleh lebih dari 255 karakter.',

            'var_sitr_attachment.string' => 'var_sitr_attachment harus berupa string.',
            'var_sitr_attachment.max' => 'var_sitr_attachment tidak boleh lebih dari 255 karakter.',

            'var_lp2b_attachment.string' => 'var_lp2b_attachment harus berupa string.',
            'var_lp2b_attachment.max' => 'var_lp2b_attachment tidak boleh lebih dari 255 karakter.',

            'var_bukti_penguasaan_tanah_attachment.string' => 'var_bukti_penguasaan_tanah_attachment harus berupa string.',
            'var_bukti_penguasaan_tanah_attachment.max' => 'var_bukti_penguasaan_tanah_attachment tidak boleh lebih dari 255 karakter.',

            'var_rencana_teknis_bangunan_attachment.string' => 'var_rencana_teknis_bangunan_attachment harus berupa string.',
            'var_rencana_teknis_bangunan_attachment.max' => 'var_rencana_teknis_bangunan_attachment tidak boleh lebih dari 255 karakter.',

            'var_ptp_kkpr_nonberusaha_attachment.string' => 'var_ptp_kkpr_nonberusaha_attachment harus berupa string.',
            'var_ptp_kkpr_nonberusaha_attachment.max' => 'var_ptp_kkpr_nonberusaha_attachment tidak boleh lebih dari 255 karakter.',

            'var_akta_pendirian_badan_attachment.string' => 'var_akta_pendirian_badan_attachment harus berupa string.',
            'var_akta_pendirian_badan_attachment.max' => 'var_akta_pendirian_badan_attachment tidak boleh lebih dari 255 karakter.',

            'user_id.integer' => 'user_id harus berupa angka.',
            'user_id.exists' => 'user_id tidak valid.',
            'user_id.sometimes' => 'user_id harus diisi jika dikirim.',

            'user_request_tte_id.integer' => 'user_request_tte_id harus berupa angka.',
            'user_request_tte_id.exists' => 'user_request_tte_id tidak valid.',
            'user_request_tte_id.sometimes' => 'user_request_tte_id harus diisi jika dikirim.',

            'request_tte_date.date' => 'request_tte_date harus berupa tanggal.',
            'request_tte_date.sometimes' => 'request_tte_date harus diisi jika dikirim.',

            'approved_date.date' => 'approved_date harus berupa tanggal.',
            'approved_date.sometimes' => 'approved_date harus diisi jika dikirim.',

            'var_penandatangan.string' => 'var_penandatangan harus berupa string.',
            'var_penandatangan.max' => 'var_penandatangan tidak boleh lebih dari 255 karakter.',
            'var_penandatangan.sometimes' => 'var_penandatangan harus diisi jika dikirim.',
        ];
    }
}

