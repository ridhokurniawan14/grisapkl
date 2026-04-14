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
        Schema::create('journals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pkl_placement_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->time('time');
            $table->enum('attend_status', ['Hadir', 'Sakit', 'Izin'])->default('Hadir');
            $table->text('activity');
            $table->string('photo_path')->nullable();
            $table->boolean('is_valid')->default(true); // Silent Validation DUDIKA
            $table->string('revision_note')->nullable(); // Alasan jika ditolak
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journals');
    }
};
