# PRD — Archer Monitor (Monitoring Atlet Panahan)

Oct 11, 2026 · @tyo

## 1. Ringkasan dan latar belakang

Archer Monitor adalah aplikasi web untuk memantau perkembangan atlet panahan klub Perpani Kota Bekasi: dari biodata, antropometri, tes fisik dan teknik, sampai skor bulanan dan catatan latihan harian.

**Masalah saat ini.** Data atlet dikelola di satu file Excel bermakro (ATHTRIX PROFILE) dengan 12 sheet. Akibatnya:

- Data tersebar antar-sheet dan ID atlet tidak konsisten (ATL3 di sheet teknik bukan atlet yang sama dengan ATL3 di database atlet; ada typo seperti ALT2).
- Tanggal tes tidak tersimpan, sehingga perkembangan dari waktu ke waktu tidak bisa dilihat.
- Atlet mencatat latihan di Buku Saku cetak (Tabel Monitor Shoot 100 Arrow), yang tidak bisa direkap pelatih.
- Excel hanya bisa dipakai satu orang pada satu waktu dan tidak punya hak akses per peran.

**Solusi.** Aplikasi web monolit (Laravel, MariaDB) yang menyimpan semua data tersebut dengan riwayat berdasarkan tanggal, menghitung profil capaian terhadap benchmark secara otomatis dengan rumus yang sama seperti di Excel, dan memungkinkan atlet mengisi Buku Saku sendiri.

**Sumber kebutuhan.** File leads (`LEADS_PERPANI_Kota_Bekasi_Analysis.xlsx`), file ATHTRIX PROFILE (`.XLSM`), dan foto Buku Saku Atlet halaman Tabel Monitor Shoot 100 Arrow.

## 2. Tujuan dan metrik keberhasilan

Tujuan utama: pelatih dapat melihat profil dan perkembangan setiap atlet dalam satu layar, tanpa membuka Excel.

1. Memusatkan seluruh data atlet di satu database dengan riwayat per tanggal.
2. Menghitung profil fisik dan teknik otomatis terhadap benchmark (BM), sama dengan hasil Excel.
3. Membuat atlet mencatat latihan harian (Buku Saku) secara digital.
4. Membandingkan dua atlet (Head to Head) untuk seleksi dan evaluasi.

Usulan metrik keberhasilan (target perlu dikonfirmasi klub):

| Metrik | Cara ukur | Usulan target |
| --- | --- | --- |
| Kesesuaian rumus | Selisih % profil aplikasi vs Excel untuk data yang sama | 0 (identik; sudah lolos uji unit pada data atlet pertama) |
| Data tereksport ke aplikasi | Atlet di Excel yang berhasil diimpor | 100% atlet aktif |
| Pengisian Buku Saku | Atlet aktif yang mengisi minimal 1 hari per minggu | 80% setelah 1 bulan |
| Waktu menyiapkan evaluasi | Waktu pelatih membuka profil atlet | Kurang dari 1 menit per atlet |

## 3. Pengguna dan peran

Ada tiga peran. Atlet hanya mengakses datanya sendiri; admin dan pelatih mengakses semua atlet.

| Fitur | Admin | Pelatih | Atlet |
| --- | --- | --- | --- |
| Registrasi mandiri (`/register`) | - | - | Ya |
| Lihat daftar semua atlet | Ya | Ya | Tidak |
| Tambah / ubah atlet | Ya | Ya | Tidak |
| Hapus atlet | Ya | Tidak | Tidak |
| Upload foto atlet | Ya | Ya | Tidak |
| Lihat profil | Semua atlet | Semua atlet | Profil sendiri |
| Input antropometri, tes fisik, teknik, beban, skor, peralatan | Ya | Ya | Tidak |
| Isi Buku Saku (100 arrow) | Ya | Ya | Milik sendiri |
| Head to Head | Ya | Ya | Tidak |
| Kelola RBAC (role, izin route, menu) | Ya | Tidak | Tidak |
| Buat akun admin / pelatih | Via `archery:make-user` | - | - |

Role dan izin dikelola berbasis **database** (tabel `roles`, `route_permissions`, `menus`) yang di-seed dari `config/rbac.php`, dan dapat diubah admin lewat halaman **Pengaturan Akses (RBAC)**. Admin dan pelatih memiliki hak manajemen yang sama, kecuali **menghapus atlet** dan **mengelola RBAC** yang hanya untuk admin.

## 4. Ruang lingkup

Rilis 1 mencakup modul inti monitoring (di bawah). Pembayaran, tugas pelatih, dan notifikasi ditunda ke rilis berikutnya.

| Modul | Rilis 1 | Rilis berikutnya |
| --- | --- | --- |
| Login dan registrasi (reguler, prestasi, terapi ABK) | Ya | - |
| Database atlet dan dashboard profil | Ya | - |
| Antropometri, tes fisik, performa teknik, beban, skor bulanan, peralatan | Ya | - |
| Buku Saku: monitor shoot 100 arrow mingguan | Ya | - |
| Head to Head | Ya | - |
| Impor dari Excel ATHTRIX | Ya | - |
| Upload foto atlet (kompres di browser, format WEBP) | Ya | - |
| RBAC: role & izin berbasis DB + halaman kelola (admin) | Ya | - |
| Pembayaran bulanan, bukti transfer, konfirmasi admin, blokir akses bila lewat batas | - | Ya |
| Task dan Result task (tes yang ditugaskan pelatih) | - | Ya |
| Notifikasi WhatsApp lewat Laravel scheduler | - | Ya |
| Mapping atlet – pelatih | - | Ya |
| Data visual lanjutan (cetak/PDF printout beban) | - | Ya |

**Di luar lingkup:** aplikasi mobile native, integrasi payment gateway (pembayaran memakai transfer manual), dan pencatatan skor per anak panah saat pertandingan.

## 5. Kebutuhan fungsional

| ID | Modul | Kebutuhan | Prioritas |
| --- | --- | --- | --- |
| F-01 | Autentikasi | Login dengan email dan password; sesi dapat diingat; percobaan login dibatasi 10 per menit; proteksi CSRF pada semua form. | Wajib |
| F-02 | Registrasi | Atlet mendaftar mandiri (nama, email, password min. 8 karakter, jenis kelamin, tanggal lahir, asal daerah, divisi, jenis pendaftaran). Role selalu atlet; kode atlet otomatis (ATL{n}). | Wajib |
| F-03 | Database atlet | Staf melihat daftar atlet (cari nama/kode, filter divisi, 25 per halaman) dan mengelola biodata. | Wajib |
| F-04 | Antropometri | Simpan 7 ukuran tubuh + 3 denyut nadi per tanggal; IMT & kategori otomatis; grafik berat badan. | Wajib |
| F-05 | Tes fisik | Simpan Yo-Yo IR1, Standing Stork ka/ki, Grip ka/ki, Bench Pull, Core per tanggal; BM dapat diubah per tes; VO2max otomatis dari tabel referensi. | Wajib |
| F-06 | Performa teknik | Simpan kualifikasi, eliminasi, maximal shooting, bow training, bow draw, battery, speed shooting per tanggal; BM per tes. | Wajib |
| F-07 | Data beban | Simpan beban & repetisi 17 latihan; 1RM otomatis; profil menampilkan 1RM terbaru per latihan. | Wajib |
| F-08 | Skor bulanan | Simpan skor, tempat, analisis per bulan & tahun; dukung skor dua babak (307+304); tabel + grafik garis. | Wajib |
| F-09 | Peralatan | Simpan 15 komponen peralatan per atlet. | Wajib |
| F-10 | Dashboard profil | Satu halaman per atlet: biodata, profil % vs BM per item & grup, radar, riwayat, foto. | Wajib |
| F-11 | Buku Saku | Tabel mingguan Sn–Sb: teknik diperbaiki (a–f), shoot 100 arrow Ya/Tidak, catatan; navigasi minggu; rekap 8 minggu; atlet isi milik sendiri. | Wajib |
| F-12 | Head to Head | Bandingkan dua atlet: radar, antropometri, % per item, skor terbaik, rata-rata skor. | Wajib |
| F-13 | Dashboard staf | Jumlah atlet aktif, rata-rata profil fisik & teknik tim, hari shoot 100 arrow per atlet minggu ini. | Wajib |
| F-14 | Impor Excel | `archery:import-excel` mengimpor atlet & data berseri dari ATHTRIX; cocok berdasarkan nama; aman diulang. | Wajib |
| F-18 | Upload foto atlet | Foto dipilih lalu dikompres di browser (maks sisi 512px) dan dikonversi ke WEBP, disimpan di `public/uploads/athletes` (tanpa storage:link); server memvalidasi format & ukuran; bila kosong tampil avatar anonim. | Wajib |
| F-19 | Pengelolaan RBAC | Admin mengelola role, izin per-route, dan menu dari UI (CRUD). Data di DB, di-seed dari `config/rbac.php`; perubahan langsung berlaku (cache di-flush). | Wajib |
| F-15 | Pembayaran | Biaya bulanan, atlet unggah bukti transfer, admin konfirmasi, notifikasi bila belum bayar, akses diblokir bila lewat batas. | Rilis berikutnya |
| F-16 | Task & Result task | Pelatih menugaskan tes ke atlet; hasil mengalir ke profil & Head to Head. Belum dikonfirmasi penuh. | Rilis berikutnya |
| F-17 | Notifikasi WhatsApp | Pengingat ke nomor WhatsApp, dipicu Laravel scheduler. | Rilis berikutnya |
| F-20 | Mapping atlet–pelatih | Tiap atlet terhubung ke satu pelatih; opsi membatasi pelatih hanya ke atlet binaannya. Belum dikonfirmasi. | Rilis berikutnya |

## 6. Aturan bisnis dan rumus

Semua rumus diturunkan dari sheet Excel ATHTRIX dan dicocokkan dengan nilai di sana.

**Persentase item** dibatasi 100%, kekurangan adalah sisanya:

```latex
\%_{item} = \min\left(1, \frac{hasil}{BM}\right), \qquad kekurangan = 1 - \%_{item}
```

**Profil grup** adalah rata-rata persentase item di dalamnya; **profil keseluruhan** adalah rata-rata persentase grup.

| Profil | Grup | Item penyusun |
| --- | --- | --- |
| Fisik | Endurance | Yo-Yo IR1 (VO2max) |
| Fisik | Balance | Standing Stork kanan, kiri |
| Fisik | Strength | Grip kanan, Grip kiri, Bench Pull, Core Stability |
| Teknik | Scoring | Skor kualifikasi, skor eliminasi |
| Teknik | Specific Strength | Maximal shooting, bow training, bow draw, battery test |
| Teknik | Specific Speed | Speed shooting |

**Benchmark.** Benchmark fisik berbeda per jenis kelamin; benchmark teknik berlaku sama kecuali diubah per tes.

| Item | Laki-laki | Perempuan |
| --- | --- | --- |
| Yo-Yo VO2max (ml/kg/mnt) | 56,56 | 49,84 |
| Standing Stork (detik) | 60 | 50 |
| Grip Strength (kg) | 57,5 | 41,4 |
| Bench Pull (repetisi) | 30 | 20 |
| Core Stability (level) | 12 | 12 |

BM teknik default: kualifikasi 625, eliminasi 135, maximal shooting 350, bow training 75, bow draw 37, battery 6, speed shooting 7. Pada data Excel, BM kualifikasi dan eliminasi bervariasi per atlet (625 sampai 635 dan 135 sampai 145), sehingga BM dapat diubah per tes.

**Rumus lain**

- IMT = berat badan (kg) dibagi kuadrat tinggi badan (m). Kategori: di bawah 18,5 KURUS; 18,5 sampai 25 NORMAL; di atas 25 GEMUK.
- 1RM = beban × (1 + 0,0333 × repetisi), dibulatkan 2 desimal.
- Skor dua babak (mis. 307+304) dijumlahkan menjadi 611; teksnya tetap disimpan.
- Kode atlet berikutnya = ATL + (angka tertinggi saat ini + 1).

**Aturan akses.** Atlet hanya boleh melihat dan mengisi Buku Saku miliknya sendiri; rute pengelolaan data dibatasi untuk admin dan pelatih.

## 7. Model data

Skema MariaDB (utf8mb4) terdiri dari 10 tabel domain, 3 tabel RBAC, dan 3 tabel bawaan Laravel. Seluruh data berseri terikat ke `athletes` dan ikut terhapus bila atlet dihapus. Kolom `users.role` memakai varchar (bukan enum) agar role bisa ditambah dari database.

| Tabel | Isi utama | Catatan |
| --- | --- | --- |
| `users` | nama, email, password, role (kode), phone | `role` varchar merujuk tabel `roles` |
| `athletes` | kode unik, nama, tempat & tanggal lahir, gender (L/P), asal, divisi, jenis pendaftaran, foto, status aktif | `user_id` opsional; `photo` = nama file WEBP |
| `anthropometries` | tanggal ukur, 7 ukuran tubuh, 3 denyut nadi | IMT dihitung saat ditampilkan |
| `physical_tests` | tanggal, tempat, level Yo-Yo, 7 hasil & 7 BM | BM kosong = default |
| `technique_tests` | tanggal, tempat, 7 hasil & 7 BM | Sama dengan fisik |
| `load_tests` | tanggal, latihan, beban, repetisi, 1RM | 1RM otomatis |
| `monthly_scores` | tahun, bulan, skor, catatan skor, tempat, analisis | Boleh > 1 per bulan |
| `equipments` | 15 komponen peralatan | Satu baris per atlet |
| `shoot_logs` | tanggal, teknik diperbaiki (JSON a–f), shoot 100 arrow, catatan | Unik per atlet & tanggal |
| `yoyo_references` | level, VO2max | 91 baris dari sheet referensi Excel |
| `roles` | code, name, is\_staff, sort | Role RBAC; di-seed dari `config/rbac.php` |
| `route_permissions` | pattern (nama route, boleh wildcard), roles (JSON), sort | Izin per-route |
| `menus` | label, route, param, roles (JSON), sort, is\_active | Menu sidebar per role |
| `password_reset_tokens`, `failed_jobs`, `personal_access_tokens` | Tabel bawaan Laravel | Belum dipakai fitur |

Daftar metrik, BM, grup, kode teknik, dan definisi formulir disimpan di berkas konfigurasi `config/archery.php`, sehingga menambah item tes tidak memerlukan perubahan kode controller.

## 8. Kebutuhan non-fungsional dan arsitektur

Arsitektur monolit Laravel 10 dengan antarmuka Blade, berjalan di PHP 8.1 dan MariaDB, dapat di-host di shared hosting (sesuai keputusan awal).

| Aspek | Kebutuhan |
| --- | --- |
| Platform | PHP 8.1, Laravel 10, MariaDB; ekstensi PHP standar Laravel (mbstring, pdo\_mysql, zip, dll.) |
| Antarmuka | Blade + Bootstrap 5 dan Chart.js dari CDN; tanpa npm atau proses build; monolit server-rendered (bukan SPA); responsif untuk ponsel |
| Keamanan | Password di-hash; proteksi CSRF; RBAC per-route via middleware; atlet tidak bisa membuka data atlet lain; role tidak bisa dipilih saat registrasi; throttle login & registrasi |
| RBAC | Role, izin per-route, dan menu berbasis DB (di-seed dari `config/rbac.php`), dibaca dengan cache yang di-flush saat berubah; dikelola admin via UI |
| Foto atlet | Dikompres di sisi klien ke WEBP (maks sisi 512px), disimpan di `public/uploads` tanpa storage:link; server memvalidasi magic bytes WEBP & ukuran (maks 2 MB) |
| Kompatibilitas DB | Panjang string default 191 agar aman di MariaDB/MySQL lama pada shared hosting |
| Akurasi | Rumus profil diuji unit terhadap angka Excel (`tests/Unit/ProfileServiceTest.php`) |
| Pemeliharaan | Metrik & BM dikonfigurasi di satu berkas; impor Excel dapat diulang tanpa duplikasi |
| Privasi | Sebagian atlet masih remaja; data hanya diakses pengguna terautentikasi, perlu persetujuan orang tua/klub sebelum data nyata dipakai di server publik |

API terpisah atau SPA tidak diperlukan pada rilis 1.

## 9. Rilis, risiko, asumsi, dan pertanyaan terbuka

**Rilis.** Kode rilis 1 sudah ditulis dan dikirim, tetapi belum dijalankan: `composer install`, migrasi, dan impor Excel belum diuji karena Packagist tidak terjangkau saat pengembangan. Target awal rilis pertama adalah 2 minggu dengan fitur utama saja; target ini tidak ketat.

| Rilis | Isi | Status |
| --- | --- | --- |
| 1 | Login, registrasi, profil, data tes, Buku Saku, Head to Head, impor Excel, upload foto, RBAC (DB + UI) | Kode ditulis, belum diuji di server |
| 2 | Pembayaran bulanan & pembatasan akses; Task & Result task; mapping atlet–pelatih | Belum dimulai |
| 3 | Notifikasi WhatsApp; printout beban; upload foto oleh atlet sendiri | Belum dimulai |

**Estimasi dari file leads.** Sheet Mandays memperkirakan total 59 mandays dengan tarif Rp1.000.000 per manday, yaitu Rp59.000.000, mencakup semua item di Sheet1 termasuk pembayaran dan registrasi. Item terbesar adalah Test Fisik dan Beban Max (masing-masing 5), Pembayaran (5), dan Data Visual (4).

**Risiko**

| Risiko | Dampak | Mitigasi |
| --- | --- | --- |
| ID atlet di Excel tidak konsisten antar-sheet | Data salah atlet saat impor | Impor mencocokkan berdasarkan nama; periksa peringatan nama yang tidak ditemukan |
| Impor Excel belum diuji | Pemetaan kolom bisa meleset | Kolom sudah dicocokkan dengan header Excel; jalankan pada salinan database dan bandingkan dengan Excel |
| Tanggal tes dan tahun skor tidak ada di Excel | Riwayat awal memakai satu tanggal | Diisi lewat opsi `--test-date` dan `--year`; koreksi manual bila perlu |
| BM teknik bervariasi per atlet di Excel | Persentase bisa beda dari Excel bila BM default dipakai | Impor menyimpan BM per tes dari Excel |
| Data anak di server publik | Risiko privasi | Autentikasi wajib, persetujuan klub atau orang tua, HTTPS |
| Shared hosting | Keterbatasan versi PHP, cron, dan SSH | Pastikan PHP 8.1 dan akses cron sebelum rilis 3 (scheduler notifikasi) |

**Asumsi**

- Satu atlet memiliki satu akun; atlet yang didaftarkan staf belum punya akun sampai dihubungkan.
- Divisi memakai daftar Recurve, Compound, Barebow, Nasional (dari kolom Divisi di Excel ditambah Barebow).
- Task dan Result task diartikan sebagai tes fisik atau teknik yang ditugaskan pelatih; ini belum dikonfirmasi penuh.
- Aplikasi ditujukan untuk satu klub; multi-klub tidak direncanakan.

**Pertanyaan terbuka**

- [ ] Mapping atlet–pelatih: satu atlet tepat satu pelatih, dan apakah pelatih hanya boleh mengakses atlet binaannya?
- [ ] Siapa yang membuat akun atlet bila tidak mendaftar sendiri, dan bagaimana menghubungkannya ke data hasil impor?
- [ ] Berapa besar iuran bulanan dan batas waktu pembayaran sebelum akses diblokir?
- [ ] Apakah atlet perlu melihat Head to Head atau hanya pelatih?
- [ ] Apakah daftar divisi dan benchmark sudah final, dan siapa yang berwenang mengubahnya?
- [ ] Apakah Buku Saku perlu dikunci setelah minggu berlalu?
- [ ] Apakah atlet boleh mengunggah fotonya sendiri dari halaman profil?
