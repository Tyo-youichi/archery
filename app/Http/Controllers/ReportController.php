<?php

namespace App\Http\Controllers;

use App\Models\Athlete;
use App\Models\MonthlyScore;
use App\Support\Metrics;
use Illuminate\Http\Request;

/**
 * Laporan rekap LINTAS-ATLET (semua atlet dalam satu tabel), meniru layout sheet
 * pada file ATHTRIX PROFILE. Akses admin & coach (diatur di tabel RBAC: reports.*).
 * Lima jenis: performa fisik, performa teknik, skor bulanan, peralatan, beban max.
 * Semua ramah cetak / Simpan PDF (landscape).
 */
class ReportController extends Controller
{
    private const TYPES = ['fisik', 'teknik', 'skor', 'peralatan', 'beban'];

    private const TITLES = [
        'fisik'     => 'Data Performa Fisik',
        'teknik'    => 'Data Performa Teknik',
        'skor'      => 'Data Skor Bulanan',
        'peralatan' => 'Data Peralatan',
        'beban'     => 'Data Beban Max (1RM)',
    ];

    /** Pilihan jumlah baris per halaman. 'all' = tanpa paginasi (untuk cetak). */
    private const PER_PAGE = ['25', '50', 'all'];

    public function index(Request $request, string $type = 'fisik')
    {
        abort_unless(in_array($type, self::TYPES, true), 404);

        $per     = in_array($request->input('per'), self::PER_PAGE, true) ? $request->input('per') : '25';
        $perPage = $per === 'all' ? 100000 : (int) $per;

        $athletes = Athlete::query()
            ->when(! $request->boolean('all'), fn ($q) => $q->where('is_active', true))
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();

        $data = match ($type) {
            'fisik'     => $this->physical($athletes),
            'teknik'    => $this->technique($athletes),
            'skor'      => $this->score($athletes, $request),
            'peralatan' => $this->equipment($athletes),
            'beban'     => $this->load($athletes),
        };

        return view('reports.index', array_merge([
            'type'       => $type,
            'types'      => self::TYPES,
            'titles'     => self::TITLES,
            'title'      => self::TITLES[$type],
            'athletes'   => $athletes,
            'showingAll' => $request->boolean('all'),
            'per'        => $per,
            'perPages'   => self::PER_PAGE,
        ], $data));
    }

    /** Fisik: denyut nadi + antropometri (ukur terbaru) + metrik fisik (tes terbaru). */
    private function physical($athletes): array
    {
        $athletes->load(['anthropometries', 'physicalTests']);

        return [
            'metricDefs' => Metrics::metrics('physical'),
            'hrCols'     => ['hr_rest' => 'Istirahat', 'hr_training' => 'Latihan'],
            'anthroCols' => [
                'height_cm'         => 'Tinggi Badan',
                'sitting_height_cm' => 'Tinggi Duduk',
                'weight_kg'         => 'Berat Badan',
                'leg_length_cm'     => 'Panjang Tungkai',
                'arm_length_cm'     => 'Panjang Lengan',
                'arm_span_cm'       => 'Rentang Lengan',
                'shoulder_width_cm' => 'Lebar Bahu',
            ],
        ];
    }

    /** Teknik: metrik teknik (tes terbaru). */
    private function technique($athletes): array
    {
        $athletes->load('techniqueTests');

        return ['metricDefs' => Metrics::metrics('technique')];
    }

    /** Skor bulanan: pivot 12 bulan untuk satu tahun. */
    private function score($athletes, Request $request): array
    {
        $years = MonthlyScore::query()->select('year')->distinct()->orderByDesc('year')->pluck('year')->all();
        $year  = (int) $request->input('year', $years[0] ?? now()->year);

        $athletes->load(['monthlyScores' => fn ($q) => $q->where('year', $year)]);

        return [
            'year'   => $year,
            'years'  => $years ?: [now()->year],
            'months' => [1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
        ];
    }

    /** Peralatan (1 baris per atlet). */
    private function equipment($athletes): array
    {
        $athletes->load('equipment');

        return ['equipmentFields' => config('archery.equipment_fields')];
    }

    /** Beban max: 1RM terbaru per latihan. */
    private function load($athletes): array
    {
        $athletes->load('loadTests');

        // Peta 1RM terbaru per latihan untuk tiap atlet (relasi sudah urut tested_at desc).
        $latest = [];
        foreach ($athletes as $a) {
            $map = [];
            foreach ($a->loadTests as $lt) {
                $map[$lt->exercise] ??= $lt->one_rm;
            }
            $latest[$a->id] = $map;
        }

        return [
            'exercises' => config('archery.load_exercises'),
            'oneRm'     => $latest,
        ];
    }
}
