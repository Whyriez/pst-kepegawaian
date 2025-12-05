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
        Schema::create('arsip_surats', function (Blueprint $table) {
            $table->id();

            // 1. Pembeda Dinamis (PENGANTAR / SPTJM)
            $table->string('jenis_dokumen')->index();

            // 2. RELASI KE JENIS LAYANAN (Ganti string manual jadi Foreign Key)
            // Ini akan otomatis terhubung ke id di tabel jenis_layanans
            $table->foreignId('jenis_layanan_id')
                ->constrained('jenis_layanans')
                ->onDelete('cascade');

            // 3. Input: Periode (Boleh null)
            $table->string('periode')->nullable();

            // 4. File
            $table->string('file_path');
            $table->string('file_name');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arsip_surats');
    }
};
