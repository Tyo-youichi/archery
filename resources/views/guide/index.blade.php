@extends('layouts.app')
@section('title', 'Panduan Pengisian Data')

@push('head')
<style>
    .guide-toc { position: sticky; top: 1rem; }
    .guide-toc a { display:block; padding:.25rem .5rem; border-radius:.4rem; color:#334155; text-decoration:none; font-size:.9rem; }
    .guide-toc a:hover { background:#eef2f5; }
    .guide h2 { scroll-margin-top: 1rem; }
    .guide section { margin-bottom: 1.5rem; }
    .guide .badge-who { font-weight:600; }
    .guide table th { white-space: nowrap; }
    .guide code { background:#eef2f5; padding:.05rem .35rem; border-radius:.3rem; }
</style>
@endpush

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
    <div>
        <h1 class="h4 mb-1">Panduan Pengisian Data Atlet</h1>
        <p class="text-muted mb-0 small">Langkah mengisi tiap jenis data di aplikasi. Untuk admin &amp; pelatih.</p>
    </div>
</div>

<div class="row g-4 guide">
    {{-- Daftar isi --}}
    <div class="col-lg-3 d-none d-lg-block">
        <div class="card"><div class="card-body guide-toc">
            <div class="fw-semibold small text-muted mb-2">DAFTAR ISI</div>
            <a href="#alur">Alur umum</a>
            <a href="#tambah-atlet">1. Menambah atlet</a>
            <a href="#profil">2. Membuka profil &amp; tab</a>
            <a href="#antro">3. Antropometri</a>
            <a href="#fisik">4. Tes Performa Fisik</a>
            <a href="#teknik">5. Tes Performa Teknik</a>
            <a href="#skor">6. Skor Bulanan</a>
            <a href="#beban">7. Beban (1RM)</a>
            <a href="#alat">8. Peralatan</a>
            <a href="#buku-saku">9. Buku Saku (100 arrow)</a>
            <a href="#metrik">10. Metrik &amp; Benchmark</a>
            <a href="#siapa">Siapa mengisi apa</a>
        </div></div>
    </div>

    <div class="col-lg-9">

        <section id="alur" class="card"><div class="card-body">
            <h2 class="h5" >Alur umum</h2>
            <p class="mb-2">Hampir semua data diisi dari <strong>halaman profil atlet</strong>. Urutannya:</p>
            <ol class="mb-2">
                <li>Buka menu <strong>Atlet</strong> → cari/klik nama atlet (atau <strong>+ Atlet</strong> untuk mendaftarkan baru).</li>
                <li>Di profil atlet, pilih <strong>tab</strong> sesuai data yang mau diisi (Antropometri, Performa Fisik, dst).</li>
                <li>Klik tombol <strong>+ (tambah)</strong> di kanan atas kartu, isi form, lalu <strong>Simpan</strong>.</li>
                <li>Setelah simpan kamu otomatis kembali ke tab tersebut; data terbaru langsung dihitung ke grafik &amp; profil.</li>
            </ol>
            <p class="text-muted small mb-0">Tiap tab punya alamat sendiri, jadi tombol <em>Back</em> browser kembali ke tab terakhir yang dibuka.</p>
        </div></section>

        <section id="tambah-atlet" class="card"><div class="card-body">
            <h2 class="h5">1. Menambah atlet baru <span class="badge text-bg-light border badge-who">Admin / Pelatih</span></h2>
            <p class="mb-2">Menu <strong>Atlet → + Atlet</strong>.</p>
            <table class="table table-sm">
                <tr><th>Kode</th><td>Otomatis terisi (mis. <code>ATL8</code>), boleh diubah. Harus unik.</td></tr>
                <tr><th>Nama</th><td>Wajib.</td></tr>
                <tr><th>Tempat &amp; tanggal lahir</th><td>Untuk menghitung usia otomatis.</td></tr>
                <tr><th>Jenis kelamin</th><td>Menentukan benchmark (nilai acuan L/P berbeda).</td></tr>
                <tr><th>Divisi</th><td>Recurve / Compound / Barebow / Nasional.</td></tr>
                <tr><th>Jenis pendaftaran</th><td>Reguler / Prestasi / Terapi ABK.</td></tr>
                <tr><th>Foto</th><td>Opsional. Otomatis dikompres ke WEBP di browser (maks sisi 512px). Kosongkan untuk avatar anonim.</td></tr>
            </table>
            <p class="text-muted small mb-0">Status <strong>Aktif</strong> menentukan atlet muncul di dashboard/laporan. Saat atlet keluar klub, cukup <strong>nonaktifkan</strong> (jangan hapus) agar riwayatnya tetap tersimpan.</p>
        </div></section>

        <section id="profil" class="card"><div class="card-body">
            <h2 class="h5">2. Membuka profil &amp; navigasi tab</h2>
            <p class="mb-2">Klik nama atlet di menu Atlet atau Dashboard. Tab yang tersedia:</p>
            <p class="mb-0"><span class="badge text-bg-secondary">Ringkasan</span> <span class="badge text-bg-secondary">Antropometri</span> <span class="badge text-bg-secondary">Performa Fisik</span> <span class="badge text-bg-secondary">Performa Teknik</span> <span class="badge text-bg-secondary">Beban</span> <span class="badge text-bg-secondary">Skor Bulanan</span> <span class="badge text-bg-secondary">Peralatan</span></p>
        </div></section>

        <section id="antro" class="card"><div class="card-body">
            <h2 class="h5">3. Antropometri &amp; Denyut Nadi <span class="badge text-bg-light border badge-who">Pelatih</span></h2>
            <p class="mb-2">Tab <strong>Antropometri → + Pengukuran</strong>. Boleh diisi berkala; data terbaru dipakai di profil, grafik berat badan memakai seluruh riwayat.</p>
            <table class="table table-sm">
                <tr><th>Tanggal ukur</th><td>Wajib.</td></tr>
                <tr><th>TB, Tinggi duduk, BB</th><td>Satuan cm / kg. <strong>IMT dihitung otomatis</strong> dari TB &amp; BB.</td></tr>
                <tr><th>Panjang tungkai / lengan / rentang lengan / lebar bahu</th><td>cm.</td></tr>
                <tr><th>Denyut nadi istirahat / maksimal / latihan</th><td>Nadi maksimal dipakai sebagai acuan 100% gauge.</td></tr>
            </table>
        </div></section>

        <section id="fisik" class="card"><div class="card-body">
            <h2 class="h5">4. Tes Performa Fisik <span class="badge text-bg-light border badge-who">Pelatih</span></h2>
            <p class="mb-2">Tab <strong>Performa Fisik → + Hasil tes</strong>.</p>
            <table class="table table-sm">
                <tr><th>Tanggal &amp; Tempat tes</th><td>Tanggal wajib.</td></tr>
                <tr><th>Yo-Yo IR1 level</th><td>Cukup isi levelnya (mis. 14.5). <strong>VO2max terisi otomatis</strong> dari tabel referensi bila kolom VO2max dikosongkan.</td></tr>
                <tr><th>Standing Stork Kanan &amp; Kiri</th><td>Detik. Kolom kanan &amp; kiri sejajar.</td></tr>
                <tr><th>Grip Strength Kanan &amp; Kiri</th><td><strong>7 kotak percobaan</strong> tiap sisi — isi semua yang dilakukan; aplikasi memakai <strong>nilai tertinggi</strong> sebagai hasil.</td></tr>
                <tr><th>Bench Pull, Core Stability</th><td>Sesuai hasil tes.</td></tr>
                <tr><th>Kolom "BM ..."</th><td><strong>Kosongkan</strong> agar memakai benchmark default (per jenis kelamin). Isi hanya bila ingin menimpa benchmark khusus tes ini.</td></tr>
            </table>
            <p class="text-muted small mb-0">Benchmark di-<em>snapshot</em> saat menyimpan, jadi mengubah BM di menu Metrik tidak mengubah persentase data lama.</p>
        </div></section>

        <section id="teknik" class="card"><div class="card-body">
            <h2 class="h5">5. Tes Performa Teknik <span class="badge text-bg-light border badge-who">Pelatih</span></h2>
            <p class="mb-2">Tab <strong>Performa Teknik → + Hasil tes</strong>. Isi tanggal/tempat lalu tiap item: Skor Kualifikasi, Skor Eliminasi, Maximal Shooting, Bow Training, Bow Draw Max Quantity, Battery Test, Speed Shooting. Aturan kolom BM sama seperti tes fisik (kosong = default).</p>
        </div></section>

        <section id="skor" class="card"><div class="card-body">
            <h2 class="h5">6. Skor Bulanan <span class="badge text-bg-light border badge-who">Pelatih</span></h2>
            <p class="mb-2">Tab <strong>Skor Bulanan → + Skor</strong>.</p>
            <table class="table table-sm">
                <tr><th>Tahun &amp; Bulan</th><td>Bulan dipilih dari dropdown nama bulan.</td></tr>
                <tr><th>Skor</th><td>Angka total (0–720). Boleh dikosongkan bila hanya mencatat catatan.</td></tr>
                <tr><th>Catatan skor</th><td>Rincian, mis. <code>307+304</code>.</td></tr>
                <tr><th>Tempat &amp; Analisis</th><td>Lokasi dan catatan evaluasi.</td></tr>
            </table>
        </div></section>

        <section id="beban" class="card"><div class="card-body">
            <h2 class="h5">7. Beban / 1RM <span class="badge text-bg-light border badge-who">Pelatih</span></h2>
            <p class="mb-2">Tab <strong>Beban → + Data beban</strong>. Pilih latihan, isi <strong>Beban (kg)</strong> dan <strong>Repetisi</strong> — <strong>1RM dihitung otomatis</strong> (rumus Epley: beban × (1 + 0.0333 × reps)). Tabel menampilkan 1RM terbaru per latihan. Tombol <strong>Cetak</strong> menghasilkan printout 1RM siap PDF.</p>
        </div></section>

        <section id="alat" class="card"><div class="card-body">
            <h2 class="h5">8. Peralatan <span class="badge text-bg-light border badge-who">Pelatih / Atlet</span></h2>
            <p class="mb-0">Tab <strong>Peralatan → Edit peralatan</strong>. Isi tiap komponen (Riser, Limbs, Sight, … Stand) sesuai perlengkapan atlet. Atlet boleh mengisi peralatannya sendiri.</p>
        </div></section>

        <section id="buku-saku" class="card"><div class="card-body">
            <h2 class="h5">9. Buku Saku — Monitor Shoot 100 Arrow <span class="badge text-bg-light border badge-who">Pelatih / Atlet</span></h2>
            <p class="mb-2">Dari profil, tombol <strong>Buku Saku (100 arrow)</strong>. Per minggu (Senin–Sabtu):</p>
            <ul class="mb-2">
                <li>Centang <strong>teknik yang diperbaiki</strong> (a Grip, b Set-up, c Full Draw &amp; Ancore, d Iming &amp; Focus, e Release, f Follow Through).</li>
                <li>Tandai <strong>Ya/Tidak</strong> apakah melakukan shoot 100 arrow hari itu.</li>
                <li>Tulis <strong>catatan</strong> temuan latihan.</li>
            </ul>
            <p class="text-muted small mb-0">Gunakan navigasi minggu untuk pindah pekan. Rekap jumlah hari shoot 100 arrow muncul otomatis dan tampil juga di Dashboard.</p>
        </div></section>

        <section id="metrik" class="card"><div class="card-body">
            <h2 class="h5">10. Metrik &amp; Benchmark <span class="badge text-bg-dark badge-who">Admin</span></h2>
            <p class="mb-0">Menu <strong>Metrik &amp; BM</strong>. Di sini admin menamb/mengubah metrik tes fisik/teknik beserta benchmark (BM) per jenis kelamin. Perubahan hanya berlaku untuk input <strong>baru</strong>; data lama tetap memakai BM yang ter-<em>snapshot</em>. Kolom metrik di form input &amp; Laporan mengikuti daftar ini secara dinamis.</p>
        </div></section>

        <section id="siapa" class="card"><div class="card-body">
            <h2 class="h5">Ringkasan: siapa mengisi apa</h2>
            <table class="table table-sm mb-0">
                <thead><tr><th>Data</th><th>Diisi oleh</th></tr></thead>
                <tbody>
                    <tr><td>Database atlet (biodata)</td><td>Admin / Pelatih</td></tr>
                    <tr><td>Antropometri, Tes Fisik, Tes Teknik, Beban, Skor Bulanan</td><td>Pelatih</td></tr>
                    <tr><td>Peralatan &amp; Buku Saku</td><td>Pelatih atau Atlet (miliknya)</td></tr>
                    <tr><td>Metrik &amp; Benchmark, Pengguna, RBAC</td><td>Admin</td></tr>
                    <tr><td>Bukti pembayaran</td><td>Atlet (verifikasi oleh Admin)</td></tr>
                </tbody>
            </table>
        </div></section>

    </div>
</div>
@endsection
