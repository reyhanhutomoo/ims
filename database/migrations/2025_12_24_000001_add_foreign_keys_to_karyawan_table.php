<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToKaryawanTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('karyawan', function (Blueprint $table) {
            // Pastikan kolom sudah ada (dibuat pada migrasi sebelumnya)
            // Tambahkan foreign keys untuk menjaga integritas referensial
            $table->foreign('pengguna_id')
                ->references('id')
                ->on('pengguna')
                ->onDelete('cascade');

            $table->foreign('kampus_id')
                ->references('id')
                ->on('kampus')
                ->onDelete('cascade');

            $table->foreign('divisi_id')
                ->references('id')
                ->on('divisi')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('karyawan', function (Blueprint $table) {
            // Hapus foreign keys sesuai nama konvensi Laravel
            $table->dropForeign('karyawan_pengguna_id_foreign');
            $table->dropForeign('karyawan_kampus_id_foreign');
            $table->dropForeign('karyawan_divisi_id_foreign');
        });
    }
}