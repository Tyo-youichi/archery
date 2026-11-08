# 10 · Metrik & Benchmark

CRUD definisi metrik tes (fisik/teknik) + benchmark (BM). Mengubah di sini langsung
memengaruhi form input, perhitungan profil, Head to Head, dan Laporan.

Controller: `app/Http/Controllers/MetricController.php`
Model: `app/Models/Metric.php`
Helper: `app/Support/Metrics.php`, `app/Support/ProfileService.php`
View: `resources/views/metrics/{index,form}.blade.php`

| Route (resource `metrics`, kecuali show) | Name | Method | Akses RBAC |
|------|------|--------|------------|
| `GET /metrics` | `metrics.index` | `index` | admin |
| `GET /metrics/create` | `metrics.create` | `create` | admin |
| `POST /metrics` | `metrics.store` | `store` | admin |
| `GET /metrics/{metric}/edit` | `metrics.edit` | `edit` | admin |
| `PUT /metrics/{metric}` | `metrics.update` | `update` | admin |
| `DELETE /metrics/{metric}` | `metrics.destroy` | `destroy` | admin |

> Pola RBAC `metrics.*` → admin.

## Cara kerja
- **Domain**: `physical` / `technique`. Tiap metrik punya `key` (kunci di JSON `results`),
  `group_key`/`group_label`, `label`, `unit`, `step`, `bm_l`, `bm_p`, `sort`, `is_active`, `is_promoted`.
- `store/update/destroy` selalu memanggil **`Metrics::flush()`** (buang cache snapshot).
- `update` **mengunci `domain` & `key`** (dipakai sebagai kunci data lama di JSON `results`).
- `destroy` tidak menghapus data tes lama — hanya definisi; nilai lama tetap di `results`.

## Rantai pemakaian
```
Metric (tabel metrics)
   └─ App\Support\Metrics  ── snapshot + cache (fallback config/archery.php)
        ├─ RecordController   → bangun field form fisik/teknik (lihat 03)
        ├─ ProfileService     → hitung % capaian vs BM (profil, dashboard, H2H)
        └─ ReportController   → kolom laporan fisik/teknik (lihat 07)
```

- **Snapshot BM**: nilai BM ikut disimpan di `results.<key>.bm` saat input, jadi perubahan
  benchmark tidak mengubah persentase data historis.
- **Fallback**: bila tabel `metrics` belum di-seed/kosong, `Metrics` memakai
  `config('archery.physical_metrics' / 'technique_metrics' / '..._groups')`.
- **Kolom generated** (untuk analitik lintas-atlet): `physical_tests.yoyo_vo2_val` &
  `technique_tests.qualification_val` diturunkan otomatis dari JSON (`virtualAs` + index).
