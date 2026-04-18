-- ============================================
-- DROP TABEL SISTEM LARAVEL YANG TIDAK DIGUNAKAN
-- ============================================
-- File: drop_system_tables.sql
-- Tanggal: 2025-12-30
-- Deskripsi: Script untuk menghapus tabel sistem Laravel
--            yang tidak digunakan dalam aplikasi
-- ============================================

-- PERINGATAN:
-- 1. Tabel 'reset_kata_sandi' digunakan untuk fitur reset password
--    Jika Anda masih menggunakan fitur reset password, JANGAN drop tabel ini!
-- 2. Tabel 'pekerjaan_gagal' digunakan untuk queue jobs yang gagal
--    Jika Anda menggunakan queue system, JANGAN drop tabel ini!

-- Backup terlebih dahulu sebelum menjalankan script ini!
-- mysqldump -u root -p ims > backup_before_drop_system_tables.sql

-- ============================================
-- DROP TABLES
-- ============================================

-- Drop tabel reset_kata_sandi (password resets)
DROP TABLE IF EXISTS `reset_kata_sandi`;

-- Drop tabel pekerjaan_gagal (failed jobs)
DROP TABLE IF EXISTS `pekerjaan_gagal`;

-- ============================================
-- VERIFIKASI
-- ============================================
-- Jalankan query berikut untuk memverifikasi tabel sudah terhapus:
-- SHOW TABLES;

-- ============================================
-- CATATAN PENTING
-- ============================================
-- Setelah drop tabel ini, Anda perlu:
-- 1. Hapus atau comment migrasi terkait:
--    - database/migrations/2014_10_12_100000_create_password_resets_table.php
--    - database/migrations/2019_08_19_000000_create_failed_jobs_table.php
--
-- 2. Jika masih ingin menggunakan fitur reset password:
--    - Gunakan token di tabel 'pengguna' atau
--    - Buat tabel baru dengan struktur yang lebih sederhana
--
-- 3. Jika masih ingin menggunakan queue system:
--    - Gunakan driver 'sync' atau 'database' dengan tabel custom
--    - Atau gunakan Redis/SQS untuk queue