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
    Schema::create('assessment_schemes', function (Blueprint $table) {
        $table->id();
        $table->foreignId('major_id')->constrained('majors')->cascadeOnDelete();
        $table->string('name'); // Contoh: "Networking (ISP)" atau "Hardware (Service PC)"
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_schemes');
    }
};
