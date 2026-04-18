<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropIpLokasiTable extends Migration
{
    /**
     * Run the migrations: drop ip_lokasi table.
     */
    public function up()
    {
        Schema::dropIfExists('ip_lokasi');
    }

    /**
     * Reverse the migrations: recreate ip_lokasi table (best-effort based on original schema).
     */
    public function down()
    {
        Schema::create('ip_lokasi', function (Blueprint $table) {
            $table->id();

            // Identitas & metadata lokasi
            $table->string('nama', 100);
            $table->string('alamat_ip', 45)->unique();
            $table->string('lokasi', 255)->nullable();

            // Koordinat & radius geofencing
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->unsignedInteger('radius_meter')->default(100);

            // Status aktif
            $table->boolean('aktif')->default(true);

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('nama');
            $table->index('aktif');
        });
    }
}