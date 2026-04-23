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
            // Dibuat nullable dulu agar data penempatan yang sudah ada di database tidak error
            $table->foreignId('assessment_scheme_id')
                ->nullable()
                ->after('dudika_id')
                ->constrained('assessment_schemes')
                ->nullOnDelete();
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
