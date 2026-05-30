# Product Requirement Document (PRD)
## Proyek: KDKMP Digital Platform (Koperasi Desa Merah Putih)

### 1. Latar Belakang & Analisis Masalah
Koperasi Desa/Kelurahan Merah Putih (KDKMP) adalah pilar ekonomi kerakyatan modern yang dicanangkan pemerintah. Masalah utama koperasi konvensional adalah ketidakterbukaan, manajemen stok retail yang berantakan, serta rantai distribusi pertanian yang terlalu panjang. 
Dengan mengadopsi model bisnis ekosistem aplikasi retail raksasa (Alfagift & Indomaret), KDKMP Digital memodernisasi toko sembako desa, menyediakan fitur belanja online bagi warga lokal, sekaligus mengintegrasikan pelaporan keuangan simpan pinjam dan penyerapan hasil bumi secara transparan.

### 2. Profil Pengguna (User Personas)
1. **Warga / Anggota Koperasi:** Berbelanja sembako murah, memantau iuran (simpanan pokok/wajib), dan menjual hasil panen ke koperasi.
2. **Petugas Kasir & Gudang KDKMP:** Mengelola stok fisik gerai, memproses transaksi kasir (*Point of Sales*), dan menerima komoditas tani.
3. **Pengurus & Pengawas Koperasi:** Memantau laporan keuangan bulanan, pembagian SHU (Sisa Hasil Usaha), dan persetujuan pinjaman anggota.

### 3. Ruang Lingkup Fitur (Feature Scope)

#### Modul A: Gerai Merah Putih (Retail Sembako - Terinspirasi Alfagift / Indomaret)
- **Katalog Produk:** Manajemen kategori pangan, sembako, dan kebutuhan pokok dengan harga khusus anggota vs. non-anggota.
- **Sistem Pembelanjaan Online (E-Commerce):** Keranjang belanja, *checkout*, pilihan kurir desa (pengantaran ke rumah warga), atau ambil di gerai KDKMP (*store pick-up*).
- **Manajemen Poin & Loyalitas:** Setiap pembelanjaan menghasilkan poin yang bisa ditukarkan dengan potongan iuran wajib atau kupon sembako murah.

#### Modul B: Agro-Supply Chain & Penyerapan Lokal
- **Portal Petani/Produsen Lokal:** Petani desa dapat mendaftarkan hasil panen (cabai, bawang, padi) untuk diserap oleh gudang koperasi.
- **Manajemen Inventaris & Pergudangan:** Fitur pencatatan stok masuk, stok keluar, dan otomatisasi peringatan jika stok menipis (*low-stock alert*).

#### Modul C: Keuangan & Simpan Pinjam Terpadu
- **Pencatatan Iuran Otomatis:** Integrasi saldo simpanan pokok, simpanan wajib, dan simpanan sukarela anggota.
- **Pengajuan Pinjaman Mikro:** Form pengajuan modal usaha bagi UMKM desa dengan skema verifikasi berjenjang oleh pengurus.

### 4. Kebutuhan Non-Fungsional (Non-Functional Requirements)
- **Keamanan:** Autentikasi berbasis Role-Based Access Control (RBAC). Data finansial sensitif harus dienkripsi.
- **Skalabilitas:** Database harus mampu menampung pertumbuhan data transaksi harian hingga puluhan ribu rekaman per desa tanpa degradasi performa.
- **Aksesibilitas:** Antarmuka harus ringan dan *mobile-friendly*, mengingat mayoritas diakses menggunakan ponsel pintar berspesifikasi menengah ke bawah di area perdesaan.