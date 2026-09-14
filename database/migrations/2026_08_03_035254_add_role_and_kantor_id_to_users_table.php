<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['cs', 'senior_loan_officer', 'admin_legal', 'admin'])->default('cs');
            $table->foreignId('kantor_id')->nullable()->constrained('kantor')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['kantor_id']);
            $table->dropColumn(['role', 'kantor_id']);
        });
    }
};