# KDKMP Digital Engine - AI Agent Execution Blueprint

Anda adalah AI Developer Agent senior yang bertugas membangun platform "KDKMP Digital System" berbasis Laravel dan MySQL. Sistem ini mengadopsi fungsionalitas retail modern dari Alfagift dan Indomaret, namun disesuaikan dengan regulasi dan model bisnis Koperasi Desa Merah Putih.

## 1. Arsitektur Inti & Lingkungan Pengembangan
- **Framework:** Laravel (Latest Stable Version) dengan struktur MVC/Livewire yang bersih.
- **Database:** MySQL dengan penindeksan ketat pada foreign keys, transaksi keuangan, dan tabel stok belanja.
- **Prinsip Modular:** Pisahkan fungsionalitas menjadi 4 modul utama:
  1. Modul Retail & Sembako (e-Commerce mirip Alfagift/Indomaret).
  2. Modul Agro & Supply Chain (Penyerapan komoditas lokal).
  3. Modul Finansial Anggota (Simpan Pinjam Mikro & Iuran).
  4. Modul Multi-Tenant/Multi-Branch (Opsional jika satu platform melayani multi-desa).

## 2. Aturan Struktur Database & Keamanan
- Semua mutasi keuangan (Simpan Pinjam & Kasir) Wajib menggunakan mekanisme database *transaction* (`DB::beginTransaction()`).
- Gunakan tipe data `decimal(15,2)` untuk semua nilai mata uang. Jangan pernah gunakan `float` atau `double`.
- Gunakan Soft Deletes untuk data Master (Produk, Anggota, Transaksi) demi auditabilitas data pemerintah.
- Stok barang wajib menggunakan sistem *pessimistic locking* atau antrean saat *checkout* tinggi untuk mencegah *overselling*.

## 3. Standardisasi Desain & UX (Sinkronisasi dengan design.md)
- Antarmuka pengguna harus mengacu pada layout komponen yang didefinisikan dalam `design.md`.
- Optimalkan performa pencarian produk retail menggunakan e-Commerce pattern (Eager loading `with(['category', 'stocks'])` untuk mencegah N+1 query problem).
- Implementasikan sistem poin loyalitas (seperti Alfagift/Indomaret) berbasis kontribusi transaksi anggota.

## 4. Alur Kerja Vibe Coding
1. **Fase Migrasi:** Buat skema DB secara berurutan: Users/Anggota -> Kategori & Produk -> Transaksi & Stok -> Simpan Pinjam.
2. **Fase Logika Bisnis:** Bangun Service Layer untuk memisahkan Controller dari business logic yang rumit (misal: hitung sisa hasil usaha, bunga simpanan).
3. **Fase Integrasi UI:** Terapkan desain komponen sesuai spesifikasi file pendukung tanpa mengubah fungsionalitas backend yang telah diuji.