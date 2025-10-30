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
            $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->onDelete('set null');
            $table->foreignId('request_tte_by_id')->nullable()->after('enum_status')->constrained('users')->onDelete('set null');
            $table->string('var_penandatangan')->nullable()->after('request_tte_by_id');
            $table->date('request_tte_date')->nullable()->after('var_penandatangan');
            $table->date('approved_date')->nullable()->after('request_tte_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permohonans', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['request_tte_by_id']);

            $table->dropColumn(['user_id', 'request_tte_by_id', 'var_penandatangan','request_tte_date','approved_date']);
        });
    }
};
