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
        Schema::table('permohonans_template_docs', function (Blueprint $table) {
            $table->string('var_generated_file_path')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permohonans_template_docs', function (Blueprint $table) {
            $table->dropColumn('var_generated_file_path');
        });
    }
};
