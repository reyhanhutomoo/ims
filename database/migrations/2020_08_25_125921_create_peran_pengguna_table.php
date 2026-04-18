<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePeranPenggunaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('peran_pengguna', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('peran_id');
            $table->unsignedBigInteger('pengguna_id');
            $table->timestamps();

            // Foreign key constraints dengan cascade delete
            $table->foreign('peran_id')
                  ->references('id')
                  ->on('peran')
                  ->onDelete('cascade');
            
            $table->foreign('pengguna_id')
                  ->references('id')
                  ->on('pengguna')
                  ->onDelete('cascade');

            // Unique constraint untuk mencegah duplikasi
            $table->unique(['peran_id', 'pengguna_id']);

            // Composite index untuk performa
            $table->index(['peran_id', 'pengguna_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('peran_pengguna');
    }
}