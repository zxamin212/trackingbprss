<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nomor_berkas_counter', function (Blueprint $table) {
            $table->date('tanggal')->primary();
            $table->unsignedInteger('terakhir')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nomor_berkas_counter');
    }
};