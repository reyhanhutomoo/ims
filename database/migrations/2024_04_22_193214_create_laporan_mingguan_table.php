<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaporanMingguanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('laporan_mingguan', function (Blueprint $table) {
            $table->id();

            // Relasi ke karyawan
            $table->unsignedBigInteger('karyawan_id');

            // Informasi laporan
            $table->string('judul', 255);
            $table->text('deskripsi')->nullable();
            $table->string('file', 255);

            // Periode laporan
            $table->unsignedTinyInteger('minggu_ke'); // 1-53
            $table->unsignedSmallInteger('tahun');    // contoh: 2025

            // Nilai & status workflow
            $table->decimal('nilai', 5, 2)->nullable();
            $table->enum('status', ['draft', 'disubmit', 'direview', 'disetujui', 'ditolak'])->default('draft');

            // Review metadata
            $table->unsignedBigInteger('direview_oleh')->nullable(); // FK ke pengguna
            $table->dateTime('tanggal_review')->nullable();
            $table->text('catatan_review')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('karyawan_id');
            $table->index('judul');
            $table->index('minggu_ke');
            $table->index('tahun');
            $table->index('status');

            // Unique constraint: satu laporan per minggu per tahun per karyawan
            $table->unique(['karyawan_id', 'minggu_ke', 'tahun']);

            // Foreign key constraints
            $table->foreign('karyawan_id')
                  ->references('id')
                  ->on('karyawan')
                  ->onDelete('cascade');

            $table->foreign('direview_oleh')
                  ->references('id')
                  ->on('pengguna')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('laporan_mingguan');
    }
}