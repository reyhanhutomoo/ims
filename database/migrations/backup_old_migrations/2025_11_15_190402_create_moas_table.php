<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMoasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('moas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // ID Mahasiswa yang mengajukan (dari tabel users)
            $table->string('tracking_number', 50)->unique(); // Nomor tracking unik untuk setiap pengajuan
            $table->string('title'); // Judul MOA/IA atau Nama Instansi
            $table->enum('document_type', ['MOA', 'IA']); // Jenis Dokumen
            $table->string('file_path', 150); // Path ke draf file yang di-upload mahasiswa
            $table->string('signed_file_path', 150)->nullable(); // Path ke file final yg sudah TTD (di-upload admin)
            $table->enum('status', ['pending', 'reviewed', 'approved', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable(); // Catatan dari admin (jika ditolak/revisi)
            $table->timestamps(); // otomatis membuat created_at dan updated_at

            // Menghubungkan ke tabel 'users'
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('moas');
    }
}