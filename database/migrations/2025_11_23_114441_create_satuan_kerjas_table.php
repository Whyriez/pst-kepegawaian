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
        Schema::create('satuan_kerjas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_satuan_kerja');
            $table->string('kode_satuan_kerja')->unique();
            $table->text('alamat_lengkap');
            $table->string('telepon')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('kepala_satker'); // Nama Kepala untuk TTD surat
            $table->string('nip_kepala_satker'); // NIP Kepala
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('satuan_kerjas');
    }
};
