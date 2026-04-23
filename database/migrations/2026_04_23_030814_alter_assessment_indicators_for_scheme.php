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
        Schema::table('assessment_indicators', function (Blueprint $table) {
            // Buang relasi jurusan
            $table->dropConstrainedForeignId('major_id');

            // Masukkan relasi skema
            $table->foreignId('assessment_scheme_id')
                ->after('assessment_element_id')
                ->constrained('assessment_schemes')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assessment_indicators', function (Blueprint $table) {
            //
        });
    }
};
