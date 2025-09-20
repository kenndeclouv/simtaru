<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PermohonanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'id' => $this->id,
            'nomor_permohonan' => $this->var_nomor_permohonan,
            'tanggal_permohonan' => $this->date_tanggal_permohonan,
            'nomor_pengesahan' => $this->var_nomor_pengesahan,
            'tanggal_pengesahan' => $this->date_tanggal_pengesahan,
            'status' => $this->enum_status,
            'catatan' => $this->text_catatan,
            'lampiran_url' => $this->var_url_lampiran,
            'geometri' => json_decode($this->json_geometry),

            'data_pemohon' => [
                'nik' => $this->var_nik,
                'nama' => $this->var_nama,
                'alamat' => $this->text_alamat,
                'provinsi_id' => $this->var_provinsi,
                'nama_provinsi' => $this->nama_provinsi,
                'kabupaten_id' => $this->var_kabupaten,
                'nama_kabupaten' => $this->nama_kabupaten,
                'kecamatan_id' => $this->var_kecamatan,
                'nama_kecamatan' => $this->nama_kecamatan,
                'kelurahan_id' => $this->var_kelurahan,
                'nama_kelurahan' => $this->nama_kelurahan,
                'email' => $this->var_email,
                'no_telp' => $this->var_no_telp,
                'no_ponsel' => $this->var_no_ponsel,
            ],

            'data_usaha' => [
                'nama_usaha' => $this->var_nama_usaha,
                'bentuk_usaha' => $this->var_bentuk_usaha,
                'alamat_usaha' => $this->text_alamat_usaha,
                'kecamatan_usaha_id' => $this->var_kecamatan_usaha,
                'nama_kecamatan_usaha' => $this->nama_kecamatan_usaha,
                'kelurahan_usaha_id' => $this->var_kelurahan_usaha,
                'nama_kelurahan_usaha' => $this->nama_kelurahan_usaha,
                'rencana_usaha' => $this->var_rencana_usaha,
                'rencana_luas_lantai' => (float) $this->dec_rencana_luas_lantai,
            ],
            
            'template_dokumen' => TemplateDocResource::collection($this->whenLoaded('templateDocs')),
            
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}

