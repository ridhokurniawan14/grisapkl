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
            // Tambah kolom untuk simpan lokasi file PDF
            $table->string('file_laporan_path')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('pkl_placements', function (Blueprint $table) {
            $table->dropColumn('file_laporan_path');
        });
    }
};
