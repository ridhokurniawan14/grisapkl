<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monitoring_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Contoh: "Monitoring Bulan 1"
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(true); // Sakelar darurat untuk Humas
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monitoring_schedules');
    }
};
