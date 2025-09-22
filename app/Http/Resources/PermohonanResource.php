<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PermohonanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tipe_permohonan' => $this->var_type, // Tambahkan ini agar jelas
            'status' => $this->enum_status,

            // --- DATA UTAMA ---
            'nomor_permohonan' => $this->var_nomor_permohonan,
            'tanggal_permohonan' => $this->date_tanggal_permohonan,
            
            // Tampilkan hanya jika tidak null
            'nomor_pengesahan' => $this->whenNotNull($this->var_nomor_pengesahan),
            'tanggal_pengesahan' => $this->whenNotNull($this->date_tanggal_pengesahan),
            'catatan' => $this->whenNotNull($this->text_catatan),
            'lampiran_url' => $this->whenNotNull($this->var_url_lampiran),

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
                // Tampilkan hanya jika ada
                'bentuk_usaha' => $this->whenNotNull($this->var_bentuk_usaha),
                'rencana_usaha' => $this->whenNotNull($this->var_rencana_usaha),
                'rencana_luas_lantai' => $this->whenNotNull($this->dec_rencana_luas_lantai),
            ],
            
            // GABUNGKAN DATA LOKASI & GEOMETRI HANYA JIKA TIPE-NYA sitr/rdtr
            $this->mergeWhen($this->var_type === 'sitr/rdtr', [
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
            ]),

            'template_dokumen' => TemplateDocResource::collection($this->whenLoaded('templateDocs')),
            
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}