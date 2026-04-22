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
        Schema::create('assessment_elements', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Contoh: "Internalisasi dan Penerapan Soft Skills"
            $table->string('nm_tp'); // Contoh: "Internalisasi dan Penerapan Soft Skills"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_elements');
    }
};
