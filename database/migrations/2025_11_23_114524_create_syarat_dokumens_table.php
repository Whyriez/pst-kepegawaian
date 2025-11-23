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
        Schema::create('syarat_dokumens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jenis_layanan_id')->constrained('jenis_layanans')->onDelete('cascade');
            $table->string('nama_dokumen'); // Contoh: "SK CPNS", "SKP 2024"
            $table->boolean('is_required')->default(true); // Wajib atau Opsional
            $table->string('allowed_types')->default('pdf'); // pdf, jpg, png
            $table->integer('max_size_kb')->default(2048); // 2MB
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('syarat_dokumens');
    }
};
