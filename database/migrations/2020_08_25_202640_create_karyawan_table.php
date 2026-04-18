<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKaryawanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('karyawan', function (Blueprint $table) {
            $table->id();

            // Relasi ke pengguna (user)
            $table->unsignedBigInteger('pengguna_id');
            // Relasi ke kampus dan divisi
            $table->unsignedBigInteger('kampus_id');
            $table->unsignedBigInteger('divisi_id');

            // Data utama karyawan
            $table->string('nama', 100);
            $table->unsignedTinyInteger('usia'); // 0-255
            $table->string('nomor_telepon', 20)->nullable();
            $table->text('alamat')->nullable();
            $table->string('foto', 255)->nullable();

            // Periode magang/kerja
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();

            // Status employe
            $table->enum('status', ['aktif', 'selesai', 'diberhentikan'])->default('aktif');

            $table->timestamps();
            $table->softDeletes();

            // Indexes untuk performa
            $table->index('nama');
            $table->index('kampus_id');
            $table->index('divisi_id');
            $table->index('tanggal_mulai');
            $table->index('tanggal_selesai');
            $table->index('status');

            // Setiap pengguna hanya boleh memiliki satu entri karyawan
            $table->unique('pengguna_id');

            // Foreign key akan ditambahkan pada migrasi terpisah
            // setelah tabel 'kampus' dan 'divisi' dibuat.
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('karyawan');
    }
}