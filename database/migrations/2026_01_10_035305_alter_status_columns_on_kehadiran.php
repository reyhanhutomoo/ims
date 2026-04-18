<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Change status_masuk and status_keluar to VARCHAR to support values like "Valid"/"Invalid".
     */
    public function up(): void
    {
        // MySQL ALTER TABLE to widen ENUM to VARCHAR without requiring doctrine/dbal
        DB::statement("ALTER TABLE `kehadiran` MODIFY `status_masuk` VARCHAR(32) NULL");
        DB::statement("ALTER TABLE `kehadiran` MODIFY `status_keluar` VARCHAR(32) NULL");
    }

    /**
     * Revert columns back to their original ENUM definitions.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE `kehadiran` MODIFY `status_masuk` ENUM('tepat_waktu','terlambat') NULL");
        DB::statement("ALTER TABLE `kehadiran` MODIFY `status_keluar` ENUM('tepat_waktu','lebih_awal') NULL");
    }
};