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
            $table->string('var_nik', 20)->nullable();
            $table->string('var_nama');
            $table->text('text_alamat');
            $table->string('var_provinsi')->nullable();
            $table->string('var_kabupaten')->nullable();
            $table->string('var_kecamatan')->nullable();
            $table->string('var_kelurahan')->nullable();
            $table->string('var_email')->nullable();
            $table->string('var_no_telp')->nullable();
            $table->string('var_no_ponsel')->nullable();

            // data usaha
            $table->string('var_nama_usaha');
            $table->string('var_bentuk_usaha')->nullable();
            $table->text('text_alamat_usaha');
            $table->string('var_kecamatan_usaha')->nullable();
            $table->string('var_kelurahan_usaha')->nullable();
            $table->string('var_rencana_usaha')->nullable();
            $table->decimal('dec_rencana_luas_lantai', 12, 2)->nullable();

            // geometri (simpen geojson biar fleksibel: point/line/polygon)
            $table->longText('json_geometry')->nullable(); // GeoJSON


            // administrasi
            $table->string('var_nomor_permohonan')->nullable();
            $table->date('date_tanggal_permohonan')->nullable();
            $table->string('var_nomor_pengesahan')->nullable();
            $table->date('date_tanggal_pengesahan')->nullable();

            $table->text('text_catatan')->nullable();
            $table->string('var_url_lampiran')->nullable();
            $table->enum('enum_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permohonans');
    }
};
