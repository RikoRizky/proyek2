# Sistem Informasi Penjaminan Mutu & Monitoring Akreditasi Perguruan Tinggi (PPEPP)

Sistem Informasi Penjaminan Mutu & Monitoring Akreditasi adalah platform berbasis web yang dirancang untuk mendigitalisasi, memonitor, dan mengelola dokumen akreditasi perguruan tinggi berdasarkan siklus **PPEPP** (*Penetapan, Pelaksanaan, Evaluasi, Pengendalian, dan Peningkatan*). 

Sistem ini mendukung pengelolaan 9 Kriteria Akreditasi secara dinamis dengan pembagian akses multi-pengguna (*Multi-Role Authorization*) untuk menjamin kolaborasi yang efektif antara administrator, perguruan tinggi, dan program studi.

---

## 🌟 Fitur Utama

### 1. Autentikasi & Multi-Role Authorization
Sistem memisahkan akses pengguna ke dalam 3 role utama:
*   **Administrator (Admin)**: 
    *   Mengelola akun pengguna, profil Perguruan Tinggi (Perti), dan Program Studi (Prodi).
    *   Mengonfigurasi Kriteria (Modul) & Butir Dokumen (Persyaratan/Requirements).
    *   Memantau seluruh unggahan dokumen dan progres akreditasi semua prodi.
    *   Mengunduh laporan ringkasan akreditasi institusi dalam format PDF.
    *   Melihat daftar permintaan konsultasi (discussions).
*   **Perguruan Tinggi (Perti)**:
    *   Mengelola profil dan akun Program Studi (Prodi) di bawah naungannya.
    *   Memantau progres pemenuhan dokumen dari setiap Program Studi secara riil-time per kriteria.
    *   Mengunduh laporan ringkasan akreditasi institusinya sendiri.
*   **Program Studi / Unit Kerja (Prodi)**:
    *   Mengunggah berkas dokumen akreditasi (PDF, Excel) untuk setiap butir persyaratan.
    *   Mendukung pengunggahan multi-dokumen, integrasi link Google Drive, dan retensi berkas sebelumnya.
    *   Sistem versi otomatis (*Versioning*) untuk pelacakan riwayat dokumen yang diperbarui.
    *   Melihat visualisasi progres pemenuhan kriteria akreditasi program studi sendiri.
    *   Mengunduh laporan ringkasan akreditasi internal program studi.

### 2. Manajemen Dokumen Lanjutan (Submissions)
*   **Batch Upload**: Mengunggah berkas untuk beberapa persyaratan sekaligus dalam satu kali klik per kriteria.
*   **Penyimpanan Multi-File**: Mengunggah lebih dari satu berkas dokumen untuk satu butir persyaratan.
*   **Integrasi Google Drive**: Menyertakan tautan/link Google Drive (misal: dokumen pendukung berukuran besar) bersamaan dengan berkas fisik.
*   **Version Control**: Melacak versi unggahan (Versi 1, Versi 2, dst.) dengan kemampuan melihat berkas versi lama dan mengaktifkan retensi dokumen (*keep files*).
*   **Dokumen Viewer Terintegrasi**: Membuka dan membaca dokumen PDF secara langsung di dalam aplikasi (*inline browser viewer*).

### 3. Formulir Konsultasi & Diskusi Publik
*   Menyediakan formulir konsultasi interaktif bagi publik/mitra untuk berdiskusi mengenai kebutuhan sistem penjaminan mutu, sistem saat ini, serta rencana investasi.

### 4. Ekspor Laporan PDF Ringkas
*   Membuat laporan ringkasan akreditasi menggunakan pustaka `laravel-dompdf` yang menghasilkan file laporan secara dinamis dan rapi untuk kebutuhan evaluasi internal maupun eksternal.

---

## 🛠️ Spesifikasi Teknologi (Tech Stack)

Sistem ini dikembangkan menggunakan teknologi modern:
*   **Core Framework**: PHP ^8.2 & Laravel ^11.31
*   **Starter Kit**: Laravel Breeze (Blade/Tailwind CSS)
*   **Desain & Styling**: Tailwind CSS, Vanilla CSS
*   **Pustaka PDF**: `barryvdh/laravel-dompdf ^3.1`
*   **Manajer Dependensi**: Composer (PHP) & NPM (Node.js)
*   **Database**: MySQL / SQLite (mendukung migrasi penuh & pengindeksan optimal)

---

## 🚀 Langkah Instalasi & Konfigurasi

Ikuti langkah-langkah di bawah ini untuk menjalankan aplikasi di lingkungan lokal Anda:

### 1. Prasyarat (Prerequisites)
Pastikan Anda sudah menginstal:
*   PHP >= 8.2 (dengan ekstensi gd, pdo, sqlite, dll.)
*   Composer
*   Node.js & NPM
*   Database server (MySQL/MariaDB) jika ingin menggunakan server MySQL

### 2. Kloning Repositori & Install Dependensi
```bash
# Install dependensi PHP (Composer)
composer install

# Install dependensi Javascript (NPM)
npm install
```

### 3. Konfigurasi Environment File
Salin file konfigurasi lingkungan `.env.example` ke `.env`:
```bash
cp .env.example .env
```
Sesuaikan konfigurasi database Anda di dalam file `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database_anda
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Generate Application Key
```bash
php artisan key:generate
```

### 5. Jalankan Migrasi & Seed Data Demo
Sistem ini dilengkapi dengan database seeder yang memuat 9 Kriteria Akreditasi (ppepp) beserta akun demo:
```bash
# Menjalankan migrasi tabel database dan memasukkan data demo
php artisan migrate --seed
```

**Informasi Akun Demo (Default):**
*   **Administrator**:
    *   Email: `admin@gmail.com`
    *   Password: `Admin123`
*   **Perguruan Tinggi (Perti)**:
    *   Email: `ulbi@gmail.com`
    *   Password: `Ulbi1234`
*   **Program Studi Informatika**:
    *   Email: `informatika@gmail.com`
    *   Password: `Informatika123`
*   **Program Studi Logistik**:
    *   Email: `logistik@gmail.com`
    *   Password: `Logistik123`

### 6. Hubungkan Storage Link
Aplikasi ini menyimpan file unggahan secara lokal di dalam folder `storage/app`. Agar file tersebut dapat diakses/di-download dengan aman:
```bash
php artisan storage:link
```

### 7. Jalankan Server Pengembangan (Local Server)
Gunakan perintah kustom composer untuk menjalankan server lokal, antrean job, log viewer, dan Vite server secara konkuren:
```bash
npm run dev
# Atau menggunakan script composer gabungan
composer dev
```
Buka peramban (browser) Anda dan akses `http://127.0.0.1:8000`.

---

## 🧪 Pengujian Unit & Fitur (Testing)

Sistem ini dilengkapi dengan pengujian terotomatisasi (*Automated Testing*) untuk memastikan integritas fitur-fitur penting seperti manajemen Prodi, pengunggahan dokumen secara batch, integrasi Google Drive, dan pembatasan hak akses (Authorization).

Jalankan perintah berikut untuk mengeksekusi pengujian:
```bash
php artisan test
```
*   `test_admin_can_create_perti_user`: Menguji kemampuan admin membuat akun perguruan tinggi.
*   `test_perti_can_create_prodi_user`: Menguji perti mendaftarkan prodi baru.
*   `test_perti_can_only_see_their_own_prodis`: Menguji pembatasan hak akses agar perti hanya dapat memantau prodi miliknya.
*   `test_batch_upload_saves_valid_files_and_reports_oversized_ones`: Menguji keandalan batch upload dan validasi ukuran berkas.
*   `test_upload_with_google_drive_links_and_multiple_files_works`: Menguji pengunggahan kombinasi file fisik dan link Google Drive.
*   `test_admin_pdf_route_with_perti_id_downloads_pdf`: Menguji ekspor ringkasan laporan akreditasi ke dalam PDF.
