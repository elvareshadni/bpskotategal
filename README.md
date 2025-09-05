# 📊 Dashboard Data Indikator Strategis  
**Badan Pusat Statistik Kota Tegal**

Website aplikasi untuk menampilkan **Data Indikator Strategis Kota Tegal** dalam bentuk dashboard interaktif.  
Dibangun dengan **CodeIgniter 4**, **Bootstrap 5**, dan **Chart.js**.

---

## ✨ Fitur Utama

### 🔑 Autentikasi
- **Login / Register Akun**
- **Lupa Password** (reset via email)
- **Manajemen Profil Akun**: ubah email, username, fullname, no. HP, foto profil, dan password

### 👤 User
- Dashboard menampilkan **visualisasi data indikator strategis** Kota Tegal (diagram interaktif dengan Chart.js)
- Menampilkan **infografis** yang tersimpan di database

### 🛠️ Admin
- **Laporan kunjungan user** (login/logout + durasi waktu aktif)
- **Kelola Data Indikator** melalui spreadsheet link yang disediakan
- **Kelola Infografis** untuk ditampilkan ke dashboard
- **Kelola carousel banner**

---

## 🏗️ Arsitektur Sistem

- **Framework**: [CodeIgniter 4](https://codeigniter.com/)  
- **UI Framework**: Bootstrap 5  
- **Chart**: Chart.js  
- **Database**: MySQL 8+  
- **Environment**: XAMPP / Laragon / Apache + MySQL  

Struktur database utama:
- `users` → data akun (role: admin/user)
- `laporan_kunjungan` → log kunjungan user
- `infografis` → data infografis
- `carousel` → data carousel/banner
- `password_resets` → token reset password:contentReference[oaicite:0]{index=0}

---

## ⚙️ Setup Program (Localhost)

### 1. Clone repository
```bash
git clone https://github.com/username/repo-bpstegal.git
cd repo-bpstegal
composer install
```

### 2. Konfigurasi .env

- Salin file `env` → ubah nama jadi `.env`
- Sesuaikan konfigurasi database, CSV_URL, dan email system.

Contoh isi `.env`:

```dotenv
app.baseURL = 'http://localhost:8080/'

database.default.hostname = localhost
database.default.database = bpstegal
database.default.username = root
database.default.password = ''
database.default.DBDriver = MySQLi

CSV_URL = 'https://script.google.com/macros/s/AKfycbwxxxxxxx/exec'

SMTP_HOST = smtp.gmail.com
SMTP_USER = your_email@gmail.com
SMTP_PASS = your_app_password
SMTP_PORT = 587
SMTP_CRYPTO = tls
```

---

## 🗄️ Setup Database

### 1. Buat database MySQL:
```SQL
CREATE DATABASE bpstegal;
```
(atau gunakan nama lain, sesuaikan di `.env`)

### 2. Jalankan migrasi jika data di database masih kosong:
```bash
php spark migrate -n App
```
### 3. Jalankan seeder (opsional untuk data dummy):
php spark db:seed DatabaseSeeder

### Opsional:
Import struktur tabel dari file `bpstegal.sql` (tersedia di repo) jika migrasi dan/atau seeder bermasalah

## 📑 Integrasi Google Spreadsheet
Aplikasi mengambil data indikator strategis dari **Google Spreadsheet**.

1. Upload Excel indikator strategis ke **Google Spreadsheet**

2. Atur agar **siapa saja dengan link bisa edit**

3. Buka menu **Extensions → Apps Script**

4. Ganti isi `Code.gs` atau `Kode.gs` dengan script berikut:


```javascript
// === KONFIGURASI ===
const SPREADSHEET_ID = '1ohHcbmQnyH5S2SwY1B9SGa_oC64FzloE3L4F8Vk2Ito'; // ganti
const DEFAULT_SHEET  = 'LUAS_KEPENDUDUKAN';

function doGet(e) {
  const sheetName = e?.parameter?.sheet || DEFAULT_SHEET;
  const ss = SpreadsheetApp.openById(SPREADSHEET_ID);
  const sh = ss.getSheetByName(sheetName);
  if (!sh) {
    return ContentService.createTextOutput(`Sheet "${sheetName}" not found`)
      .setMimeType(ContentService.MimeType.TEXT);
  }
  const values = sh.getDataRange().getValues();
  const csv = values.map(r => r.map(v => {
    if (v == null) return '';
    const s = String(v);
    const needQ = /[",\n\r]/.test(s);
    return needQ ? `"${s.replace(/"/g,'""')}"` : s;
  }).join(',')).join('\n');

  return ContentService.createTextOutput(csv).setMimeType(ContentService.MimeType.CSV);
}

```

5. Deploy sebagai Web App:

- Description: `API CSV Dashboard`
- Execute as: *Me*
- Access: *Anyone*

6. Copy URL → paste ke `.env` pada `CSV_URL`

## 📬 Setup Email Reset Password
1. Gunakan akun Gmail nyata yang anda miliki
2. Aktifkan **App Passwords** (di akun dengan 2FA)
3. Masukkan `SMTP_USER` dan `SMTP_PASS` ke `.env`

## 🚀 Jalankan Server Lokal
```bash
php spark serve
```

Buka di browser: ***http://localhost:8080***

---

### 🛠️ Perintah Tambahan Untuk Developer
- #### Buat migration baru:

```bash
php spark make:migration NamaMigration
```

- #### Buat seeder baru:

```bash
php spark make:seeder NamaSeeder
```

---

### 📊 Laporan Kunjungan
Setiap **user** (role = `user`) yang login & logout akan dicatat ke tabel `laporan_kunjungan`.
Fitur **auto-logout** setelah 30 menit idle memastikan data `logout_time` dan `durasi_waktu` tidak pernah kosong.
Admin dapat melihat laporan kunjungan di dashboard.

---

### 📌 Catatan Penting
- Default database name: `bpstegal` (ubah sesuai kebutuhan di `.env`)

- File `.env` WAJIB di-setup (database, CSV_URL, email)

- Data indikator strategis **harus berasal dari Google Spreadsheet** dengan Apps Script di atas

- Website masih dalam tahap **pengembangan & evaluasi** untuk peningkatan performa dan pengalaman pengguna

---

### 🖼️ Tampilan (Screenshots)
- Dashboard User dengan Chart.js

- Halaman Infografis

- Dashboard Admin (Laporan Kunjungan)

- Form Login & Register

***(tambahkan screenshot sesuai kebutuhan di folder /public/screenshots)***

---

## 📄 Lisensi
© 2025 Badan Pusat Statistik Kota Tegal

Aplikasi ini dikembangkan untuk mendukung transparansi data indikator strategis.
Gunakan sesuai kebutuhan internal & edukasi.
