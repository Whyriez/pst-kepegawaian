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
        Schema::create('periodes', function (Blueprint $table) {
            $table->id();
            // Relasi ke Jenis Layanan (Biar spesifik per layanan)
            $table->foreignId('jenis_layanan_id')
                ->constrained('jenis_layanans')
                ->onDelete('cascade');

            $table->string('nama_periode'); // Contoh: "Periode April 2025"
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->boolean('is_active')->default(true); // Switch manual jika ingin tutup paksa
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('periodes');
    }
};
