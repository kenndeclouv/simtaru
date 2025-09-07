<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('permohonans', function (Blueprint $table) {
            $table->id();
            // informasi pengusul
            $table->string('var_nik', 20);
            $table->string('var_nama');
            $table->text('text_alamat');
            $table->string('var_kabupaten');
            $table->string('var_kecamatan');
            $table->string('var_kelurahan');
            $table->string('var_email');
            $table->string('var_no_telp')->nullable();
            $table->string('var_no_ponsel')->nullable();

            // data usaha
            $table->string('var_nama_usaha');
            $table->string('var_bentuk_usaha');
            $table->text('text_alamat_usaha');
            $table->string('var_kecamatan_usaha');
            $table->string('var_kelurahan_usaha');
            $table->string('var_rencana_usaha');
            $table->decimal('dec_rencana_luas_lantai', 12, 2)->nullable();

            // geometri (simpen geojson biar fleksibel: point/line/polygon)
            $table->longText('json_geometry')->nullable(); // GeoJSON
            // $table->string('var_gambar_area')->nullable(); // path ke file upload

            // administrasi
            $table->string('var_nomor_permohonan');
            $table->date('date_tanggal_permohonan');
            $table->string('var_nomor_pengesahan')->nullable();
            $table->date('date_tanggal_pengesahan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permohonans');
    }
};
