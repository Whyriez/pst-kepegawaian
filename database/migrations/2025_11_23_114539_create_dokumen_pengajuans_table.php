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
        Schema::create('dokumen_pengajuans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_id')->constrained('pengajuans')->onDelete('cascade');
            $table->foreignId('syarat_dokumen_id')->constrained('syarat_dokumens');

            $table->string('nama_file_asli'); // nama_file_dari_user.pdf
            $table->string('path_file'); // storage/pengajuan/2025/uniqid.pdf
            $table->string('tipe_file'); // application/pdf
            $table->integer('ukuran_file'); // dalam KB
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumen_pengajuans');
    }
};
