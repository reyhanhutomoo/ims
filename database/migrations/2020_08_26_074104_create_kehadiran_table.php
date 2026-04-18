<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKehadiranTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kehadiran', function (Blueprint $table) {
            $table->id();

            // Relasi ke karyawan
            $table->unsignedBigInteger('karyawan_id');

            // Tanggal dan waktu
            $table->date('tanggal');
            $table->time('waktu_masuk')->nullable();
            $table->time('waktu_keluar')->nullable();

            // Informasi IP & lokasi
            $table->string('ip_masuk', 45)->nullable();
            $table->string('lokasi_masuk', 255)->nullable();
            $table->string('ip_keluar', 45)->nullable();
            $table->string('lokasi_keluar', 255)->nullable();

            // Status masuk/keluar
            $table->enum('status_masuk', ['tepat_waktu', 'terlambat'])->nullable();
            $table->enum('status_keluar', ['tepat_waktu', 'lebih_awal'])->nullable();

            // Laporan
            $table->text('laporan_harian')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('karyawan_id');
            $table->index('tanggal');
            $table->index('status_masuk');
            $table->index('status_keluar');

            // Unique constraint: satu kehadiran per karyawan per hari
            $table->unique(['karyawan_id', 'tanggal']);

            // Foreign key constraint
            $table->foreign('karyawan_id')
                  ->references('id')
                  ->on('karyawan')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kehadiran');
    }
}