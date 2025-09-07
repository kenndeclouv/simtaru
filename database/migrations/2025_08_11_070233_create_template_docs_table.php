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
        Schema::create('template_docs', function (Blueprint $table) {
            $table->id();
            $table->string('var_nama');
            $table->string('var_file_path');
            $table->enum('enum_jenis', ['sitr', 'rdtr', 'kkpr']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_docs');
    }
};
