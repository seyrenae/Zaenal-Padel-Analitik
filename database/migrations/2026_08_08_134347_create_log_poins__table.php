<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_poins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pertandingan_id')->constrained('pertandingans')->onDelete('cascade');
            
            // Konteks Poin
            $table->integer('set_ke'); // Mencatat poin ini terjadi di set berapa
            $table->integer('game_ke'); // Mencatat poin ini terjadi di game berapa
            $table->enum('tim_pemenang_poin', ['Tim A', 'Tim B']); // Siapa yang mendapat +1
            $table->string('server_saat_ini'); // Dipakai sebagai konteks analitik performa saat serve vs return

            // Input Event Per Poin (Sesuai Spesifikasi)
            $table->string('pemain_penghasil_poin'); // Dropdown 4 pilihan nama pemain
            $table->string('jenis_akhir_poin'); // Winner, Unforced error, Forced error, Error pantul dinding, Double fault
            $table->string('jenis_pukulan')->nullable(); // Opsional: Bandeja, Vibora, Smash, dll
            $table->boolean('libatkan_dinding')->default(false); // Menjadi dasar statistik khas padel

            $table->timestamps(); // Created_at otomatis menjadi penanda waktu asli tiap poin dicetak
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_poins');
    }
};