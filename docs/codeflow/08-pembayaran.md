# 8 · Pembayaran

Iuran bulanan. Atlet unggah bukti periode berjalan; admin verifikasi/tolak. Akses atlet
dikunci sampai periode berjalan **verified**.

Controller: `app/Http/Controllers/PaymentController.php`
Middleware gate: `app/Http/Middleware/EnsurePaymentVerified.php` (alias `payment`)
Model: `app/Models/Payment.php`
View: `resources/views/payment/{my,index}.blade.php`

| Route | Name | Method | Akses RBAC |
|-------|------|--------|------------|
| `GET /pembayaran/saya` | `payment.my` | `my` | athlete |
| `POST /pembayaran/saya` | `payment.upload` | `upload` | athlete |
| `GET /pembayaran/{payment}/bukti` | `payment.proof` | `proof` | `*` (pemilik/staf; dicek controller) |
| `GET /pembayaran` | `payment.index` | `index` | admin |
| `PUT /pembayaran/{payment}/verifikasi` | `payment.verify` | `verify` | admin |
| `PUT /pembayaran/{payment}/tolak` | `payment.reject` | `reject` | admin |

## Gate akses (middleware `payment`)
`EnsurePaymentVerified`: untuk user `role = athlete`, bila **tidak ada** `Payment` periode
(bulan+tahun) berjalan berstatus `verified` → redirect `payment.my` dengan pesan. Route
yang tetap boleh: `payment.my`, `payment.upload`, `payment.proof`, `logout`. Staf tidak dikunci.

## Sisi Atlet
- **`my()`**: siapkan `Payment` periode berjalan (`firstOrNew`) + riwayat. View `payment/my.blade.php` (form unggah + nominal berformat + modal lihat bukti).
- **`upload()`**: validasi bukti data-URL `webp` (magic bytes `RIFF`/`WEBP`, maks 3 MB) → simpan ke `Storage::disk('payments')` (privat) → status `pending` (reset verifikasi).

## Sisi Admin
- **`index()`**: daftar `Payment` + filter `status` (pending/verified/rejected) + hitung jumlah per status. View `payment/index.blade.php`.
- **`verify()`**: status `verified` + `verified_by`/`verified_at`.
- **`reject()`**: status `rejected` + catatan.
- **`proof()`**: stream file bukti privat (pemilik atau staf).

| Model | `App\Models\Payment` (unik per `athlete_id+year+month`; `period_label`) |
|-------|------|
| Disk | `config/filesystems.php` → disk `payments` (privat, `storage/app/payments`) |
| Config | `config/archery.php` → `monthly_fee` (nominal iuran yang ditampilkan) |
| Migrasi | `database/migrations/2026_10_23_000001_create_payments_table.php` |
