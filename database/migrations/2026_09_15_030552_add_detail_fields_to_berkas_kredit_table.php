<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('berkas_kredit', function (Blueprint $table) {
            $table->string('tempat_lahir')->nullable()->after('nama_nasabah');
            $table->date('tanggal_lahir')->nullable()->after('tempat_lahir');
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable()->after('tanggal_lahir');
            $table->text('alamat_ktp')->nullable()->after('jenis_kelamin');
            $table->text('alamat_domisili')->nullable()->after('alamat_ktp');
            $table->string('no_hp')->nullable()->after('alamat_domisili');
            $table->string('pekerjaan_usaha')->nullable()->after('no_hp');
            $table->decimal('plafon', 15, 2)->nullable()->after('pekerjaan_usaha');
            $table->string('file_dokumen')->nullable()->after('plafon');

            $table->foreignId('slo_id')->nullable()->after('user_id')->constrained('users');
            $table->enum('sumber', ['langsung', 'marketing'])->default('langsung')->after('slo_id');
            $table->text('keterangan')->nullable()->after('sumber');
        });
    }

    public function down(): void
    {
        Schema::table('berkas_kredit', function (Blueprint $table) {
            $table->dropForeign(['slo_id']);
            $table->dropColumn([
                'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin',
                'alamat_ktp', 'alamat_domisili', 'no_hp', 'pekerjaan_usaha',
                'plafon', 'file_dokumen', 'slo_id', 'sumber', 'keterangan',
            ]);
        });
    }
};