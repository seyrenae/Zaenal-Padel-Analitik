# Padel Live Tracking & Sport Analytics

Aplikasi berbasis web untuk mencatat skor secara langsung (live tracking) pada pertandingan Padel dan menyiarkannya secara real-time untuk kebutuhan Overlay Livestream (seperti OBS Studio) menggunakan teknologi WebSockets.

## 🚀 Teknologi yang Digunakan
- **Backend:** Laravel (PHP)
- **Database:** SQLite
- **Frontend:** HTML, Tailwind CSS, Vanilla JS, Axios
- **WebSockets:** Laravel Reverb & Laravel Echo
- **Asset Bundler:** Vite

## 🛠️ Persiapan Awal (Setup)

Pastikan sistem Anda sudah terinstal **PHP**, **Composer**, dan **Node.js**.

1. **Buka folder proyek ini (`padel-proto`)** di terminal Anda.
2. **Instal dependensi PHP & Node:**
   ```bash
   composer install
   npm install
   ```
3. **Siapkan Environment:**
   Salin file `.env.example` menjadi `.env` (jika belum ada) dan _generate_ kunci aplikasi.
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
4. **Migrasi Database:**
   Jalankan migrasi untuk membuat tabel-tabel (secara default akan menggunakan SQLite).
   ```bash
   php artisan migrate
   ```

## ▶️ Cara Menjalankan Aplikasi Secara Lokal

Aplikasi ini sangat bergantung pada fitur pertukaran data *real-time* (WebSocket). Oleh karena itu, Anda harus menjalankan **4 perintah terminal yang berbeda** secara bersamaan. 

Silakan buka 4 *tab* atau *window* terminal di dalam folder direktori proyek ini, lalu jalankan masing-masing baris berikut di tab yang berbeda:

### Terminal 1: Server Web (Laravel)
```bash
php artisan serve
```

### Terminal 2: WebSocket Server (Laravel Reverb)
```bash
php artisan reverb:start
```
*(Menangani koneksi WebSockets secara langsung ke browser)*

### Terminal 3: Worker Antrean (Queue)
```bash
php artisan queue:work
```
*(Dibutuhkan agar event pengiriman poin (Broadcast) tidak membebani memori dan dapat disiarkan tepat waktu ke OBS)*

### Terminal 4: Kompilasi Aset Frontend (Tailwind/Vite)
```bash
npm run dev
```
*(Berfungsi untuk merender style warna dan CSS secara langsung setiap kali ada perubahan pada file layout)*

---

## 📱 Daftar Rute & Navigasi Halaman

Setelah semua dari 4 layanan di atas berjalan dengan aman tanpa *error*, akses aplikasi melalui browser Anda pada URL: `http://127.0.0.1:8000`.

* **`/` (Halaman Setup / Beranda):** 
  Untuk memulai turnamen baru. Mengatur format permainan (*Best of 3*, *Golden Point*), mendaftarkan nama pemain, dan penentuan siapa yang melakukan *serve* pertama.
* **`/operator/{id}` (Dashboard Operator):** 
  Halaman "Dapur" wasit untuk memonitor pertandingan. Terdiri atas kontrol pencatatan poin (*Two-Step Flow*), tombol Jeda, pindah serve, undo rekaman (*History State*), dll.
* **`/livestream/{id}` (Livestream / OBS Overlay):** 
  URL khusus untuk _Broadcaster_ (Sutradara Siaran). Halaman ini berlatar belakang transparan dan menampilkan skor angka raksasa yang bergerak *real-time* otomatis untuk pertandingan tertentu. Cocok dimasukkan ke dalam **OBS Studio** menggunakan fitur **Browser Source**.
* **`/summary/{id}` (Halaman Statistik/Ringkasan):** 
  Halaman ringkasan *Post-Match* yang menampilkan statistik tingkat lanjut, rekapitulasi data akurat terkait poin *Winners*, *Errors*, dan pemakaian Dinding oleh setiap pemain.
