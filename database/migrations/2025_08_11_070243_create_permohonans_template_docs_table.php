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
        Schema::create('permohonans_template_docs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fk_permohonan_id')->constrained('permohonans')->onDelete('cascade');
            $table->foreignId('fk_template_docs_id')->constrained('template_docs')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permohonans_template_docs');
    }
};
