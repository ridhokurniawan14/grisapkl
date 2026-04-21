<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('monitorings', function (Blueprint $table) {
            // Ditambahkan setelah ID biar rapi
            $table->foreignId('monitoring_schedule_id')->nullable()->after('id')->constrained('monitoring_schedules')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('monitorings', function (Blueprint $table) {
            $table->dropForeign(['monitoring_schedule_id']);
            $table->dropColumn('monitoring_schedule_id');
        });
    }
};
