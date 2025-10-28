<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Permohonan extends Model
{
    use LogsActivity;

    protected $guarded = ['id'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['var_nama', 'enum_status', 'text_catatan'])  // Catat perubahan hanya di kolom ini
            ->setDescriptionForEvent(fn(string $eventName) => "Permohonan {$this->var_nama} telah di-{$eventName}")  // Deskripsi log
            ->useLogName('Permohonan')  // Nama grup log
            ->logOnlyDirty();  // Hanya catat jika ada perubahan
    }

    public function templateDocs()
    {
        return $this
            ->belongsToMany(TemplateDocs::class, 'permohonans_template_docs', 'fk_permohonan_id', 'fk_template_docs_id')
            ->withPivot('var_generated_file_path')  // <-- PENTING!
            ->withTimestamps();
    }

    // Location relationships
    public function province(): BelongsTo
    {
        return $this
            ->belongsTo(Province::class, 'var_provinsi', 'id')
            ->withDefault(function ($province, $permohonan) {
                $province->name = $permohonan->var_provinsi;
            });
    }

    public function regency(): BelongsTo
    {
        return $this
            ->belongsTo(Regency::class, 'var_kabupaten', 'id')
            ->withDefault(function ($regency, $permohonan) {
                $regency->name = $permohonan->var_kabupaten;
            });
    }

    public function district(): BelongsTo
    {
        return $this
            ->belongsTo(District::class, 'var_kecamatan', 'id')
            ->withDefault(function ($district, $permohonan) {
                $district->name = $permohonan->var_kecamatan;
            });
    }

    public function village(): BelongsTo
    {
        return $this
            ->belongsTo(Village::class, 'var_kelurahan', 'id')
            ->withDefault(function ($village, $permohonan) {
                $village->name = $permohonan->var_kelurahan;
            });
    }

    public function districtUsaha(): BelongsTo
    {
        return $this
            ->belongsTo(District::class, 'var_kecamatan_usaha', 'id')
            ->withDefault(function ($district, $permohonan) {
                $district->name = $permohonan->var_kecamatan_usaha;
            });
    }

    public function villageUsaha(): BelongsTo
    {
        return $this
            ->belongsTo(Village::class, 'var_kelurahan_usaha', 'id')
            ->withDefault(function ($village, $permohonan) {
                $village->name = $permohonan->var_kelurahan_usaha;
            });
    }

    // Accessor methods for backward compatibility
    public function getNamaProvinsiAttribute()
    {
        return $this->province->name;  // Langsung aja!
    }

    public function getNamaKabupatenAttribute()
    {
        return $this->regency->name;
    }

    public function getNamaKecamatanAttribute()
    {
        return $this->district->name;
    }

    public function getNamaKelurahanAttribute()
    {
        return $this->village->name;
    }

    public function getNamaKecamatanUsahaAttribute()
    {
        return $this->districtUsaha->name;
    }

    public function getNamaKelurahanUsahaAttribute()
    {
        return $this->villageUsaha->name;
    }

    protected function generateAttachmentUrl($value)
    {
        if (empty($value)) {
            return null;
        }
        if (str_starts_with($value, 'http')) {
            return $value;
        }
        return asset('storage/' . $value);
    }

    public function getVarFotocopyKtpAttachmentAttribute()
    {
        return $this->generateAttachmentUrl($this->attributes['var_fotocopy_ktp_attachment'] ?? null);
    }

    public function getVarFotocopyNpwpAttachmentAttribute()
    {
        return $this->generateAttachmentUrl($this->attributes['var_fotocopy_npwp_attachment'] ?? null);
    }

    public function getVarFotoLokasiRencanaKegiatanAttachmentAttribute()
    {
        return $this->generateAttachmentUrl($this->attributes['var_foto_lokasi_rencana_kegiatan_attachment'] ?? null);
    }

    public function getVarTitikKoordinatAttachmentAttribute()
    {
        return $this->generateAttachmentUrl($this->attributes['var_titik_koordinat_attachment'] ?? null);
    }

    public function getVarSitrAttachmentAttribute()
    {
        return $this->generateAttachmentUrl($this->attributes['var_sitr_attachment'] ?? null);
    }

    public function getVarLp2bAttachmentAttribute()
    {
        return $this->generateAttachmentUrl($this->attributes['var_lp2b_attachment'] ?? null);
    }

    public function getVarBuktiPenguasaanTanahAttachmentAttribute()
    {
        return $this->generateAttachmentUrl($this->attributes['var_bukti_penguasaan_tanah_attachment'] ?? null);
    }

    public function getVarRencanaTeknisBangunanAttachmentAttribute()
    {
        return $this->generateAttachmentUrl($this->attributes['var_rencana_teknis_bangunan_attachment'] ?? null);
    }

    public function getVarPtpKkprNonberusahaAttachmentAttribute()
    {
        return $this->generateAttachmentUrl($this->attributes['var_ptp_kkpr_nonberusaha_attachment'] ?? null);
    }

    public function getVarAktaPendirianBadanAttachmentAttribute()
    {
        return $this->generateAttachmentUrl($this->attributes['var_akta_pendirian_badan_attachment'] ?? null);
    }

    public function getKoordinatAttribute(): array
    {
        // Kalo datanya kosong, balikin array kosong
        if (empty($this->json_geometry)) {
            return [];
        }

        try {
            // Decode string JSON jadi object PHP
            $data = json_decode($this->json_geometry);

            if (!$data) {
                return [];
            }

            $coordinates = [];
            $geometry = null;

            // Cek 1: Apakah ini FeatureCollection
            if (isset($data->type) && $data->type === 'FeatureCollection' && isset($data->features[0])) {
                $geometry = $data->features[0]->geometry ?? null;
            }
            // Cek 2: Apakah ini satu Feature (KASUSMU YANG INI)
            else if (isset($data->type) && $data->type === 'Feature' && isset($data->geometry)) {
                $geometry = $data->geometry;
            }
            // Cek 3: Apakah ini data geometri langsung
            else if (isset($data->type) && ($data->type === 'Polygon' || $data->type === 'LineString' || $data->type === 'Point')) {
                $geometry = $data;
            }

            // --- Ekstrak koordinat dari geometri yang ditemukan ---

            // Kalo tipenya Polygon
            if ($geometry && isset($geometry->type) && $geometry->type === 'Polygon' && isset($geometry->coordinates[0])) {
                // $geometry->coordinates[0] adalah array berisi [lng, lat], [lng, lat], ...
                $coordinates = $geometry->coordinates[0];
            }
            // ==========================================================
            // INI BLOK BARUNYA UNTUK LineString
            // ==========================================================
            else if ($geometry && isset($geometry->type) && $geometry->type === 'LineString' && isset($geometry->coordinates)) {
                // $geometry->coordinates adalah array berisi [lng, lat], [lng, lat], ...
                $coordinates = $geometry->coordinates;  // <- Perhatiin, gak pakai [0]
            }
            // ==========================================================
            // Kalo tipenya Point (satu titik aja)
            else if ($geometry && isset($geometry->type) && $geometry->type === 'Point' && isset($geometry->coordinates)) {
                // $geometry->coordinates adalah [lng, lat]
                $coordinates = [$geometry->coordinates];  // Kita bungkus array biar konsisten
            }

            // Pastikan hasilnya array
            return is_array($coordinates) ? $coordinates : [];
        } catch (\Exception $e) {
            // Log error kalo JSON-nya aneh / gak valid
            Log::error('Gagal parse json_geometry (ID: {$this->id}): ' . $e->getMessage());
            return [];
        }
    }
}
