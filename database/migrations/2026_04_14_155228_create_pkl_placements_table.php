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
        Schema::create('pkl_placements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('dudika_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete(); // Pembimbing
            $table->date('start_date');
            $table->date('end_date');
            $table->string('pkl_field')->nullable(); // Bidang/Bagian PKL
            $table->enum('status', ['Aktif', 'Ditarik'])->default('Aktif');
            // Snapshot Pengesahan untuk QR Code KS:
            $table->string('pengesah_ks_nama')->nullable();
            $table->string('pengesah_ks_nip')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pkl_placements');
    }
};
