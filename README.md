# 📦 Sistem Inventaris Pondok Pesantren

Aplikasi **Sistem Inventaris** berbasis web untuk mengelola barang, peminjaman, dan aset di lingkungan Pondok Pesantren. Dibangun menggunakan **Laravel 11** dengan antarmuka modern menggunakan **TailwindCSS** dan **Alpine.js**.

---

## 📋 Daftar Isi

- [Fitur Utama](#-fitur-utama)
- [Tech Stack](#-tech-stack)
- [Persyaratan Sistem](#-persyaratan-sistem)
- [Instalasi](#-instalasi)
- [Konfigurasi](#-konfigurasi)
- [Menjalankan Aplikasi](#-menjalankan-aplikasi)
- [Akun Default](#-akun-default)
- [Peran Pengguna](#-peran-pengguna)
- [Modul & Fitur](#-modul--fitur)
- [API Endpoint](#-api-endpoint)
- [Struktur Proyek](#-struktur-proyek)

---

## ✨ Fitur Utama

| Modul                       | Deskripsi                                                              |
| --------------------------- | ---------------------------------------------------------------------- |
| **Manajemen Barang**        | CRUD barang, kategori, lokasi, QR Code, gambar, pencarian & filter     |
| **Peminjaman**              | Ajukan peminjaman, approval workflow, pencatatan kondisi, pengembalian |
| **Perpanjangan Peminjaman** | Request perpanjangan durasi pinjam dengan persetujuan petugas          |
| **Stock Opname**            | Audit fisik inventaris secara berkala dengan pencocokan stok           |
| **Maintenance / Perbaikan** | Pencatatan jadwal pemeliharaan dan perbaikan barang                    |
| **Permintaan Barang Baru**  | Santri dapat mengajukan permintaan pengadaan barang baru               |
| **Feedback / Ulasan**       | Feedback terhadap barang setelah peminjaman dikembalikan               |
| **Bookmark**                | Tandai barang favorit untuk akses cepat                                |
| **Laporan Petugas**         | Petugas membuat laporan harian; admin me-review                        |
| **Notifikasi**              | Sistem notifikasi untuk aktivitas penting                              |
| **Log Aktivitas**           | Audit trail seluruh aktivitas pengguna                                 |
| **Manajemen Pengguna**      | Kelola akun, role, status aktif/nonaktif, reset password               |
| **Konfigurasi Sistem**      | Pengaturan sistem yang dapat disesuaikan oleh admin                    |
| **Dashboard**               | Dashboard dinamis sesuai peran (admin/petugas/user) dengan grafik      |
| **Ekspor Data**             | Ekspor laporan ke PDF & Excel                                          |

---

## 🛠 Tech Stack

| Komponen           | Teknologi                                 |
| ------------------ | ----------------------------------------- |
| **Backend**        | PHP 8.2+, Laravel 11                      |
| **Frontend**       | Blade Templates, TailwindCSS 4, Alpine.js |
| **Build Tool**     | Vite                                      |
| **Database**       | SQLite (default) / MySQL / PostgreSQL     |
| **Authentication** | Laravel Breeze, Laravel Sanctum (API)     |
| **PDF Export**     | barryvdh/laravel-dompdf                   |
| **Excel Export**   | maatwebsite/excel                         |
| **QR Code**        | simplesoftwareio/simple-qrcode            |
| **Testing**        | Pest PHP                                  |

---

## 💻 Persyaratan Sistem

- **PHP** ≥ 8.2
- **Composer** ≥ 2.x
- **Node.js** 18.x
- **NPM** ≥ 8.x
- **Database**: SQLite (default), MySQL 8+, atau PostgreSQL 14+

---

## 🚀 Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/rizkiansyahalfin/inventaris.git
cd inventaris
```

### 2. Install Dependensi PHP

```bash
composer install
```

### 3. Install Dependensi Node.js

```bash
npm install
```

### 4. Setup Environment

```bash
cp .env.example .env
php artisan key:generate
```

### 5. Setup Database

**SQLite (default, tanpa konfigurasi tambahan):**

```bash
touch database/database.sqlite
php artisan migrate
```

**MySQL / PostgreSQL:**

Edit file `.env` dan sesuaikan konfigurasi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=inventaris
DB_USERNAME=root
DB_PASSWORD=
```

Lalu jalankan migrasi:

```bash
php artisan migrate
```

### 6. Seed Data Awal

```bash
php artisan db:seed
```

Ini akan membuat data contoh termasuk user, kategori, barang, peminjaman, dan lainnya.

---

## ⚙ Konfigurasi

File `.env` berisi konfigurasi utama:

| Variabel           | Deskripsi      | Default  |
| ------------------ | -------------- | -------- |
| `APP_NAME`         | Nama aplikasi  | Laravel  |
| `DB_CONNECTION`    | Jenis database | sqlite   |
| `SESSION_DRIVER`   | Driver sesi    | database |
| `QUEUE_CONNECTION` | Driver antrian | database |
| `CACHE_STORE`      | Driver cache   | database |

---

## ▶ Menjalankan Aplikasi

### Mode Development (Disarankan)

Jalankan semua service sekaligus:

```bash
composer dev
```

Perintah ini akan menjalankan:

- **PHP Server** → `http://localhost:8000`
- **Vite** → Hot reload untuk asset frontend
- **Queue Worker** → Pemrosesan antrian
- **Pail** → Real-time log viewer

### Atau Jalankan Manual

```bash
# Terminal 1 - Laravel Server
php artisan serve

# Terminal 2 - Vite Dev Server
npm run dev
```

Buka browser dan akses: **http://localhost:8000**

---

## 👤 Akun Default

Setelah menjalankan `php artisan db:seed`, akun berikut tersedia:

| Peran             | Email                       | Password   |
| ----------------- | --------------------------- | ---------- |
| **Admin**         | `admin@pondok.com`          | `password` |
| **Admin**         | `rizki@pondok.com`          | `password` |
| **Petugas**       | `soleh@pondok.com`          | `password` |
| **Petugas**       | `hadi@pondok.com`           | `password` |
| **Petugas**       | `siti@pondok.com`           | `password` |
| **User (Santri)** | `ahmad.fadillah@pondok.com` | `password` |

> ⚠️ **Penting:** Segera ganti password default setelah deploy ke production!

---

## 🎭 Peran Pengguna

### 🔴 Admin (Pengurus Pondok)

- Akses penuh ke seluruh fitur
- Dashboard dengan statistik lengkap (total barang, peminjaman, pengguna, grafik)
- Manajemen user (CRUD, ubah role, reset password, aktif/nonaktif)
- Manajemen kategori dan lokasi
- Approval peminjaman (termasuk bulk approve/reject)
- Review laporan petugas
- Ekspor laporan ke PDF & Excel
- Log aktivitas & konfigurasi sistem

### 🟡 Petugas (Staff Pondok)

- Manajemen barang (CRUD, tambah stok)
- Kelola peminjaman (approve/reject, update status, pengembalian)
- Kelola perpanjangan peminjaman
- Pencatatan maintenance / perbaikan
- Stock opname
- Kelola permintaan barang
- Buat laporan petugas harian
- Lihat laporan dasar

### 🟢 User (Santri)

- Dashboard pribadi (peminjaman aktif, riwayat, notifikasi)
- Lihat daftar barang & detail
- Ajukan peminjaman barang
- Ajukan permintaan barang baru
- Berikan feedback setelah pengembalian
- Bookmark barang favorit
- Kelola notifikasi

---

## 📦 Modul & Fitur

### 1. Manajemen Barang (`/items`)

- Daftar barang dengan pencarian, filter, dan paginasi
- Detail barang lengkap: nama, kode, QR Code, gambar, stok, kondisi, status
- Status barang: **Tersedia**, **Dipinjam**, **Dalam Perbaikan**, **Rusak**, **Hilang**
- Tracking harga beli, tanggal beli, garansi, supplier
- Relasi ke kategori dan lokasi
- Lampiran file (attachments)
- Penambahan stok (add stock)

### 2. Peminjaman (`/borrows`)

- Alur peminjaman: **Ajukan** → **Pending Approval** → **Disetujui/Ditolak** → **Dipinjam** → **Dikembalikan**
- Pencatatan kondisi barang saat pinjam dan saat kembali
- Perpanjangan peminjaman (`/extensions`)
- Tracking tanggal pinjam, jatuh tempo, dan pengembalian

### 3. Stock Opname (`/stock-opnames`)

- Buat sesi stock opname baru
- Mulai, periksa item satu per satu, selesaikan
- Pencocokan stok sistem dengan stok fisik

### 4. Maintenance (`/maintenances`)

- Pencatatan jadwal perbaikan barang
- Ekspor ke PDF

### 5. Permintaan Barang (`/item-requests`)

- User mengajukan permintaan barang baru
- Admin/petugas menyetujui atau menolak

### 6. Feedback (`/feedbacks`)

- User memberikan ulasan setelah mengembalikan barang pinjaman

### 7. Laporan Petugas (`/staff-reports`)

- Petugas membuat laporan harian
- Admin me-review, approve, atau reject
- Ekspor ke PDF & Excel
- Bulk actions untuk proses massal

### 8. Dashboard (`/dashboard`)

- **Admin**: Total barang, peminjaman, pengguna, pending approval, grafik bulanan
- **Petugas**: Peminjaman hari ini, pengembalian hari ini, overdue, stok rendah
- **User**: Peminjaman aktif, riwayat, notifikasi terbaru

### 9. Laporan & Ekspor (`/reports`)

- Laporan komprehensif
- Ekspor ke PDF & Excel

---

## 🔌 API Endpoint

API tersedia melalui `/api/` dengan autentikasi **Laravel Sanctum** (Bearer Token).

| Method   | Endpoint                          | Deskripsi                  |
| -------- | --------------------------------- | -------------------------- |
| `GET`    | `/api/categories`                 | Daftar kategori            |
| `POST`   | `/api/categories`                 | Buat kategori baru         |
| `GET`    | `/api/items`                      | Daftar barang              |
| `POST`   | `/api/items`                      | Buat barang baru           |
| `POST`   | `/api/items/{id}/categories/{id}` | Tambah kategori ke barang  |
| `DELETE` | `/api/items/{id}/categories/{id}` | Hapus kategori dari barang |
| `POST`   | `/api/attachments`                | Upload lampiran            |
| `DELETE` | `/api/attachments/{id}`           | Hapus lampiran             |
| `GET`    | `/api/borrows`                    | Daftar peminjaman          |
| `POST`   | `/api/borrows`                    | Buat peminjaman baru       |
| `PATCH`  | `/api/borrows/{id}/return`        | Kembalikan barang          |

---

## 📁 Struktur Proyek

```
inventaris/
├── app/
│   ├── Exports/             # Export classes (PDF, Excel)
│   ├── Http/
│   │   ├── Controllers/     # 33 controllers (Web + API + Admin)
│   │   ├── Middleware/       # CheckRole, CheckUserStatus, LogActivity
│   │   └── Requests/        # Form request validation
│   ├── Models/              # 17 Eloquent models
│   ├── Traits/              # HasRoles trait
│   └── View/                # View components
├── database/
│   ├── factories/           # 8 model factories
│   ├── migrations/          # 35 migration files
│   └── seeders/             # 12 seeder classes
├── resources/
│   └── views/               # 92 Blade templates
│       ├── admin/           # Admin-specific views
│       ├── auth/            # Authentication views
│       ├── borrows/         # Peminjaman views
│       ├── dashboard/       # Dashboard (admin/staff/user)
│       ├── items/           # Manajemen barang views
│       ├── layouts/         # Layout templates
│       ├── maintenances/    # Maintenance views
│       ├── staff-reports/   # Laporan petugas views
│       ├── stock-opnames/   # Stock opname views
│       └── ...              # Dan lainnya
├── routes/
│   ├── web.php              # Web routes (224 baris)
│   ├── api.php              # API routes (Sanctum)
│   └── auth.php             # Authentication routes
├── tailwind.config.js       # TailwindCSS configuration
├── vite.config.js           # Vite build configuration
└── vercel.json              # Vercel deployment config
```

---

## 🧪 Testing

Proyek ini menggunakan **Pest PHP** untuk testing:

```bash
# Jalankan semua test
php artisan test

# Atau menggunakan Pest langsung
./vendor/bin/pest
```

---

## 🚢 Deployment

### Vercel

Proyek sudah dikonfigurasi untuk deployment di Vercel (`vercel.json`):

```bash
vercel --prod
```

### Production Build

```bash
npm run build
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 📄 Lisensi

Proyek ini dilisensikan di bawah [MIT License](https://opensource.org/licenses/MIT).
