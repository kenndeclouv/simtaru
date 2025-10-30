<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('permohonans', function (Blueprint $table) {
            $table->enum('enum_status', [
                'pending',
                'approved',
                'rejected',
                'request_tte'
            ])->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('permohonans')
            ->where('enum_status', 'request_tte')
            ->update(['enum_status' => 'pending']);

        Schema::table('permohonans', function (Blueprint $table) {
            $table->enum('enum_status', [
                'pending',
                'approved',
                'rejected'
            ])->default('pending')->change();
        });
    }
};
