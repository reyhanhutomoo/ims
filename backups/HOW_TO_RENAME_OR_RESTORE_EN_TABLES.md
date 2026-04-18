# Rename vs Drop for English-Named Tables

Context:
- App is already using Indonesian tables via models: [app.User()](../app/User.php:12), [app.Role()](../app/Role.php:8), [app.Employee()](../app/Employee.php:8), [app.Campus()](../app/Campus.php:9), [app.Division()](../app/Division.php:9), [app.Attendance()](../app/Attendance.php:9), [app.Holiday()](../app/Holiday.php:9), [app.WeeklyReports()](../app/WeeklyReports.php:9), [app.Moa()](../app/Moa.php:9).
- DB target per [.env()](../.env:12): DB_DATABASE=u230210512_ims
- We already created a full backup: [backups.backup_before_drop.sql](backup_before_drop.sql:1)
- We applied a drop script: [backups.drop_english_tables.sql](drop_english_tables.sql:1)

Can we rename the English tables instead of dropping?
- Technically possible, but not to the Indonesian names because those tables already exist and the schemas differ (e.g. users vs pengguna: different column names).
- Safe approaches:
  1) Archive the English tables by renaming them to a non-conflicting prefix (e.g., en_*) so data is preserved but not used by the app.
  2) Restore English tables from backup into a temporary schema, then migrate data into the Indonesian tables with explicit column mapping.

Below are step-by-step instructions for both options on Windows CMD/Laragon.

---

## Option A: Archive English Tables (rename to en_*)

If English tables still existed in the current DB (not dropped yet), you could directly run:
- Example (execute inside MySQL):
  RENAME TABLE
    attendances TO en_attendances,
    campus TO en_campus,
    division TO en_division,
    employees TO en_employees,
    holidays TO en_holidays,
    leaves TO en_leaves,
    moas TO en_moas,
    weeklyreports TO en_weeklyreports,
    role_user TO en_role_user,
    roles TO en_roles,
    users TO en_users;

Since in our case they have been dropped, use Option B to restore them first, then optionally archive.

---

## Option B: Restore English Tables from Backup, Then Archive or Migrate

1) Create a temporary database to restore the backup
- Command:
  mysql -u root -h 127.0.0.1 --port=3306 -e "CREATE DATABASE IF NOT EXISTS u230210512_ims_bak CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

2) Import the pre-drop backup into the temporary DB
- Command:
  mysql -u root -h 127.0.0.1 --port=3306 u230210512_ims_bak < backups\backup_before_drop.sql

3) Archive (cross-database rename) the English tables into the main DB with en_* prefix
- Command (run inside MySQL client):
  RENAME TABLE
    u230210512_ims_bak.attendances TO u230210512_ims.en_attendances,
    u230210512_ims_bak.campus TO u230210512_ims.en_campus,
    u230210512_ims_bak.division TO u230210512_ims.en_division,
    u230210512_ims_bak.employees TO u230210512_ims.en_employees,
    u230210512_ims_bak.holidays TO u230210512_ims.en_holidays,
    u230210512_ims_bak.leaves TO u230210512_ims.en_leaves,
    u230210512_ims_bak.moas TO u230210512_ims.en_moas,
    u230210512_ims_bak.weeklyreports TO u230210512_ims.en_weeklyreports,
    u230210512_ims_bak.role_user TO u230210512_ims.en_role_user,
    u230210512_ims_bak.roles TO u230210512_ims.en_roles,
    u230210512_ims_bak.users TO u230210512_ims.en_users;

- Verify:
  mysql -u root -h 127.0.0.1 --port=3306 -D u230210512_ims -e "SHOW TABLES LIKE 'en_%';"

4) (Optional) Drop the temporary database
- Command:
  mysql -u root -h 127.0.0.1 --port=3306 -e "DROP DATABASE u230210512_ims_bak;"

This preserves all English tables with en_* names, without affecting the app (app uses Indonesian tables).

---

## Option C: Migrate Data from English Tables into Indonesian Tables

If you need the data from English tables moved into the Indonesian tables the app uses, migrate with explicit column mapping. Below are examples for common tables. Adjust mappings to your actual columns if they differ.

Important:
- Backup first: [backups.backup_before_drop.sql](backup_before_drop.sql:1)
- Ensure en_* tables exist (use Option B to restore, then archive to en_*).

1) users -> pengguna
- Mapping:
  - users.id -> pengguna.id
  - users.name -> pengguna.nama
  - users.email -> pengguna.email
  - users.password -> pengguna.kata_sandi
  - users.remember_token -> pengguna.token_ingat_saya
  - users.email_verified_at -> pengguna.email_terverifikasi_pada
  - timestamps -> same
- SQL:
  INSERT INTO pengguna (id, nama, email, kata_sandi, token_ingat_saya, email_terverifikasi_pada, created_at, updated_at)
  SELECT id, name, email, password, remember_token, email_verified_at, created_at, updated_at
  FROM en_users
  ON DUPLICATE KEY UPDATE
    nama=VALUES(nama),
    email=VALUES(email),
    kata_sandi=VALUES(kata_sandi),
    token_ingat_saya=VALUES(token_ingat_saya),
    email_terverifikasi_pada=VALUES(email_terverifikasi_pada),
    updated_at=VALUES(updated_at);

2) roles -> peran
- Mapping:
  - roles.id -> peran.id
  - roles.name -> peran.nama
  - peran.deskripsi (set NULL or a default)
- SQL:
  INSERT INTO peran (id, nama, deskripsi, created_at, updated_at)
  SELECT id, name, NULL, created_at, updated_at
  FROM en_roles
  ON DUPLICATE KEY UPDATE
    nama=VALUES(nama),
    deskripsi=VALUES(deskripsi),
    updated_at=VALUES(updated_at);

3) role_user -> peran_pengguna (pivot)
- Mapping:
  - role_user.role_id -> peran_pengguna.peran_id
  - role_user.user_id -> peran_pengguna.pengguna_id
- SQL:
  INSERT INTO peran_pengguna (peran_id, pengguna_id, created_at, updated_at)
  SELECT role_id, user_id, NOW(), NOW()
  FROM en_role_user
  ON DUPLICATE KEY UPDATE
    peran_id=VALUES(peran_id),
    pengguna_id=VALUES(pengguna_id),
    updated_at=VALUES(updated_at);

4) employees -> karyawan
- Columns often differ. Typical mapping:
  - employees.user_id -> karyawan.pengguna_id
  - employees.name -> karyawan.nama
  - employees.age -> karyawan.usia
  - employees.campus_id -> karyawan.kampus_id
  - employees.division_id -> karyawan.divisi_id
  - employees.start_date -> karyawan.tanggal_mulai
  - employees.end_date -> karyawan.tanggal_selesai
- Example template (adjust to your columns):
  INSERT INTO karyawan (id, pengguna_id, nama, usia, kampus_id, divisi_id, tanggal_mulai, tanggal_selesai, created_at, updated_at)
  SELECT id, user_id, name, age, campus_id, division_id, start_date, end_date, created_at, updated_at
  FROM en_employees
  ON DUPLICATE KEY UPDATE
    pengguna_id=VALUES(pengguna_id),
    nama=VALUES(nama),
    usia=VALUES(usia),
    kampus_id=VALUES(kampus_id),
    divisi_id=VALUES(divisi_id),
    tanggal_mulai=VALUES(tanggal_mulai),
    tanggal_selesai=VALUES(tanggal_selesai),
    updated_at=VALUES(updated_at);

5) campus -> kampus
- Example:
  INSERT INTO kampus (id, nama, alamat, kota, provinsi, kode_pos, telepon, aktif, created_at, updated_at)
  SELECT id, name, address, city, province, postal_code, phone, 1, created_at, updated_at
  FROM en_campus
  ON DUPLICATE KEY UPDATE
    nama=VALUES(nama),
    alamat=VALUES(alamat),
    kota=VALUES(kota),
    provinsi=VALUES(provinsi),
    kode_pos=VALUES(kode_pos),
    telepon=VALUES(telepon),
    updated_at=VALUES(updated_at);

6) division -> divisi
- Example:
  INSERT INTO divisi (id, nama, deskripsi, aktif, created_at, updated_at)
  SELECT id, name, description, 1, created_at, updated_at
  FROM en_division
  ON DUPLICATE KEY UPDATE
    nama=VALUES(nama),
    deskripsi=VALUES(deskripsi),
    updated_at=VALUES(updated_at);

7) holidays -> hari_libur
- Example:
  INSERT INTO hari_libur (id, nama, deskripsi, tanggal_mulai, tanggal_selesai, jenis, berulang_tahunan, created_at, updated_at)
  SELECT id, name, description, start_date, end_date, type, is_recurring, created_at, updated_at
  FROM en_holidays
  ON DUPLICATE KEY UPDATE
    nama=VALUES(nama),
    deskripsi=VALUES(deskripsi),
    tanggal_mulai=VALUES(tanggal_mulai),
    tanggal_selesai=VALUES(tanggal_selesai),
    jenis=VALUES(jenis),
    berulang_tahunan=VALUES(berulang_tahunan),
    updated_at=VALUES(updated_at);

8) attendances -> kehadiran
- Example:
  INSERT INTO kehadiran (id, karyawan_id, tanggal, waktu_masuk, waktu_keluar, ip_masuk, lokasi_masuk, ip_keluar, lokasi_keluar, status_masuk, status_keluar, laporan_harian, created_at, updated_at)
  SELECT id, employee_id, date, checkin_time, checkout_time, checkin_ip, checkin_location, checkout_ip, checkout_location, checkin_status, checkout_status, daily_report, created_at, updated_at
  FROM en_attendances
  ON DUPLICATE KEY UPDATE
    karyawan_id=VALUES(karyawan_id),
    tanggal=VALUES(tanggal),
    waktu_masuk=VALUES(waktu_masuk),
    waktu_keluar=VALUES(waktu_keluar),
    updated_at=VALUES(updated_at);

9) weeklyreports -> laporan_mingguan
- Example:
  INSERT INTO laporan_mingguan (id, karyawan_id, judul, deskripsi, file, minggu_ke, tahun, nilai, status, direview_oleh, tanggal_review, catatan_review, created_at, updated_at)
  SELECT id, employee_id, title, description, file_path, week_no, year_no, score, status, reviewed_by, reviewed_at, review_notes, created_at, updated_at
  FROM en_weeklyreports
  ON DUPLICATE KEY UPDATE
    judul=VALUES(judul),
    deskripsi=VALUES(deskripsi),
    updated_at=VALUES(updated_at);

10) moas -> pengajuan_moa
- Example:
  INSERT INTO pengajuan_moa (id, pengguna_id, nomor_pelacakan, judul, jenis_dokumen, path_berkas, path_berkas_ttd, status, catatan_admin, created_at, updated_at, deleted_at)
  SELECT id, user_id, tracking_number, title, document_type, file_path, signed_file_path, status, admin_notes, created_at, updated_at, NULL
  FROM en_moas
  ON DUPLICATE KEY UPDATE
    judul=VALUES(judul),
    jenis_dokumen=VALUES(jenis_dokumen),
    path_berkas=VALUES(path_berkas),
    path_berkas_ttd=VALUES(path_berkas_ttd),
    status=VALUES(status),
    catatan_admin=VALUES(catatan_admin),
    updated_at=VALUES(updated_at);

After migration:
- You can keep en_* tables as historical reference, or drop them if no longer needed.

---

## One-liner helpers (Windows CMD)

- Create temp DB, restore backup into it:
  mysql -u root -h 127.0.0.1 --port=3306 -e "CREATE DATABASE IF NOT EXISTS u230210512_ims_bak" && mysql -u root -h 127.0.0.1 --port=3306 u230210512_ims_bak < backups\backup_before_drop.sql

- Show archived en_* tables:
  mysql -u root -h 127.0.0.1 --port=3306 -D u230210512_ims -e "SHOW TABLES LIKE 'en_%';"

Notes:
- If your MySQL requires a password, add -p and enter it when prompted.
- Always test INSERT ... SELECT mappings on a copy or with LIMIT before bulk migration.
- Keep backups: [backups.backup_before_drop.sql](backup_before_drop.sql:1). You can recreate the drop script or adjust it at [backups.drop_english_tables.sql](drop_english_tables.sql:1).