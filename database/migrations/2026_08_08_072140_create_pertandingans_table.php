<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pertandingans', function (Blueprint $table) {
            $table->id();
            
            // A. Informasi pertandingan
            $table->string('nama_turnamen'); // Menandai konteks tempat dan acara
            $table->string('kategori'); // Open, Mixed, Veteran, dst.
            $table->string('babak'); // Grup, Perempat final, Semifinal, Final
            $table->string('format_set'); // Best of 1 / Best of 3
            $table->boolean('golden_point')->default(true); // Ya (true) / Tidak (false)

            // B. Data tim dan pemain (Disimpan statis agar tidak berubah jika ada pergantian nama tim di masa depan)
            $table->string('tim_a_pemain_kiri'); 
            $table->string('tim_a_pemain_kanan');
            $table->string('tim_b_pemain_kiri');
            $table->string('tim_b_pemain_kanan');

            // C. Konfirmasi awal
            $table->enum('serve_awal', ['Tim A', 'Tim B']); // Menjadi titik awal logika pergantian giliran serve
            $table->enum('sisi_lapangan_awal_tim_a', ['Kiri', 'Kanan']); // Dipakai sebagai acuan saat ganti sisi

            // Data Tambahan dari Live Match (Halaman 2)
            $table->string('kondisi_lapangan')->nullable(); // Indoor / Outdoor, kondisi cuaca
            $table->text('catatan_operator')->nullable(); // Ruang bagi operator mencatat kejadian kualitatif
            
            // Status Sistem
            $table->enum('status', ['setup', 'live', 'selesai'])->default('setup');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pertandingans');
    }
};