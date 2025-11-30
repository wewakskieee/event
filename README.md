# Sistem Event & Ticketing (Laravel + Advanced SQL)

Aplikasi manajemen event dan penjualan tiket berbasis web yang dibangun dengan Laravel dan PHP 8.3. Fokus utama proyek ini adalah implementasi logika bisnis yang berat di sisi database menggunakan Stored Procedures, Triggers, dan Views.

## 🚀 Fitur Utama
- **Manajemen Event:** CRUD Event, Banner Slider, Kuota, dan Harga.
- **Transaksi Tiket:** Checkout multi-step, validasi kuota real-time, dan invoice.
- **Tiket Digital:** Generasi QR Code unik (Hash) dan validasi scan (Check-in).
- **Advanced SQL:** Logika transaksi aman menggunakan Database Locking & Stored Procedures.
- **Laporan & Statistik:** Dashboard admin dengan fungsi agregasi SQL.

## 🛠 Teknologi
- **Backend:** Laravel 12.x, PHP 8.3
- **Database:** MySQL 8 (Procedure, Trigger, View, Function)
- **Frontend:** Blade, Tailwind CSS, Alpine.js
- **Tools:** HTML5-QRCode scanner

## 💾 Desain Basis Data (Advanced)
Proyek ini memindahkan sebagian logika bisnis ke database untuk performa dan integritas data:

### 1. Stored Procedure: `SP_CreateTransaction`
Menangani pembelian tiket dengan mekanisme **Atomic Transaction**:
- Melakukan lock row pada tabel event.
- Pengecekan kuota (`quota_remaining`).
- Jika aman, insert ke tabel `transactions`, `transaction_items`, dan generate `tickets`.
- Update kuota.
- Rollback otomatis jika ada error/kuota habis.

### 2. Triggers
- `after_event_insert/update`: Mencatat log perubahan data event ke tabel `logs_event` (Audit Trail).
- `after_transaction_canceled`: Mengembalikan kuota tiket secara otomatis ke tabel event saat status transaksi diubah menjadi 'canceled'.

### 3. Stored Function
- `f_total_ticket_sold(event_id)`: Menghitung total tiket terjual (status paid) untuk efisiensi query dashboard.

## 📸 Alur Sistem
1. **Pembelian:** User memilih event -> Input kuota -> Checkout (SP dipanggil) -> Pending.
2. **Pembayaran:** Admin memverifikasi pembayaran -> Status Paid -> QR Code muncul di sisi User.
3. **Validasi (On Site):** Petugas membuka `/ticket/scan` -> Scan QR User -> Validasi Hash -> Mark as Used.

## 📦 Instalasi

1. **Clone Repository**
   ```bash
   git clone [https://github.com/username/event-ticketing.git](https://github.com/username/event-ticketing.git)