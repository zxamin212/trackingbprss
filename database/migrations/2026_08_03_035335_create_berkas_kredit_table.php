<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('berkas_kredit', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_berkas')->unique();
            $table->string('nama_nasabah');
            $table->string('jenis_kredit')->nullable();
            $table->string('status_terkini')->default('diajukan');
            $table->date('tanggal_masuk');
            $table->date('tanggal_selesai')->nullable();
            $table->foreignId('kantor_id')->constrained('kantor');
            $table->foreignId('user_id')->constrained('users'); // CS yang input pertama kali
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('berkas_kredit');
    }
};