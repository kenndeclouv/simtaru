<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
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
            ->logOnly(['var_nama', 'enum_status', 'text_catatan']) // Catat perubahan hanya di kolom ini
            ->setDescriptionForEvent(fn(string $eventName) => "Permohonan {$this->var_nama} telah di-{$eventName}") // Deskripsi log
            ->useLogName('Permohonan') // Nama grup log
            ->logOnlyDirty(); // Hanya catat jika ada perubahan
    }


    public function templateDocs()
    {
        return $this->belongsToMany(TemplateDocs::class, 'permohonans_template_docs', 'fk_permohonan_id', 'fk_template_docs_id')
            ->withPivot('var_generated_file_path') // <-- PENTING!
            ->withTimestamps();
    }

    protected function getWilayahName($filePath, $id)
    {
        // Pengaman jika $id atau $filePath kosong
        if (empty($id) || empty($filePath)) {
            return $id; // Kembalikan ID aslinya
        }

        $fullPath = public_path($filePath);

        if (!File::exists($fullPath)) {
            Log::warning("File data wilayah tidak ditemukan di: {$fullPath}");
            return $id; // Kembalikan ID jika file tidak ada
        }

        $data = json_decode(File::get($fullPath), true);
        $found = collect($data)->firstWhere('id', $id);

        return $found ? $found['nama'] : $id; // Jika ketemu, kembalikan 'nama', jika tidak, kembalikan ID
    }

    public function getNamaProvinsiAttribute()
    {
        return $this->getWilayahName('data-indonesia/provinsi.json', $this->var_provinsi);
    }

    public function getNamaKabupatenAttribute()
    {
        return $this->getWilayahName("data-indonesia/kabupaten/{$this->var_provinsi}.json", $this->var_kabupaten);
    }

    public function getNamaKecamatanAttribute()
    {
        return $this->getWilayahName("data-indonesia/kecamatan/{$this->var_kabupaten}.json", $this->var_kecamatan);
    }

    public function getNamaKelurahanAttribute()
    {
        return $this->getWilayahName("data-indonesia/kelurahan/{$this->var_kecamatan}.json", $this->var_kelurahan);
    }

    public function getNamaKecamatanUsahaAttribute()
    {
        $keyStorages = KeyStorage::all();
        return $this->getWilayahName("data-indonesia/kecamatan/{$keyStorages->where('var_key', 'kabupatenUsahaDefaultId')->first()->var_value}.json", $this->var_kecamatan_usaha);
    }

    public function getNamaKelurahanUsahaAttribute()
    {
        return $this->getWilayahName("data-indonesia/kelurahan/{$this->var_kecamatan_usaha}.json", $this->var_kelurahan_usaha);
    }
}
