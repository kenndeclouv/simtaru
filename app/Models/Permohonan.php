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
        return $this->belongsTo(Province::class, 'var_provinsi', 'id');
    }

    public function regency(): BelongsTo
    {
        return $this->belongsTo(Regency::class, 'var_kabupaten', 'id');
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'var_kecamatan', 'id');
    }

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class, 'var_kelurahan', 'id');
    }

    public function districtUsaha(): BelongsTo
    {
        return $this->belongsTo(District::class, 'var_kecamatan_usaha', 'id');
    }

    public function villageUsaha(): BelongsTo
    {
        return $this->belongsTo(Village::class, 'var_kelurahan_usaha', 'id');
    }

    // Accessor methods for backward compatibility
    public function getNamaProvinsiAttribute()
    {
        return $this->province?->name ?? $this->var_provinsi;
    }

    public function getNamaKabupatenAttribute()
    {
        return $this->regency?->name ?? $this->var_kabupaten;
    }

    public function getNamaKecamatanAttribute()
    {
        return $this->district?->name ?? $this->var_kecamatan;
    }

    public function getNamaKelurahanAttribute()
    {
        return $this->village?->name ?? $this->var_kelurahan;
    }

    public function getNamaKecamatanUsahaAttribute()
    {
        return $this->districtUsaha?->name ?? $this->var_kecamatan_usaha;
    }

    public function getNamaKelurahanUsahaAttribute()
    {
        return $this->villageUsaha?->name ?? $this->var_kelurahan_usaha;
    }

    public function getVarFotocopyKtpAttachmentAttribute()
    {
        if (!empty($this->attributes['var_fotocopy_ktp_attachment'])) {
            return asset('storage/' . $this->attributes['var_fotocopy_ktp_attachment']);
        }
        return null;
    }

    public function getVarFotocopyNpwpAttachmentAttribute()
    {
        if (!empty($this->attributes['var_fotocopy_npwp_attachment'])) {
            return asset('storage/' . $this->attributes['var_fotocopy_npwp_attachment']);
        }
        return null;
    }

    public function getVarFotoLokasiRencanaKegiatanAttachmentAttribute()
    {
        if (!empty($this->attributes['var_foto_lokasi_rencana_kegiatan_attachment'])) {
            return asset('storage/' . $this->attributes['var_foto_lokasi_rencana_kegiatan_attachment']);
        }
        return null;
    }

    public function getVarTitikKoordinatAttachmentAttribute()
    {
        if (!empty($this->attributes['var_titik_koordinat_attachment'])) {
            return asset('storage/' . $this->attributes['var_titik_koordinat_attachment']);
        }
        return null;
    }

    public function getVarSitrAttachmentAttribute()
    {
        if (!empty($this->attributes['var_sitr_attachment'])) {
            return asset('storage/' . $this->attributes['var_sitr_attachment']);
        }
        return null;
    }

    public function getVarLp2bAttachmentAttribute()
    {
        if (!empty($this->attributes['var_lp2b_attachment'])) {
            return asset('storage/' . $this->attributes['var_lp2b_attachment']);
        }
        return null;
    }

    public function getVarBuktiPenguasaanTanahAttachmentAttribute()
    {
        if (!empty($this->attributes['var_bukti_penguasaan_tanah_attachment'])) {
            return asset('storage/' . $this->attributes['var_bukti_penguasaan_tanah_attachment']);
        }
        return null;
    }

    public function getVarRencanaTeknisBangunanAttachmentAttribute()
    {
        if (!empty($this->attributes['var_rencana_teknis_bangunan_attachment'])) {
            return asset('storage/' . $this->attributes['var_rencana_teknis_bangunan_attachment']);
        }
        return null;
    }

    public function getVarPtpKkprNonberusahaAttachmentAttribute()
    {
        if (!empty($this->attributes['var_ptp_kkpr_nonberusaha_attachment'])) {
            return asset('storage/' . $this->attributes['var_ptp_kkpr_nonberusaha_attachment']);
        }
        return null;
    }

    public function getVarAktaPendirianBadanAttachmentAttribute()
    {
        if (!empty($this->attributes['var_akta_pendirian_badan_attachment'])) {
            return asset('storage/' . $this->attributes['var_akta_pendirian_badan_attachment']);
        }
        return null;
    }
}
