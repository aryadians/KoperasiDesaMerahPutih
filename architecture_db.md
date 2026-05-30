# Data Architecture & Schema Blueprint (MySQL)

Dokumen ini mendefinisikan relasi tabel-tabel utama untuk mendukung ekosistem retail dan finansial KDKMP.

## 1. Tabel Inti Pengguna (`users` & `members`)

- `users`: id, name, email, password, role (admin, pengurus, kasir, anggota), status, timestamps.
- `members`: id, user_id (FK), nik, nomor_anggota, alamat_desa, tanggal_bergabung, total_poin, status_aktif.

## 2. Tabel Retail & e-Commerce (`products`, `categories`, `orders`)

- `categories`: id, name, slug.
- `products`: id, category_id (FK), name, description, price_member, price_non_member, current_stock, unit (kg, pcs, liter), is_local_product (boolean).
- `orders`: id, user_id (FK), order_number, total_amount, points_earned, payment_status (pending, paid, cancelled), delivery_type (pickup, delivery), timestamps.
- `order_items`: id, order_id (FK), product_id (FK), quantity, price_at_purchase, subtotal.

## 3. Tabel Finansial Koperasi (`savings`, `loans`)

- `member_savings`: id, member_id (FK), type (pokok, wajib, sukarela), amount, transaction_date, notes.
- `loans`: id, member_id (FK), loan_code, amount_requested, amount_approved, interest_rate, tenor_months, status (draft, approved, rejected, active, paid_off).
- `loan_payments`: id, loan_id (FK), amount_paid, penalty, installment_number, payment_date.

## 4. Tabel Penyerapan Komoditas Lokal (`crop_absorptions`)

- `crop_absorptions`: id, member_id (FK_petani), product_name, quantity, price_per_unit, total_payout, status (pending, received, paid), absorption_date.
