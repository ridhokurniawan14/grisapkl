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
        Schema::table('pkl_placements', function (Blueprint $table) {
            // Tahun Ajaran
            $table->foreignId('academic_year_id')->nullable()->after('id')->constrained('academic_years')->nullOnDelete();

            // Lokasi Absensi
            $table->string('latitude')->nullable()->after('teacher_id');
            $table->string('longitude')->nullable()->after('latitude');
            $table->integer('radius')->default(50)->after('longitude'); // Default 50 meter
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pkl_placements', function (Blueprint $table) {
            //
        });
    }
};
