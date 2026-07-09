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
        Schema::create('bukus', function (Blueprint $table) {
            $table->id();
            $table->string('kode_buku')->unique(); // Harus unik untuk validasi
            $table->string('judul');
            $table->foreignId('jenis_buku_id')->constrained('jenis_bukus')->onDelete('cascade'); // Relasi Belongs To
            $table->string('pengarang');
            $table->string('penerbit');
            $table->text('sinopsis');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bukus');
    }
};
