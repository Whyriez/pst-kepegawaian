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
        Schema::create('pengajuans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_tiket')->unique();
            $table->foreignId('pegawai_id')->constrained('pegawais')->onDelete('cascade');
            $table->foreignId('jenis_layanan_id')->constrained('jenis_layanans');

            // Status & Prioritas
            $table->enum('status', ['pending', 'disetujui', 'ditolak', 'ditunda', 'perbaikan'])->default('pending');
            $table->enum('prioritas', ['rendah', 'sedang', 'tinggi'])->default('sedang');

            // Data Dinamis
            $table->json('data_tambahan')->nullable();

            // Catatan & Tindak Lanjut
            $table->text('catatan_admin')->nullable();
            $table->date('tanggal_tindak_lanjut')->nullable(); // <--- TAMBAHAN BARU

            // Tanggal & Verifikator
            $table->date('tanggal_pengajuan');
            $table->date('tanggal_verifikasi')->nullable();
            $table->foreignId('verifikator_id')->nullable()->constrained('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuans');
    }
};
