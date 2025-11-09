<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

class PermohonanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => new UserResource($this->whenLoaded('user')),
            'user_request_tte' => new UserResource($this->whenLoaded('userRequestTteBy')),
            'penandatangan' => $this->whenNotNull($this->var_penandatangan),
            'tipe_permohonan' => $this->var_type,
            'status' => $this->enum_status,
            // --- DATA UTAMA ---
            'nomor_permohonan' => $this->var_nomor_permohonan,
            'tanggal_permohonan' => $this->date_tanggal_permohonan,
            // Tampilkan hanya jika tidak null
            'nomor_pengesahan' => $this->whenNotNull($this->var_nomor_pengesahan),
            'tanggal_pengesahan' => $this->whenNotNull($this->date_tanggal_pengesahan),
            'catatan' => $this->whenNotNull($this->text_catatan),
            'lampiran_url' => $this->whenNotNull($this->var_url_lampiran),
            'sk' => [
                'nomor' => $this->whenNotNull($this->var_nomor_sk),
                'tanggal_terbit' => $this->whenNotNull($this->date_sk_terbit ? (string) $this->date_sk_terbit : null),
                'file_url' => $this->whenNotNull($this->var_sk_attachment),
            ],
            // --- DATA PEMOHON ---
            'pemohon' => [
                'nama' => $this->var_nama,
                'alamat' => $this->text_alamat,
                'email' => $this->var_email,
                'no_telp' => $this->whenNotNull($this->var_no_telp),
                'no_ponsel' => $this->whenNotNull($this->var_no_ponsel),
                // Tampilkan NIK hanya jika ada
                'nik' => $this->whenNotNull($this->var_nik),
            ],
            // --- DATA USAHA ---
            'usaha' => [
                'nama_usaha' => $this->var_nama_usaha,
                'alamat_usaha' => $this->text_alamat_usaha,
                'bentuk_usaha' => $this->whenNotNull($this->var_bentuk_usaha),
                'rencana_usaha' => $this->whenNotNull($this->var_rencana_usaha),
                'rencana_luas_lantai' => $this->whenNotNull($this->dec_rencana_luas_lantai),
                // kkpr
                'var_npwp_pemohon_atau_badan_usaha' => $this->whenNotNull($this->var_npwp_pemohon_atau_badan_usaha),
                'var_jenis_kegiatan' => $this->whenNotNull($this->var_jenis_kegiatan),
            ],

            'lokasi_pemohon' => [
                'provinsi' => $this->when($this->var_provinsi, fn() => ['id' => $this->var_provinsi, 'nama' => $this->nama_provinsi]),
                'kabupaten' => $this->when($this->var_kabupaten, fn() => ['id' => $this->var_kabupaten, 'nama' => $this->nama_kabupaten]),
                'kecamatan' => $this->when($this->var_kecamatan, fn() => ['id' => $this->var_kecamatan, 'nama' => $this->nama_kecamatan]),
                'kelurahan' => $this->when($this->var_kelurahan, fn() => ['id' => $this->var_kelurahan, 'nama' => $this->nama_kelurahan]),
            ],
            'lokasi_usaha' => [
                'kecamatan' => $this->when($this->var_kecamatan_usaha, fn() => ['id' => $this->var_kecamatan_usaha, 'nama' => $this->nama_kecamatan_usaha]),
                'kelurahan' => $this->when($this->var_kelurahan_usaha, fn() => ['id' => $this->var_kelurahan_usaha, 'nama' => $this->nama_kelurahan_usaha]),
            ],
            'geometri' => $this->whenNotNull(json_decode($this->json_geometry)),

            'template_dokumen' => TemplateDocResource::collection(
                $this->whenLoaded('permohonanTemplateDocs')
            ),
            'attachment' => [
                'fotocopy_ktp' => $this->whenNotNull($this->var_fotocopy_ktp_attachment),
                'fotocopy_npwp' => $this->whenNotNull($this->var_fotocopy_npwp_attachment),
                'foto_lokasi_rencana_kegiatan' => $this->whenNotNull($this->var_foto_lokasi_rencana_kegiatan_attachment),
                'titik_koordinat' => $this->whenNotNull($this->var_titik_koordinat_attachment),
                'sitr' => $this->whenNotNull($this->var_sitr_attachment),
                'lp2b' => $this->whenNotNull($this->var_lp2b_attachment),
                'bukti_penguasaan_tanah' => $this->whenNotNull($this->var_bukti_penguasaan_tanah_attachment),
                'rencana_teknis_bangunan' => $this->whenNotNull($this->var_rencana_teknis_bangunan_attachment),
                'ptp_kkpr_nonberusaha' => $this->whenNotNull($this->var_ptp_kkpr_nonberusaha_attachment),
                'akta_pendirian_badan' => $this->whenNotNull($this->var_akta_pendirian_badan_attachment),
            ],
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
            'request_tte_date' => $this->request_tte_date ? (string) $this->request_tte_date : null,
            'approved_date' => $this->approved_date ? (string) $this->approved_date : null,
        ];
    }
}
