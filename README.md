# KDKMP Digital Engine (Koperasi Desa Merah Putih)

Sistem Informasi Koperasi Desa Merah Putih berbasis Laravel yang mengintegrasikan Modul Retail, Finansial Anggota, dan Manajemen Komoditas Agro.

## 🚀 Fitur Utama
- **Modul Retail (POS & Gerai)**: Antarmuka kasir modern dengan integrasi *barcode scanner*, *thermal printer receipt*, dan *checkout* cepat.
- **Finansial Anggota**: Sistem simpanan (pokok, wajib, sukarela), pengajuan pinjaman mikro, dan pembagian Sisa Hasil Usaha (SHU) otomatis.
- **Manajemen Komoditas Agro**: Platform penyerapan hasil tani warga dengan transparansi harga.
- **UI Premium**: Menggunakan sistem desain *Merah Putih* dengan animasi *scroll-reveal*, *3D tilt*, dan navigasi seluler menyerupai aplikasi *native*.

## 🛠️ Instalasi & Pengembangan

1. **Clone Repositori**:
   ```bash
   git clone <url-repo>
   cd KoperasiDesaMerahPutih
   ```

2. **Setup Lingkungan**:
   ```bash
   cp .env.example .env
   composer install
   php artisan key:generate
   ```

3. **Database**:
   ```bash
   php artisan migrate --seed
   ```

4. **Jalankan Aplikasi**:
   ```bash
   php artisan serve
   ```

## 🧪 Pengujian
Menjalankan unit test untuk memastikan logika bisnis finansial (terutama perhitungan SHU) berjalan akurat:
```bash
php artisan test --testsuite=Unit
```

## 🔒 Keamanan & Kontribusi
Proyek ini mengadopsi standar pengembangan aman. Pastikan untuk selalu menjalankan `php artisan optimize` sebelum *deployment*. Untuk kontribusi, silakan ajukan *Pull Request* dan pastikan semua pengujian lulus.

---
© 2026 Koperasi Desa Merah Putih.
