<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Ubah tipe kolom enum untuk menambahkan 'Libur'
        DB::statement("ALTER TABLE journals MODIFY COLUMN attend_status ENUM('Hadir', 'Sakit', 'Izin', 'Libur') NOT NULL DEFAULT 'Hadir'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('journals', function (Blueprint $table) {
            //
        });
    }
};
