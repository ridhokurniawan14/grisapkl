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
        Schema::table('school_profiles', function (Blueprint $table) {
            // Tambahkan kolom toggle baru
            $table->boolean('is_teacher_signature_enabled')->default(true)->after('is_radius_attendance_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('school_profiles', function (Blueprint $table) {
            $table->dropColumn('is_teacher_signature_enabled');
        });
    }
};
