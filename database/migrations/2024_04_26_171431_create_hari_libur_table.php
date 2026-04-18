<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHariLiburTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hari_libur', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 255);
            $table->text('deskripsi')->nullable();
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
            $table->enum('jenis', ['nasional', 'cuti_bersama', 'khusus'])->default('nasional');
            $table->boolean('berulang_tahunan')->default(false);
            $table->timestamps();
            $table->softDeletes();

            // Indexes untuk performa
            $table->index('nama');
            $table->index('tanggal_mulai');
            $table->index('tanggal_selesai');
            $table->index('jenis');
            $table->index('berulang_tahunan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hari_libur');
    }
}