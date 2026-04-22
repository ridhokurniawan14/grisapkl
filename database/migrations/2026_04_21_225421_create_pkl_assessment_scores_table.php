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
        Schema::create('pkl_assessment_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pkl_assessment_id')->constrained('pkl_assessments')->cascadeOnDelete();
            $table->foreignId('assessment_indicator_id')->constrained('assessment_indicators')->cascadeOnDelete();
            $table->integer('score'); // Menyimpan nilai angka (misal: 85, 90)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pkl_assessment_scores');
    }
};
