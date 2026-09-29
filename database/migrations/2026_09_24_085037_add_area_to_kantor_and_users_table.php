<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kantor', function (Blueprint $table) {
            $table->enum('area', ['barat', 'selatan', 'timur'])->nullable()->after('jenis');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->enum('area', ['barat', 'selatan', 'timur'])->nullable()->after('kantor_id');
        });

        // Tambah role area_manager & manager_bisnis ke enum role
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('cs','slo','admin_legal','admin','direksi','area_manager','manager_bisnis') NOT NULL DEFAULT 'cs'");
    }

    public function down(): void
    {
        Schema::table('kantor', function (Blueprint $table) {
            $table->dropColumn('area');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('area');
        });

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('cs','slo','admin_legal','admin','direksi') NOT NULL DEFAULT 'cs'");
    }
};