<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // PENTING: Pastikan urutan file SQL ini benar sesuai dependensi data
        // Contoh: provinsi dulu, baru kabupaten/kota, dst.
        $files = [
            'provinces.sql',
            'regencies.sql',
            'districts.sql',
            'villages.sql',
        ];

        foreach ($files as $file) {
            // Buat path lengkap ke file SQL
            $path = database_path('seeders/' . $file);
            
            // Cek apakah file ada sebelum dieksekusi
            if (File::exists($path)) {
                // Tampilkan info di console
                $this->command->info("Seeding data from: {$file}");
                
                // Baca isi file SQL
                $sql = File::get($path);
                
                // Eksekusi query SQL mentah
                DB::unprepared($sql);
            } else {
                $this->command->error("File not found: {$file}");
            }
        }
    }
}