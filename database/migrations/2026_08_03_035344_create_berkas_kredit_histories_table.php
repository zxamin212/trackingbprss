<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('berkas_kredit_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('berkas_kredit_id')->constrained('berkas_kredit')->cascadeOnDelete();
            $table->string('status'); // diajukan, verifikasi, survey, komite, ditolak_survey, ditolak_komite, akad, belum_lengkap, pencairan, dibatalkan
            $table->text('keterangan')->nullable();
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('berkas_kredit_histories');
    }
};