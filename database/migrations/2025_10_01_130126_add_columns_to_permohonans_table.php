<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('permohonans', function (Blueprint $table) {
            $table->string('var_npwp_pemohon_atau_badan_usaha')->nullable();
            $table->string('var_jenis_kegiatan')->nullable();
            
            $table->string('var_fotocopy_ktp_attachment')->nullable();
            $table->string('var_fotocopy_npwp_attachment')->nullable();
            $table->string('var_foto_lokasi_rencana_kegiatan_attachment')->nullable();
            $table->string('var_titik_koordinat_attachment')->nullable();
            $table->string('var_sitr_attachment')->nullable();
            $table->string('var_lp2b_attachment')->nullable();
            $table->string('var_bukti_penguasaan_tanah_attachment')->nullable();
            $table->string('var_rencana_teknis_bangunan_attachment')->nullable();
            $table->string('var_ptp_kkpr_nonberusaha_attachment')->nullable();
            $table->string('var_akta_pendirian_badan_attachment')->nullable();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permohonans', function (Blueprint $table) {
            $table->dropColumn('var_npwp_pemohon_atau_badan_usaha');
            $table->dropColumn('var_jenis_kegiatan');

            $table->dropColumn('var_fotocopy_ktp_attachment');
            $table->dropColumn('var_fotocopy_npwp_attachment');
            $table->dropColumn('var_foto_lokasi_rencana_kegiatan_attachment');
            $table->dropColumn('var_titik_koordinat_attachment');
            $table->dropColumn('var_sitr_attachment');
            $table->dropColumn('var_lp2b_attachment');
            $table->dropColumn('var_bukti_penguasaan_tanah_attachment');
            $table->dropColumn('var_rencana_teknis_bangunan_attachment');
            $table->dropColumn('var_ptp_kkpr_nonberusaha_attachment');
            $table->dropColumn('var_akta_pendirian_badan_attachment');
        });
    }
};
