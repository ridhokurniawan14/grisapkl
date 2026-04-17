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
        Schema::table('students', function (Blueprint $table) {
            // Tambahkan academic_year_id setelah user_id atau student_class_id
            $table->foreignId('academic_year_id')
                ->nullable()
                ->after('student_class_id')
                ->constrained('academic_years')
                ->nullOnDelete();
            // nullOnDelete = Jika tahun ajaran dihapus, data siswa tidak ikut terhapus (hanya id-nya jadi null) demi keamanan arsip.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            //
        });
    }
};
