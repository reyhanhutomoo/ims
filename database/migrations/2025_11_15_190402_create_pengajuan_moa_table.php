<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePengajuanMoaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pengajuan_moa', function (Blueprint $table) {
            $table->id();

            // Relasi ke pengguna (mahasiswa yang mengajukan)
            $table->unsignedBigInteger('pengguna_id');

            // Informasi dokumen dan tracking
            $table->string('nomor_pelacakan', 50)->unique();
            $table->string('judul');
            $table->enum('jenis_dokumen', ['MOA', 'IA']);
            $table->string('path_berkas', 150);
            $table->string('path_berkas_ttd', 150)->nullable();

            // Status dan catatan admin
            $table->enum('status', ['menunggu', 'direview', 'disetujui', 'ditolak'])->default('menunggu');
            $table->text('catatan_admin')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('status');
            $table->index('judul');

            // Foreign key constraints
            $table->foreign('pengguna_id')
                  ->references('id')
                  ->on('pengguna')
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
        Schema::dropIfExists('pengajuan_moa');
    }
}