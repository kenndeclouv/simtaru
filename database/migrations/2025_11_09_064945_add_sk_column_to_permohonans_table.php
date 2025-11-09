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
            $table->string('var_sk_attachment')->nullable()->after('approved_date');
            $table->string('var_nomor_sk')->nullable()->after('var_sk_attachment');
            $table->date('date_sk_terbit')->nullable()->after('var_nomor_sk');
        });
    }

    public function down(): void
    {
        Schema::table('permohonans', function (Blueprint $table) {
            $table->dropColumn(['var_sk_attachment', 'var_nomor_sk', 'date_sk_terbit']);
        });
    }
};
