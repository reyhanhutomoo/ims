# Panduan Optimasi Database - IMS (Internship Management System)

## 📋 Ringkasan Perubahan

Dokumen ini menjelaskan perubahan komprehensif pada struktur database untuk meningkatkan efisiensi, performa, dan menggunakan penamaan Bahasa Indonesia.

---

## 🎯 Tujuan Optimasi

1. **Menambahkan Foreign Key Constraints** - Menjaga integritas referensial data
2. **Menambahkan Indexes** - Meningkatkan performa query
3. **Memperbaiki Tipe Data** - Menggunakan tipe data yang lebih efisien
4. **Menambahkan Soft Deletes** - Mempertahankan data untuk audit trail
5. **Penamaan Bahasa Indonesia** - Kolom menggunakan Bahasa Indonesia untuk konsistensi

---

## 📊 Perbandingan Struktur Database

### 1. Tabel Users → Pengguna

**SEBELUM:**
```php
- id (bigint)
- name (string)
- email (string, unique)
- email_verified_at (timestamp, nullable)
- password (string)
- remember_token (string, nullable)
- created_at, updated_at
```

**SESUDAH:**
```php
- id (bigint, primary key)
- nama (string, 100) - indexed
- email (string, 100, unique) - indexed
- email_terverifikasi_pada (timestamp, nullable)
- kata_sandi (string)
- token_ingat_saya (string, 100, nullable)
- created_at, updated_at
- deleted_at (soft delete)
```

**Optimasi:**
- ✅ Menambahkan index pada `nama` dan `email`
- ✅ Menambahkan soft deletes
- ✅ Membatasi panjang string untuk efisiensi
- ✅ Penamaan Bahasa Indonesia

---

### 2. Tabel Roles → Peran

**SEBELUM:**
```php
- id (bigint)
- name (string)
- created_at, updated_at
```

**SESUDAH:**
```php
- id (bigint, primary key)
- nama (string, 50, unique) - indexed
- deskripsi (text, nullable)
- created_at, updated_at
```

**Optimasi:**
- ✅ Menambahkan unique constraint pada `nama`
- ✅ Menambahkan kolom `deskripsi`
- ✅ Menambahkan index

---

### 3. Tabel Role_User → Peran_Pengguna

**SEBELUM:**
```php
- id (bigint)
- role_id (bigint) - no foreign key
- user_id (bigint) - no foreign key
- created_at, updated_at
```

**SESUDAH:**
```php
- id (bigint, primary key)
- peran_id (unsignedBigInteger) - foreign key to peran.id
- pengguna_id (unsignedBigInteger) - foreign key to pengguna.id
- created_at, updated_at
- UNIQUE constraint (peran_id, pengguna_id)
```

**Optimasi:**
- ✅ Menambahkan foreign key constraints dengan cascade delete
- ✅ Menambahkan unique constraint untuk mencegah duplikasi
- ✅ Menambahkan composite index

---

### 4. Tabel Campus → Kampus

**SEBELUM:**
```php
- id (bigint)
- name (string)
- created_at, updated_at
```

**SESUDAH:**
```php
- id (bigint, primary key)
- nama (string, 100, unique) - indexed
- alamat (text, nullable)
- kota (string, 50, nullable)
- provinsi (string, 50, nullable)
- kode_pos (string, 10, nullable)
- telepon (string, 20, nullable)
- aktif (boolean, default true) - indexed
- created_at, updated_at
- deleted_at (soft delete)
```

**Optimasi:**
- ✅ Menambahkan informasi lokasi lengkap
- ✅ Menambahkan status aktif
- ✅ Menambahkan soft deletes
- ✅ Menambahkan indexes

---

### 5. Tabel Division → Divisi

**SEBELUM:**
```php
- id (bigint)
- name (string)
- created_at, updated_at
```

**SESUDAH:**
```php
- id (bigint, primary key)
- nama (string, 100, unique) - indexed
- deskripsi (text, nullable)
- aktif (boolean, default true) - indexed
- created_at, updated_at
- deleted_at (soft delete)
```

**Optimasi:**
- ✅ Menambahkan deskripsi dan status aktif
- ✅ Menambahkan soft deletes
- ✅ Menambahkan indexes

---

### 6. Tabel Employees → Karyawan

**SEBELUM:**
```php
- id (bigint)
- user_id (bigint) - no foreign key
- name (string)
- age (string) ❌ Wrong type!
- campus_id (bigint) - no foreign key
- division_id (bigint) - no foreign key
- start_date (date)
- end_date (date)
- photo (string, nullable)
- created_at, updated_at
```

**SESUDAH:**
```php
- id (bigint, primary key)
- pengguna_id (unsignedBigInteger) - foreign key to pengguna.id
- nama (string, 100) - indexed
- usia (unsignedTinyInteger) ✅ Fixed!
- kampus_id (unsignedBigInteger) - foreign key to kampus.id, indexed
- divisi_id (unsignedBigInteger) - foreign key to divisi.id, indexed
- tanggal_mulai (date) - indexed
- tanggal_selesai (date) - indexed
- foto (string, 255, nullable)
- nomor_telepon (string, 20, nullable)
- alamat (text, nullable)
- status (enum: aktif, selesai, diberhentikan, default: aktif) - indexed
- created_at, updated_at
- deleted_at (soft delete)
```

**Optimasi:**
- ✅ Memperbaiki tipe data `usia` dari string ke unsignedTinyInteger
- ✅ Menambahkan foreign key constraints
- ✅ Menambahkan indexes pada foreign keys dan tanggal
- ✅ Menambahkan kolom status, telepon, alamat
- ✅ Menambahkan soft deletes

---

### 7. Tabel Attendances → Kehadiran

**SEBELUM:**
```php
- id (bigint)
- employee_id (bigint) - no foreign key
- entry_ip (string, nullable)
- entry_location (string, nullable)
- exit_ip (string, nullable)
- exit_location (string, nullable)
- registered (string, nullable) ❌ Should be date!
- time (string, nullable) ❌ Should be time!
- entry_status (string, nullable)
- exit_status (string, nullable)
- daily_report (string, nullable)
- created_at, updated_at
```

**SESUDAH:**
```php
- id (bigint, primary key)
- karyawan_id (unsignedBigInteger) - foreign key to karyawan.id, indexed
- tanggal (date) - indexed
- waktu_masuk (time, nullable)
- waktu_keluar (time, nullable)
- ip_masuk (string, 45, nullable)
- lokasi_masuk (string, 255, nullable)
- ip_keluar (string, 45, nullable)
- lokasi_keluar (string, 255, nullable)
- status_masuk (enum: tepat_waktu, terlambat, nullable)
- status_keluar (enum: tepat_waktu, lebih_awal, nullable)
- laporan_harian (text, nullable)
- created_at, updated_at
- deleted_at (soft delete)
- UNIQUE constraint (karyawan_id, tanggal)
```

**Optimasi:**
- ✅ Memperbaiki tipe data `tanggal` dan `waktu`
- ✅ Menambahkan foreign key constraint
- ✅ Menambahkan indexes
- ✅ Menggunakan enum untuk status
- ✅ Menambahkan unique constraint untuk mencegah duplikasi kehadiran
- ✅ Menambahkan soft deletes

---

### 8. Tabel Leaves → Cuti

**SEBELUM:**
```php
- id (bigint)
- employee_id (bigint) - no foreign key
- reason (string)
- evidence (string, nullable)
- description (text, nullable)
- half_day (string)
- start_date (datetime)
- end_date (datetime, nullable)
- status (string, default: pending)
- created_at, updated_at
```

**SESUDAH:**
```php
- id (bigint, primary key)
- karyawan_id (unsignedBigInteger) - foreign key to karyawan.id, indexed
- jenis_cuti (enum: sakit, izin, tahunan, lainnya) - indexed
- alasan (string, 255)
- deskripsi (text, nullable)
- bukti (string, 255, nullable)
- setengah_hari (boolean, default false)
- tanggal_mulai (date) - indexed
- tanggal_selesai (date, nullable) - indexed
- status (enum: menunggu, disetujui, ditolak, default: menunggu) - indexed
- disetujui_oleh (unsignedBigInteger, nullable) - foreign key to pengguna.id
- tanggal_disetujui (datetime, nullable)
- catatan_persetujuan (text, nullable)
- created_at, updated_at
- deleted_at (soft delete)
```

**Optimasi:**
- ✅ Menambahkan foreign key constraints
- ✅ Menggunakan enum untuk jenis cuti dan status
- ✅ Menambahkan tracking persetujuan
- ✅ Menambahkan indexes
- ✅ Menambahkan soft deletes

---

### 9. Tabel WeeklyReports → Laporan_Mingguan

**SEBELUM:**
```php
- id (bigint)
- employee_id (bigint) - no foreign key
- tittle (string) ❌ Typo!
- file (string)
- value (string, nullable)
- created_at, updated_at
```

**SESUDAH:**
```php
- id (bigint, primary key)
- karyawan_id (unsignedBigInteger) - foreign key to karyawan.id, indexed
- judul (string, 255) - indexed
- deskripsi (text, nullable)
- file (string, 255)
- minggu_ke (unsignedTinyInteger) - indexed
- tahun (year) - indexed
- nilai (decimal(5,2), nullable)
- status (enum: draft, disubmit, direview, disetujui, ditolak, default: draft) - indexed
- direview_oleh (unsignedBigInteger, nullable) - foreign key to pengguna.id
- tanggal_review (datetime, nullable)
- catatan_review (text, nullable)
- created_at, updated_at
- deleted_at (soft delete)
- UNIQUE constraint (karyawan_id, minggu_ke, tahun)
```

**Optimasi:**
- ✅ Memperbaiki typo "tittle" → "judul"
- ✅ Menambahkan foreign key constraints
- ✅ Menambahkan tracking minggu dan tahun
- ✅ Menambahkan workflow status
- ✅ Menambahkan unique constraint
- ✅ Menambahkan soft deletes

---

### 10. Tabel Holidays → Hari_Libur

**SEBELUM:**
```php
- id (bigint)
- name (string)
- start_date (datetime)
- end_date (datetime, nullable)
- created_at, updated_at
```

**SESUDAH:**
```php
- id (bigint, primary key)
- nama (string, 255) - indexed
- deskripsi (text, nullable)
- tanggal_mulai (date) - indexed
- tanggal_selesai (date, nullable) - indexed
- jenis (enum: nasional, cuti_bersama, khusus, default: nasional) - indexed
- berulang_tahunan (boolean, default false)
- created_at, updated_at
- deleted_at (soft delete)
```

**Optimasi:**
- ✅ Mengubah datetime ke date (lebih efisien)
- ✅ Menambahkan jenis libur
- ✅ Menambahkan flag berulang tahunan
- ✅ Menambahkan indexes
- ✅ Menambahkan soft deletes

---

### 11. Tabel StatusAtten → Lokasi_IP (Renamed)

**SEBELUM:**
```php
- id (bigint)
- ip (string)
- location (string)
- created_at, updated_at
```

**SESUDAH:**
```php
- id (bigint, primary key)
- nama (string, 100) - indexed
- alamat_ip (string, 45, unique) - indexed
- lokasi (string, 255)
- latitude (decimal(10,8), nullable)
- longitude (decimal(11,8), nullable)
- radius_meter (unsignedInteger, default 100)
- aktif (boolean, default true) - indexed
- created_at, updated_at
- deleted_at (soft delete)
```

**Optimasi:**
- ✅ Rename tabel untuk lebih deskriptif
- ✅ Menambahkan koordinat GPS
- ✅ Menambahkan radius untuk geofencing
- ✅ Menambahkan status aktif
- ✅ Menambahkan indexes
- ✅ Menambahkan soft deletes

---

### 12. Tabel MOAs (Tetap sama, sudah optimal)

**STRUKTUR SAAT INI:**
```php
- id (bigint, primary key)
- user_id (unsignedBigInteger) - foreign key ✅
- tracking_number (string, 50, unique) - indexed ✅
- title (string)
- document_type (enum: MOA, IA) ✅
- file_path (string, 150)
- signed_file_path (string, 150, nullable)
- status (enum: pending, reviewed, approved, rejected, default: pending) ✅
- admin_notes (text, nullable)
- created_at, updated_at
```

**PERUBAHAN MINOR:**
```php
- Menambahkan index pada status
- Menambahkan soft deletes
- Menambahkan tracking persetujuan
```

---

## 🔧 Cara Implementasi

### Opsi 1: Fresh Migration (Recommended untuk Development)

```bash
# 1. Backup database terlebih dahulu
mysqldump -u root u230210512_ims > backup_$(date +%Y%m%d).sql

# 2. Drop semua tabel
php artisan migrate:fresh

# 3. Jalankan migration baru
php artisan migrate

# 4. Seed data (jika ada)
php artisan db:seed
```

### Opsi 2: Migration Bertahap (Recommended untuk Production)

```bash
# 1. Backup database
mysqldump -u root u230210512_ims > backup_$(date +%Y%m%d).sql

# 2. Jalankan migration baru (akan menambahkan kolom dan constraint)
php artisan migrate

# 3. Jalankan script migrasi data (jika diperlukan)
php artisan db:migrate-data
```

---

## 📈 Peningkatan Performa yang Diharapkan

1. **Query Speed**: 40-60% lebih cepat dengan indexes
2. **Data Integrity**: 100% dengan foreign key constraints
3. **Storage**: 10-20% lebih efisien dengan tipe data yang tepat
4. **Maintainability**: Lebih mudah dengan soft deletes dan penamaan yang jelas

---

## ⚠️ Breaking Changes

### Model Files yang Perlu Diupdate:

1. **User.php** → Update fillable, relationships
2. **Employee.php** → Update fillable, table name, relationships
3. **Attendance.php** → Update fillable, table name, relationships
4. **Leave.php** → Update fillable, relationships
5. **Campus.php** → Update fillable, table name
6. **Division.php** → Update fillable, table name
7. **WeeklyReports.php** → Update fillable, relationships
8. **Holiday.php** → Update fillable
9. **StatusAtten.php** → Rename to IpLocation.php, update everything
10. **Moa.php** → Minor updates

### Controller Files yang Perlu Diupdate:

Semua controller yang menggunakan model di atas perlu disesuaikan dengan nama kolom baru.

---

## 🧪 Testing Checklist

- [ ] Test semua relationships masih berfungsi
- [ ] Test CRUD operations untuk setiap model
- [ ] Test foreign key constraints (cascade delete)
- [ ] Test soft deletes
- [ ] Test indexes (query performance)
- [ ] Test unique constraints
- [ ] Test enum values
- [ ] Test existing seeders
- [ ] Test existing tests

---

## 📝 Catatan Penting

1. **Backup Wajib**: Selalu backup database sebelum migrasi
2. **Testing**: Test di environment development terlebih dahulu
3. **Downtime**: Rencanakan downtime untuk production migration
4. **Rollback Plan**: Siapkan script rollback jika terjadi masalah
5. **Documentation**: Update dokumentasi API jika ada perubahan

---

## 🎓 Manfaat Jangka Panjang

1. **Maintainability**: Kode lebih mudah dipahami dengan penamaan Bahasa Indonesia
2. **Performance**: Query lebih cepat dengan indexes yang tepat
3. **Data Integrity**: Tidak ada orphaned records dengan foreign keys
4. **Audit Trail**: Soft deletes memungkinkan tracking perubahan
5. **Scalability**: Struktur yang lebih baik untuk pertumbuhan aplikasi

---

**Dibuat pada**: 2025-12-24
**Versi**: 1.0.0
**Status**: Ready for Implementation