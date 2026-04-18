<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCutiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cuti', function (Blueprint $table) {
            $table->id();

            // Relasi ke karyawan
            $table->unsignedBigInteger('karyawan_id');

            // Informasi cuti
            $table->enum('jenis_cuti', ['sakit', 'izin', 'tahunan', 'lainnya'])->index();
            $table->string('alasan', 255);
            $table->text('deskripsi')->nullable();
            $table->string('bukti', 255)->nullable();
            $table->boolean('setengah_hari')->default(false);

            // Periode cuti
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();

            // Status & persetujuan
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu')->index();
            $table->unsignedBigInteger('disetujui_oleh')->nullable(); // FK ke pengguna
            $table->dateTime('tanggal_disetujui')->nullable();
            $table->text('catatan_persetujuan')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('karyawan_id');
            $table->index('tanggal_mulai');
            $table->index('tanggal_selesai');

            // Foreign key constraints
            $table->foreign('karyawan_id')
                  ->references('id')
                  ->on('karyawan')
                  ->onDelete('cascade');

            $table->foreign('disetujui_oleh')
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
        Schema::dropIfExists('cuti');
    }
}