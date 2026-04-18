-- Dry-run: Drop English-named tables that are not used by current models
-- Source DB config: .env DB_DATABASE=u230210512_ims, DB_HOST=127.0.0.1, DB_USERNAME=root

-- Classification (from backups/tables_list.txt):
-- Keep (Indonesian schema used by models):
--   pengguna, peran, peran_pengguna, karyawan, kampus, divisi, kehadiran,
--   hari_libur, ip_lokasi, laporan_mingguan, pengajuan_moa, statusatten, migrations
--
-- Explanations:
--   - pengguna: used by App\User model
--   - peran: used by App\Role model
--   - peran_pengguna: pivot used by User-Role relation
--   - karyawan: used by App\Employee model
--   - kampus: used by App\Campus model
--   - divisi: used by App\Division model
--   - kehadiran: used by App\Attendance model
--   - hari_libur: used by App\Holiday model
--   - ip_lokasi: used by seeder/migration
--   - laporan_mingguan: used by App\WeeklyReports model
--   - pengajuan_moa: used by App\Moa model
--   - statusatten: used by App\StatusAtten model and seeders
--   - migrations: core Laravel migration registry
--
-- Drop candidates (English-named, duplicated/legacy, not referenced by current models):
--   attendances, campus, division, employees, holidays, leaves, moas,
--   weeklyreports, role_user, roles, users
--
-- Notes on core/optional English tables:
--   - password_resets: core for password reset tokens; KEEP unless confirmed unused
--   - failed_jobs: used by queue when not 'sync'; currently QUEUE_CONNECTION=sync, but KEEP unless confirmed unused
--   - personal_access_tokens: Laravel Sanctum; KEEP unless confirmed unused
--
-- Execution safety:
--   - All drops use IF EXISTS to avoid errors if a table is already absent.
--   - This script excludes core tables unless explicitly uncommented below.

USE `u230210512_ims`;

-- DROP English duplicates (safe set)
DROP TABLE IF EXISTS `attendances`;
DROP TABLE IF EXISTS `campus`;
DROP TABLE IF EXISTS `division`;
DROP TABLE IF EXISTS `employees`;
DROP TABLE IF EXISTS `holidays`;
DROP TABLE IF EXISTS `leaves`;
DROP TABLE IF EXISTS `moas`;
DROP TABLE IF EXISTS `weeklyreports`;
DROP TABLE IF EXISTS `role_user`;
DROP TABLE IF EXISTS `roles`;
DROP TABLE IF EXISTS `users`;

-- OPTIONAL: Uncomment if confirmed unused
-- DROP TABLE IF EXISTS `failed_jobs`;
-- DROP TABLE IF EXISTS `password_resets`;
-- DROP TABLE IF EXISTS `personal_access_tokens`;