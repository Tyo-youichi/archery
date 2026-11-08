# 3 · Atlet & Data Per-Atlet

Mencakup menu **Atlet** (CRUD + profil) dan input data berseri: antropometri, tes fisik,
tes teknik, beban (1RM), skor bulanan.

Controller: `app/Http/Controllers/AthleteController.php`, `app/Http/Controllers/RecordController.php`
Helper: `app/Support/ProfileService.php`, `app/Support/Metrics.php`, trait `Concerns/AuthorizesAthlete.php`

---

## 3.1 CRUD & Profil Atlet — `AthleteController`

| Route | Name | Method | Akses RBAC |
|-------|------|--------|------------|
| `GET /athletes` | `athletes.index` | `index` | admin, coach |
| `GET /athletes/create` | `athletes.create` | `create` | admin, coach |
| `POST /athletes` | `athletes.store` | `store` | admin, coach |
| `GET /athletes/{athlete}` | `athletes.show` | `show` | `*` (atlet: miliknya; dicek controller) |
| `GET /athletes/{athlete}/edit` | `athletes.edit` | `edit` | admin, coach, athlete |
| `PUT /athletes/{athlete}` | `athletes.update` | `update` | admin, coach, athlete |
| `DELETE /athletes/{athlete}` | `athletes.destroy` | `destroy` | admin |

### `index()` — daftar atlet
Filter `q` (nama/kode) & `division`, `paginate(25)`. Model `Athlete`. View `resources/views/athletes/index.blade.php`.

### `show()` — halaman profil (inti aplikasi)
1. `authorizeView($athlete)` (trait — staf semua, atlet hanya miliknya).
2. **`ProfileService::forAthlete()`** → antropometri terbaru + evaluasi `physical` & `technique` (hasil vs benchmark).
3. Ambil `monthlyScores`, `anthropometries`, 1RM terbaru per latihan (`LoadTest`).
4. Bangun data chart (radar, skor, berat badan, bar fisik/teknik, beban) untuk tab **Data Visual** — dilempar sebagai array ke Blade lalu dirender Chart.js (via `@json`).
5. View `resources/views/athletes/show.blade.php` (tab: biodata, fisik, teknik, skor, beban, peralatan, data visual).

| Model dipakai di `show` | `Athlete`, `Anthropometry`, `PhysicalTest`, `TechniqueTest`, `LoadTest`, `MonthlyScore`, `Equipment` |
|---|---|
| Helper | `ProfileService` (→ `Metrics`) |
| Assets | `public/js/chart.umd.min.js` |

### `store()` / `update()` / `destroy()`
- `store`: `validated()` + `handlePhoto()` → buat `Athlete`.
- `update`: `authorizeView`. **Staf** → `validated()` (semua field). **Atlet** → `validatedSelf()` (hanya nama, tempat/tgl lahir, gender, asal — kode/divisi/jenis/aktif terkunci). Lalu `handlePhoto()`.
- `destroy`: hapus foto (disk `athletes`) + record.
- **Foto**: dikompres di browser → dikirim data URL `webp` di field `photo_data`; server validasi magic bytes `RIFF`/`WEBP` lalu simpan ke `Storage::disk('athletes')`.
- View form: `resources/views/athletes/form.blade.php`.

### Panel "Akun login" di form
Form edit biodata (`athletes/form.blade.php`) menampilkan panel akun **hanya untuk pemilik**
(`auth()->id() === $athlete->user_id`) yang submit ke `account.update` → `AccountController`
(lihat [01-auth-dan-akun.md](01-auth-dan-akun.md)).

---

## 3.2 Data berseri — `RecordController` (generik)

Satu controller melayani 5 jenis record lewat parameter `{type}`. Definisi tiap jenis
(model, label, field, aturan) ada di **`config/archery.php` → `records`**.

| Route (prefix `/athletes/{athlete}/records/{type}`) | Name | Method |
|------|------|--------|
| `GET …/create` | `records.create` | `create` |
| `POST …/` | `records.store` | `store` |
| `GET …/{id}/edit` | `records.edit` | `edit` |
| `PUT …/{id}` | `records.update` | `update` |
| `DELETE …/{id}` | `records.destroy` | `destroy` |

- **Akses RBAC**: `records.*` → admin, coach.
- **`{type}`** yang valid (dari `config('archery.records')`): `anthropometry`, `physical`, `technique`, `load`, `score`.

| type | Model | Label |
|------|-------|-------|
| `anthropometry` | `App\Models\Anthropometry` | Antropometri |
| `physical` | `App\Models\PhysicalTest` | Tes Performa Fisik |
| `technique` | `App\Models\TechniqueTest` | Performa Teknik |
| `load` | `App\Models\LoadTest` | Data Beban (1RM) |
| `score` | `App\Models\MonthlyScore` | Skor Bulanan |

### Metrik dinamis (physical & technique)
- Field metrik **tidak hardcode** — diambil dari `App\Support\Metrics::metrics($domain)`
  (tabel `metrics`, fallback `config/archery.php`).
- Disimpan ke kolom **JSON `results`**: `{ "<key>": { "result": x, "bm": <snapshot>, "attempts"?: [...] } }`.
  BM di-**snapshot** saat input, jadi mengubah benchmark tidak mengubah data lama.
- **Grip 7 percobaan**: `config('archery.physical_attempts')` = `['grip_right'=>7,'grip_left'=>7]`.
  Tiap percobaan punya kotak sendiri (`<key>_a1..a7`); `result` = **nilai tertinggi**, semua
  percobaan disimpan di `attempts[]`. Pasangan kanan/kiri (`_right`/`_left`) ditata sejajar.
- Method pendukung di controller: `metricFields()` (bangun field), `rules()` (validasi), `toResults()` (flatten → JSON + snapshot BM + hitung max), `metricValues()` (prefill saat edit).

### View
`resources/views/records/form.blade.php` — generik; merender field biasa (col-md-4) dan
blok metrik (`type = metric`, value + BM menyatu, pasangan kanan/kiri sejajar).

### VO2max & 1RM otomatis (di Model, bukan controller)
- `PhysicalTest::booted()` → isi `results.yoyo_vo2.result` dari `YoyoReference` bila hanya level diisi.
- `LoadTest::booted()` → hitung `one_rm = weight_kg × (1 + 0.0333 × reps)` (`config('archery.one_rm_factor')`).
