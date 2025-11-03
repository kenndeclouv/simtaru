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
            ->logOnly(['var_nama', 'enum_status', 'text_catatan'])
            ->setDescriptionForEvent(fn(string $eventName) => "Permohonan {$this->var_nama} telah di-{$eventName}")
            ->useLogName('Permohonan')
            ->logOnlyDirty();
    }

    public function permohonanTemplateDocs()
    {
        return $this->hasMany(PermohonanTemplateDoc::class, 'fk_permohonan_id');
    }

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


    public function getNamaProvinsiAttribute()
    {
        return $this->province->name;
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
        if (empty($this->json_geometry)) {
            return [];
        }

        try {

            $data = json_decode($this->json_geometry);

            if (!$data) {
                return [];
            }

            $coordinates = [];
            $geometry = null;

            if (isset($data->type) && $data->type === 'FeatureCollection' && isset($data->features[0])) {
                $geometry = $data->features[0]->geometry ?? null;
            }

            else if (isset($data->type) && $data->type === 'Feature' && isset($data->geometry)) {
                $geometry = $data->geometry;
            }

            else if (isset($data->type) && ($data->type === 'Polygon' || $data->type === 'LineString' || $data->type === 'Point')) {
                $geometry = $data;
            }

            if ($geometry && isset($geometry->type) && $geometry->type === 'Polygon' && isset($geometry->coordinates[0])) {

                $coordinates = $geometry->coordinates[0];
            }

            else if ($geometry && isset($geometry->type) && $geometry->type === 'LineString' && isset($geometry->coordinates)) {

                $coordinates = $geometry->coordinates;
            }

            else if ($geometry && isset($geometry->type) && $geometry->type === 'Point' && isset($geometry->coordinates)) {

                $coordinates = [$geometry->coordinates];
            }

            return is_array($coordinates) ? $coordinates : [];
        } catch (\Exception $e) {

            Log::error('Gagal parse json_geometry (ID: {$this->id}): ' . $e->getMessage());
            return [];
        }
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function userRequestTteBy()
    {
        return $this->belongsTo(User::class, 'user_request_tte_id');
    }
}
