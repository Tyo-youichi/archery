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
| Tambah / ubah / hapus atlet | Ya | Ya | Tidak |
| Lihat profil | Semua atlet | Semua atlet | Profil sendiri |
| Input antropometri, tes fisik, teknik, beban, skor, peralatan | Ya | Ya | Tidak |
| Isi Buku Saku (100 arrow) | Ya | Ya | Milik sendiri |
| Head to Head | Ya | Ya | Tidak |
| Buat akun admin / pelatih | Via perintah `archery:make-user` | - | - |

Peran admin dan pelatih saat ini memiliki hak yang sama; pembedaan (mis. hanya admin boleh menghapus atlet) belum diputuskan.

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
| Pembayaran bulanan, bukti transfer, konfirmasi admin, blokir akses bila lewat batas | - | Ya |
| Task dan Result task (tes yang ditugaskan pelatih) | - | Ya |
| Notifikasi WhatsApp lewat Laravel scheduler | - | Ya |
| Upload foto atlet | - | Ya |
| Data visual lanjutan (cetak/PDF printout beban) | - | Ya |

**Di luar lingkup:** aplikasi mobile native, integrasi payment gateway (pembayaran memakai transfer manual), dan pencatatan skor per anak panah saat pertandingan.

## 5. Kebutuhan fungsional

| ID | Modul | Kebutuhan | Prioritas |
| --- | --- | --- | --- |
| F-01 | Autentikasi | Pengguna login dengan email dan password; sesi dapat diingat; percobaan login dibatasi 10 per menit. | Wajib |
| F-02 | Registrasi | Atlet mendaftar mandiri dengan nama, email, password (min. 8 karakter), jenis kelamin, tanggal lahir, asal daerah, divisi, dan jenis pendaftaran (reguler, prestasi, terapi ABK). Role selalu atlet; kode atlet dibuat otomatis (ATL{n}). | Wajib |
| F-03 | Database atlet | Staf melihat daftar atlet (cari nama atau kode, filter divisi, 25 per halaman) dan mengelola biodata. | Wajib |
| F-04 | Antropometri | Simpan tinggi badan, tinggi duduk, berat badan, panjang tungkai, panjang lengan, rentang lengan, lebar bahu, dan denyut nadi (istirahat, maksimal, latihan) per tanggal. IMT dan kategorinya dihitung otomatis; grafik berat badan ditampilkan. | Wajib |
| F-05 | Tes fisik | Simpan Yo-Yo IR1 (level dan VO2max), Standing Stork kanan dan kiri, Grip Strength kanan dan kiri, Bench Pull, dan Core Stability per tanggal, dengan BM yang bisa diubah per tes. VO2max terisi otomatis dari tabel referensi Yo-Yo bila hanya level yang diisi. | Wajib |
| F-06 | Performa teknik | Simpan skor kualifikasi, skor eliminasi, maximal shooting, bow training, bow draw, battery test, dan speed shooting per tanggal, dengan BM per tes. | Wajib |
| F-07 | Data beban | Simpan beban dan repetisi untuk 17 latihan; 1RM dihitung otomatis; profil menampilkan 1RM terbaru per latihan. | Wajib |
| F-08 | Skor bulanan | Simpan skor, tempat, dan analisis per bulan dan tahun; mendukung skor dua babak (mis. 307+304); ditampilkan sebagai tabel dan grafik garis. | Wajib |
| F-09 | Peralatan | Simpan 15 komponen peralatan per atlet (riser, limbs, sight, scope, plunger, clicker, arrow rest, stabilizer, side stabilizer, weight, string, peep sight, arrow, finger tab / release, stand). | Wajib |
| F-10 | Dashboard profil | Satu halaman per atlet: biodata, profil % capaian terhadap BM per item dan per grup, grafik radar, riwayat, dan akses ke seluruh data di atas. | Wajib |
| F-11 | Buku Saku | Tabel mingguan Senin sampai Sabtu: centang teknik yang diperbaiki (a Grip, b Set-up, c Full Draw & Ancore, d Iming & Focus, e Release, f Follow Through), pilihan Ya atau Tidak untuk shoot 100 arrow, dan catatan. Navigasi antar minggu dan rekap 8 minggu terakhir. Atlet mengisi milik sendiri. | Wajib |
| F-12 | Head to Head | Staf memilih dua atlet; sistem menampilkan radar profil, antropometri, persentase per item fisik dan teknik, skor terbaik, dan rata-rata skor. | Wajib |
| F-13 | Dashboard staf | Jumlah atlet aktif, rata-rata profil fisik dan teknik tim, dan jumlah hari shoot 100 arrow per atlet minggu ini. | Wajib |
| F-14 | Impor Excel | Perintah `archery:import-excel` mengimpor atlet dan semua data berseri dari file ATHTRIX; pencocokan antar-sheet berdasarkan nama; aman dijalankan ulang. | Wajib |
| F-15 | Pembayaran | Biaya bulanan, atlet unggah bukti transfer, admin konfirmasi, notifikasi bila belum bayar, akses diblokir bila lewat batas waktu. | Rilis berikutnya |
| F-16 | Task dan Result task | Pelatih menugaskan tes ke atlet; hasil mengalir ke profil dan Head to Head. Definisi belum dikonfirmasi penuh. | Rilis berikutnya |
| F-17 | Notifikasi WhatsApp | Pengingat ke nomor WhatsApp, dipicu Laravel scheduler. | Rilis berikutnya |

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

Skema MariaDB (utf8mb4) terdiri dari 10 tabel domain ditambah 3 tabel bawaan Laravel. Seluruh data berseri terikat ke `athletes` dan ikut terhapus bila atlet dihapus.

| Tabel | Isi utama | Catatan |
| --- | --- | --- |
| `users` | nama, email, password, role (admin, coach, athlete), phone | Role bawaan: athlete |
| `athletes` | kode unik, nama, tempat dan tanggal lahir, jenis kelamin (L/P), asal daerah, divisi, jenis pendaftaran, status aktif | `user_id` opsional (akun atlet) |
| `anthropometries` | tanggal ukur, 7 ukuran tubuh, 3 denyut nadi | IMT dihitung saat ditampilkan |
| `physical_tests` | tanggal tes, tempat, level Yo-Yo, 7 hasil dan 7 BM | BM kosong berarti memakai default |
| `technique_tests` | tanggal tes, tempat, 7 hasil dan 7 BM | Sama dengan fisik |
| `load_tests` | tanggal, latihan, beban, repetisi, 1RM | 1RM dihitung otomatis |
| `monthly_scores` | tahun, bulan, skor, catatan skor, tempat, analisis | Boleh lebih dari satu per bulan |
| `equipments` | 15 komponen peralatan | Satu baris per atlet |
| `shoot_logs` | tanggal, teknik diperbaiki (JSON a sampai f), shoot 100 arrow (ya/tidak), catatan | Unik per atlet dan tanggal |
| `yoyo_references` | level, VO2max | 91 baris dari sheet referensi Excel |
| `password_reset_tokens`, `failed_jobs`, `personal_access_tokens` | Tabel bawaan Laravel | Belum dipakai fitur |

Daftar metrik, BM, grup, kode teknik, dan definisi formulir disimpan di berkas konfigurasi `config/archery.php`, sehingga menambah item tes tidak memerlukan perubahan kode controller.

## 8. Kebutuhan non-fungsional dan arsitektur

Arsitektur monolit Laravel 10 dengan antarmuka Blade, berjalan di PHP 8.1 dan MariaDB, dapat di-host di shared hosting (sesuai keputusan awal).

| Aspek | Kebutuhan |
| --- | --- |
| Platform | PHP 8.1, Laravel 10, MariaDB; ekstensi PHP standar Laravel (mbstring, pdo\_mysql, zip, dll.) |
| Antarmuka | Blade + Bootstrap 5 dan Chart.js dari CDN; tanpa npm atau proses build; responsif untuk ponsel (atlet mengisi Buku Saku dari ponsel) |
| Keamanan | Password di-hash; proteksi CSRF; role dicek di middleware dan controller; atlet tidak bisa membuka data atlet lain; role tidak bisa dipilih saat registrasi; throttle pada login dan registrasi |
| Kompatibilitas DB | Panjang string default 191 agar aman di MariaDB atau MySQL lama pada shared hosting |
| Akurasi | Rumus profil diuji unit terhadap angka Excel (`tests/Unit/ProfileServiceTest.php`) |
| Pemeliharaan | Metrik dan BM dikonfigurasi di satu berkas; impor Excel dapat dijalankan ulang tanpa duplikasi |
| Privasi | Sebagian atlet masih remaja; data hanya diakses pengguna terautentikasi, dan perlu persetujuan orang tua atau klub sebelum data nyata dipakai di server publik |

API terpisah atau SPA tidak diperlukan pada rilis 1.

## 9. Rilis, risiko, asumsi, dan pertanyaan terbuka

**Rilis.** Kode rilis 1 sudah ditulis dan dikirim, tetapi belum dijalankan: `composer install`, migrasi, dan impor Excel belum diuji karena Packagist tidak terjangkau saat pengembangan. Target awal rilis pertama adalah 2 minggu dengan fitur utama saja; target ini tidak ketat.

| Rilis | Isi | Status |
| --- | --- | --- |
| 1 | Login, registrasi, profil, data tes, Buku Saku, Head to Head, impor Excel | Kode ditulis, belum diuji di server |
| 2 | Pembayaran bulanan dan pembatasan akses; Task dan Result task | Belum dimulai |
| 3 | Notifikasi WhatsApp; upload foto; printout beban | Belum dimulai |

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

- [ ] Apakah admin dan pelatih perlu hak berbeda (mis. hanya admin boleh menghapus atlet)?
- [ ] Siapa yang membuat akun atlet bila tidak mendaftar sendiri, dan bagaimana menghubungkannya ke data hasil impor?
- [ ] Berapa besar iuran bulanan dan batas waktu pembayaran sebelum akses diblokir?
- [ ] Apakah atlet perlu melihat Head to Head atau hanya pelatih?
- [ ] Apakah daftar divisi dan benchmark sudah final, dan siapa yang berwenang mengubahnya?
- [ ] Apakah Buku Saku perlu dikunci setelah minggu berlalu?
