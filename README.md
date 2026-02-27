# 📦 Sistem Inventaris Pondok Pesantren

Aplikasi **Sistem Inventaris** berbasis web untuk mengelola barang, peminjaman, dan aset di lingkungan Pondok Pesantren. Dibangun menggunakan **Laravel 11** dengan arsitektur **MVC + Service Layer**, antarmuka modern menggunakan **TailwindCSS** dan **Alpine.js**, serta dukungan penuh **Dark Mode**.

---

## 📋 Daftar Isi

- [Fitur Utama](#-fitur-utama)
- [Arsitektur & Best Practices](#-arsitektur--best-practices)
- [Struktur Proyek](#-struktur-proyek)
- [Tech Stack](#-tech-stack)
- [Persyaratan Sistem](#-persyaratan-sistem)
- [Instalasi](#-instalasi)
- [Konfigurasi](#-konfigurasi)
- [Menjalankan Aplikasi](#-menjalankan-aplikasi)
- [Akun Default](#-akun-default)
- [Peran Pengguna](#-peran-pengguna)
- [Modul & Fitur](#-modul--fitur)
- [API Endpoint](#-api-endpoint)
- [Testing](#-testing)
- [Deployment](#-deployment)

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
| **Dark Mode**               | Tema gelap/terang persisten dengan class-based toggling                |

---

## 🏗 Arsitektur & Best Practices

Proyek ini menerapkan arsitektur **MVC + Service Layer** yang bersih dan terstruktur:

### Alur Request

```
Route → Middleware → Controller → Service → Model → Database
                         ↓                      ↑
                   FormRequest            Eloquent ORM
                   (Validasi)           (Query & Relasi)
                         ↓
                       View
                   (Blade Template)
```

### Prinsip yang Diterapkan

| Prinsip                          | Implementasi                                                                            |
| -------------------------------- | --------------------------------------------------------------------------------------- |
| **Thin Controller**              | Controller hanya mengatur request/response, tidak mengandung business logic             |
| **Service Layer**                | Business logic kompleks dipusatkan di `app/Services/`                                   |
| **Form Request Validation**      | Semua validasi input menggunakan dedicated `FormRequest` class di `app/Http/Requests/`  |
| **Eloquent Relationships**       | Relasi antar model didefinisikan secara eksplisit (`hasMany`, `belongsTo`, `morphMany`) |
| **Eager Loading**                | Penggunaan `with()` untuk menghindari N+1 query problem                                 |
| **Query Scopes**                 | Filter umum didefinisikan sebagai model scope (misal: `Borrow::pending()`)              |
| **Separation of Concerns**       | Setiap layer memiliki tanggung jawab yang jelas dan tidak tumpang tindih                |
| **Dependency Injection**         | Service di-inject melalui constructor controller                                        |
| **Authorization in FormRequest** | Logika otorisasi (ownership, status check) ditangani di `authorize()` FormRequest       |

### Service Layer

| Service              | Tanggung Jawab                                                      |
| -------------------- | ------------------------------------------------------------------- |
| `BorrowService`      | Create, approve, reject, return peminjaman; notifikasi; auto-reject |
| `ItemService`        | Create/update barang multi-unit; add stock; generate kode barang    |
| `StaffReportService` | Kalkulasi statistik dashboard; filter & deskripsi filter laporan    |

### Form Requests (14 class)

| FormRequest                 | Digunakan Oleh                       |
| --------------------------- | ------------------------------------ |
| `StoreBorrowRequest`        | `BorrowController@store`             |
| `StoreItemRequest`          | `ItemController@store`               |
| `UpdateItemRequest`         | `ItemController@update`              |
| `UpdateStockRequest`        | `ItemController@addStock`            |
| `StoreMaintenanceRequest`   | `MaintenanceController@store`        |
| `UpdateMaintenanceRequest`  | `MaintenanceController@update`       |
| `StoreStaffReportRequest`   | `StaffReportController@store`        |
| `UpdateStaffReportRequest`  | `StaffReportController@update`       |
| `StoreStockOpnameRequest`   | `StockOpnameController@store/update` |
| `StoreItemRequestRequest`   | `ItemRequestController@store`        |
| `UpdateItemRequestRequest`  | `ItemRequestController@update`       |
| `StoreItemFeedbackRequest`  | `ItemFeedbackController@store`       |
| `UpdateItemFeedbackRequest` | `ItemFeedbackController@update`      |
| `ProfileUpdateRequest`      | `ProfileController@update`           |

---

## 📁 Struktur Proyek

```
inventaris/
├── app/
│   ├── Exceptions/              # Custom exception handlers
│   ├── Exports/                 # Export classes (PDF, Excel)
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/           # Admin-only controllers (UserManagement, BorrowApproval)
│   │   │   ├── Api/             # API controllers (Items, Categories, Borrows, Attachments)
│   │   │   ├── Auth/            # Authentication controllers (Laravel Breeze)
│   │   │   ├── BorrowController.php
│   │   │   ├── ItemController.php
│   │   │   ├── StaffReportController.php
│   │   │   ├── DashboardController.php
│   │   │   └── ...              # 18 controllers total
│   │   ├── Middleware/
│   │   │   ├── CheckRole.php         # Role-based access control
│   │   │   ├── CheckUserStatus.php   # Block inactive users
│   │   │   └── LogActivity.php       # Auto-log request activity
│   │   └── Requests/            # 14 FormRequest validation classes
│   ├── Models/                  # 17 Eloquent models
│   │   ├── User.php             # User model (HasRoles trait, relasi lengkap)
│   │   ├── Item.php             # Barang (status constants, updateCondition, scopes)
│   │   ├── Borrow.php           # Peminjaman (scopes: pending, approved, borrowed, etc.)
│   │   └── ...
│   ├── Services/                # Business logic layer
│   │   ├── BorrowService.php    # Logic peminjaman (approve, reject, notifikasi)
│   │   ├── ItemService.php      # Logic barang (multi-unit, generate code, stock)
│   │   └── StaffReportService.php # Logic laporan (stats, filter, export)
│   ├── Traits/
│   │   └── HasRoles.php         # Role checking trait
│   └── View/                    # Blade view components
├── database/
│   ├── factories/               # 8 model factories
│   ├── migrations/              # 35 migration files
│   └── seeders/                 # 12 seeder classes
├── resources/
│   └── views/                   # 95+ Blade templates (dark mode ready)
│       ├── admin/               # Admin-specific views (user management, borrow approvals)
│       ├── auth/                # Authentication views (login, register, etc.)
│       ├── borrows/             # Peminjaman views (index, create, show)
│       ├── dashboard/           # Role-based dashboards (admin, staff, user)
│       ├── items/               # Manajemen barang (index, create, edit, show, add-stock)
│       ├── layouts/             # Layout templates (app, navigation, guest)
│       ├── maintenances/        # Maintenance views
│       ├── staff-reports/       # Laporan petugas (index, create, show, dashboard, export, bulk)
│       ├── stock-opnames/       # Stock opname views
│       └── ...                  # feedbacks, item-requests, notifications, reports, etc.
├── routes/
│   ├── web.php                  # Main web routes + route file includes
│   ├── web/
│   │   ├── staff.php            # Routes untuk petugas & admin (items, borrows, maintenances)
│   │   └── admin.php            # Routes khusus admin (users, categories, locations)
│   ├── api.php                  # REST API routes (Sanctum authenticated)
│   └── auth.php                 # Authentication routes (Laravel Breeze)
├── tailwind.config.js           # TailwindCSS config (darkMode: 'class')
├── vite.config.js               # Vite build configuration
└── vercel.json                  # Vercel deployment config
```

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
| **Charts**         | Chart.js (tema otomatis dark/light)       |
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
- Penambahan stok multi-unit (auto-generate kode unit)

### 2. Peminjaman (`/borrows`)

- Alur peminjaman: **Ajukan** → **Pending Approval** → **Disetujui/Ditolak** → **Dipinjam** → **Dikembalikan**
- Pencatatan kondisi barang saat pinjam dan saat kembali
- Auto-reject pengajuan lain saat satu peminjaman disetujui
- Perpanjangan peminjaman (`/extensions`)
- Tracking tanggal pinjam, jatuh tempo, dan pengembalian

### 3. Stock Opname (`/stock-opnames`)

- Buat sesi stock opname baru
- Mulai, periksa item satu per satu, selesaikan
- Pencocokan stok sistem dengan stok fisik

### 4. Maintenance (`/maintenances`)

- Pencatatan jadwal perbaikan barang
- Update kondisi barang otomatis setelah perbaikan selesai
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
- Dashboard statistik laporan

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

## 🛡 Keamanan

| Fitur                 | Implementasi                                                |
| --------------------- | ----------------------------------------------------------- |
| **Authentication**    | Laravel Breeze (session-based) + Sanctum (API token)        |
| **Role-Based Access** | Custom `CheckRole` middleware (`admin`, `petugas`, `user`)  |
| **User Status Check** | `CheckUserStatus` middleware (blokir user nonaktif)         |
| **Form Validation**   | Dedicated `FormRequest` classes dengan `authorize()` method |
| **CSRF Protection**   | Laravel built-in CSRF token                                 |
| **Soft Deletes**      | Item dan Borrow menggunakan `SoftDeletes` trait             |
| **Activity Logging**  | Semua aksi penting dicatat di `activity_logs` table         |

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
