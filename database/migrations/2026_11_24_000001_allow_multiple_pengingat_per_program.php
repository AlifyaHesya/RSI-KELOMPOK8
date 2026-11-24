<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengingat', function (Blueprint $table) {
            $table->dropForeign('pengingat_id_mahasiswa_foreign');
            $table->dropForeign('pengingat_id_proker_foreign');
            $table->dropUnique('pengingat_id_mahasiswa_id_proker_unique');
            $table->unique(
                ['id_mahasiswa', 'id_proker', 'waktu_pengingat'],
                'pengingat_mahasiswa_proker_waktu_unique'
            );
            $table->foreign('id_mahasiswa')->references('id_mahasiswa')->on('mahasiswa')->cascadeOnDelete();
            $table->foreign('id_proker')->references('id_proker')->on('program_kerja')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pengingat', function (Blueprint $table) {
            $table->dropForeign('pengingat_id_mahasiswa_foreign');
            $table->dropForeign('pengingat_id_proker_foreign');
            $table->dropUnique('pengingat_mahasiswa_proker_waktu_unique');
            $table->unique(['id_mahasiswa', 'id_proker']);
            $table->foreign('id_mahasiswa')->references('id_mahasiswa')->on('mahasiswa')->cascadeOnDelete();
            $table->foreign('id_proker')->references('id_proker')->on('program_kerja')->cascadeOnDelete();
        });
    }
};
