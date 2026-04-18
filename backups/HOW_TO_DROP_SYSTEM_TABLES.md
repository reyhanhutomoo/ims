# Panduan Drop Tabel Sistem Laravel

## ⚠️ PERINGATAN PENTING

Sebelum menghapus tabel `reset_kata_sandi` dan `pekerjaan_gagal`, pastikan Anda memahami konsekuensinya:

### Tabel `reset_kata_sandi` (password_resets)
- **Fungsi**: Menyimpan token untuk reset password pengguna
- **Dampak jika dihapus**: Fitur "Lupa Password" tidak akan berfungsi
- **Alternatif**: 
  - Tambahkan kolom `token_reset_password` dan `token_kadaluarsa` di tabel `pengguna`
  - Atau gunakan sistem reset password via email tanpa menyimpan token di database

### Tabel `pekerjaan_gagal` (failed_jobs)
- **Fungsi**: Menyimpan log queue jobs yang gagal dieksekusi
- **Dampak jika dihapus**: Tidak bisa tracking jobs yang gagal
- **Alternatif**: 
  - Gunakan queue driver `sync` (tanpa queue)
  - Atau gunakan logging ke file untuk tracking error

---

## 📋 Langkah-Langkah Drop Tabel

### 1. Backup Database
```bash
# Backup full database
mysqldump -u root -p ims > backups/backup_before_drop_system_$(date +%Y%m%d_%H%M%S).sql

# Atau backup hanya 2 tabel ini
mysqldump -u root -p ims reset_kata_sandi pekerjaan_gagal > backups/backup_system_tables.sql
```

### 2. Jalankan Script Drop
```bash
# Masuk ke MySQL
mysql -u root -p ims

# Jalankan script
source backups/drop_system_tables.sql

# Atau langsung dari command line
mysql -u root -p ims < backups/drop_system_tables.sql
```

### 3. Pindahkan Migrasi ke Backup
```bash
# Pindahkan migrasi ke folder backup
move database\migrations\2014_10_12_100000_create_password_resets_table.php database\migrations\backup_old_migrations\
move database\migrations\2019_08_19_000000_create_failed_jobs_table.php database\migrations\backup_old_migrations\
```

### 4. Update Konfigurasi Laravel

#### A. Nonaktifkan Password Reset (jika tidak digunakan)

**File: `config/auth.php`**
```php
'passwords' => [
    'users' => [
        'provider' => 'users',
        'table' => null, // Set null untuk disable
        'expire' => 60,
        'throttle' => 60,
    ],
],
```

#### B. Ubah Queue Driver ke Sync

**File: `.env`**
```env
QUEUE_CONNECTION=sync
```

Atau jika masih ingin menggunakan queue, gunakan driver lain:
```env
QUEUE_CONNECTION=redis
# atau
QUEUE_CONNECTION=database  # Tapi perlu tabel 'jobs' baru
```

### 5. Verifikasi

```sql
-- Cek tabel yang tersisa
SHOW TABLES;

-- Seharusnya hanya ada 12 tabel bisnis:
-- 1. pengguna
-- 2. karyawan
-- 3. peran
-- 4. peran_pengguna
-- 5. kehadiran
-- 6. cuti
-- 7. laporan_mingguan
-- 8. pengajuan_moa
-- 9. kampus
-- 10. divisi
-- 11. hari_libur
-- 12. ip_lokasi
```

---

## 🔄 Cara Restore (Jika Diperlukan)

Jika ternyata Anda masih membutuhkan tabel tersebut:

```bash
# Restore dari backup
mysql -u root -p ims < backups/backup_system_tables.sql

# Kembalikan migrasi
move database\migrations\backup_old_migrations\2014_10_12_100000_create_password_resets_table.php database\migrations\
move database\migrations\backup_old_migrations\2019_08_19_000000_create_failed_jobs_table.php database\migrations\
```

---

## ✅ Checklist Sebelum Drop

- [ ] Sudah backup database lengkap
- [ ] Sudah memastikan fitur reset password tidak digunakan
- [ ] Sudah memastikan queue system tidak digunakan atau sudah migrasi ke driver lain
- [ ] Sudah membaca dan memahami semua konsekuensi
- [ ] Sudah siap update konfigurasi Laravel setelah drop

---

## 📞 Catatan Tambahan

Jika Anda masih ragu, lebih baik **JANGAN drop tabel ini** karena:
1. Ukuran tabel ini sangat kecil (biasanya < 1MB)
2. Tidak memberatkan performa database
3. Mungkin dibutuhkan di masa depan

**Rekomendasi**: Biarkan tabel ini tetap ada kecuali Anda 100% yakin tidak akan menggunakannya.