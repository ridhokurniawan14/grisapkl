<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school_profiles', function (Blueprint $table) {
            $table->string('kop_surat_path')->nullable()->after('logo_path');
            $table->string('surat_pengantar_nomor')->nullable()->after('kop_surat_path');
        });
    }

    public function down(): void
    {
        Schema::table('school_profiles', function (Blueprint $table) {
            $table->dropColumn(['kop_surat_path', 'surat_pengantar_nomor']);
        });
    }
};
