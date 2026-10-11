<?php

namespace App\Console\Commands;

use App\Models\Anthropometry;
use App\Models\Athlete;
use App\Models\Equipment;
use App\Models\LoadTest;
use App\Models\MonthlyScore;
use App\Models\PhysicalTest;
use App\Models\TechniqueTest;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\Date as XlDate;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Impor data dari file "ATHTRIX PROFILE ... .XLSM".
 *
 *   php artisan archery:import-excel /path/ATHTRIX.XLSM --year=2026 --test-date=2026-01-15
 *
 * Penting: ID atlet di beberapa sheet TIDAK konsisten (mis. ATL3 di sheet teknik adalah
 * atlet berbeda dari ATL3 di DATA ATLET), jadi pencocokan dilakukan berdasarkan NAMA.
 * Aman dijalankan ulang (upsert).
 */
class ImportAthtrixExcel extends Command
{
    protected $signature = 'archery:import-excel {file : Path file .xlsm/.xlsx}
                            {--year= : Tahun untuk sheet skor bulanan (default tahun berjalan)}
                            {--test-date= : Tanggal tes untuk data fisik/teknik/beban (default hari ini)}';

    protected $description = 'Impor atlet, antropometri, tes fisik/teknik, skor bulanan, peralatan, dan beban dari Excel ATHTRIX';

    private array $byName = [];

    public function handle(): int
    {
        $file = $this->argument('file');
        if (! is_file($file)) {
            $this->error("File tidak ditemukan: $file");

            return self::FAILURE;
        }
        $year = (int) ($this->option('year') ?: now()->year);
        $date = $this->option('test-date') ?: now()->toDateString();

        $reader = new Xlsx();
        $reader->setReadDataOnly(true);
        $reader->setLoadSheetsOnly(['DATA ATLET', 'DATA PERFORMA FISIK', 'DATA PERFORMA TEKNIK',
            'DATA SKOR BULANAN', 'DATA PERALATAN', 'DATA BEBAN MAX']);
        $wb = $reader->load($file);

        DB::transaction(function () use ($wb, $year, $date) {
            $this->athletes($wb->getSheetByName('DATA ATLET'));
            $this->physical($wb->getSheetByName('DATA PERFORMA FISIK'), $date);
            $this->technique($wb->getSheetByName('DATA PERFORMA TEKNIK'), $date);
            $this->scores($wb->getSheetByName('DATA SKOR BULANAN'), $year);
            $this->equipment($wb->getSheetByName('DATA PERALATAN'));
            $this->loads($wb->getSheetByName('DATA BEBAN MAX'), $date);
        });

        $this->info('Selesai.');

        return self::SUCCESS;
    }

    // ---------- helpers ----------

    private function v(Worksheet $ws, int $col, int $row)
    {
        if (! $ws->cellExists([$col, $row])) {
            return null;
        }
        $cell = $ws->getCell([$col, $row]);
        $v = $cell->getValue();
        if (is_string($v) && str_starts_with($v, '=')) {
            $v = $cell->getOldCalculatedValue();   // nilai cache hasil hitung Excel
        }
        if (is_string($v)) {
            $v = trim($v);
            if ($v === '' || $v === '-') {
                return null;
            }
        }

        return $v;
    }

    private function num($v): ?float
    {
        return is_numeric($v) ? (float) $v : null;
    }

    private function key(?string $name): string
    {
        return strtoupper(preg_replace('/\s+/', ' ', trim((string) $name)));
    }

    private function athleteFor(Worksheet $ws, int $row, int $nameCol = 3): ?Athlete
    {
        $name = $this->v($ws, $nameCol, $row);

        return $name ? ($this->byName[$this->key($name)] ?? null) : null;
    }

    private function rows(Worksheet $ws, int $first): \Generator
    {
        $last = $ws->getHighestDataRow();
        for ($r = $first; $r <= $last; $r++) {
            if ($this->v($ws, 3, $r)) {
                yield $r;
            }
        }
    }

    // ---------- sheets ----------

    private function athletes(Worksheet $ws): void
    {
        $n = 0;
        foreach ($this->rows($ws, 5) as $r) {
            $name = (string) $this->v($ws, 3, $r);
            $code = strtoupper((string) $this->v($ws, 2, $r));
            $code = preg_replace('/^ALT/', 'ATL', $code);           // typo "ALT2" di Excel
            $ttl = (string) $this->v($ws, 4, $r);
            $gender = match ($this->v($ws, 6, $r)) { 'Laki-laki' => 'L', 'Perempuan' => 'P', default => null };

            $birthDate = null;
            $place = null;
            if ($ttl !== '') {                                          // K hanya valid bila TTL terisi
                $place = trim(preg_split('/\d/', $ttl)[0]);
                $k = $this->v($ws, 11, $r);
                if (is_numeric($k)) {
                    $birthDate = XlDate::excelToDateTimeObject($k)->format('Y-m-d');
                } elseif ($k instanceof \DateTimeInterface) {
                    $birthDate = $k->format('Y-m-d');
                }
            }

            $a = Athlete::updateOrCreate(['code' => $code ?: Athlete::nextCode()], [
                'name' => $name,
                'birth_place' => $place ?: null,
                'birth_date' => $birthDate,
                'gender' => $gender,
                'origin' => $this->v($ws, 7, $r),
                'division' => $this->v($ws, 8, $r),
            ]);
            $this->byName[$this->key($name)] = $a;
            $n++;
        }
        $this->line("Atlet: $n");
    }

    private function physical(Worksheet $ws, string $date): void
    {
        $cols = [ // metrik => [kolom hasil, kolom BM]
            'yoyo_vo2' => [19, 20], 'stork_right' => [22, 23], 'stork_left' => [25, 26],
            'grip_right' => [28, 29], 'grip_left' => [31, 32], 'bench_pull' => [34, 35], 'core' => [37, 38],
        ];
        $n = 0;
        foreach ($this->rows($ws, 3) as $r) {
            if (! ($a = $this->athleteFor($ws, $r))) {
                $this->warn('Fisik: nama tidak ada di DATA ATLET: ' . $this->v($ws, 3, $r));
                continue;
            }
            $cm = fn ($x) => ($x = $this->num($x)) === null ? null : ($x < 3 ? $x * 100 : $x);   // meter -> cm
            Anthropometry::updateOrCreate(['athlete_id' => $a->id, 'measured_at' => $date], [
                'height_cm' => $cm($this->v($ws, 9, $r)),
                'sitting_height_cm' => $this->num($this->v($ws, 10, $r)),
                'weight_kg' => $this->num($this->v($ws, 11, $r)),
                'leg_length_cm' => $this->num($this->v($ws, 12, $r)),
                'arm_length_cm' => $this->num($this->v($ws, 13, $r)),
                'arm_span_cm' => $this->num($this->v($ws, 14, $r)),
                'shoulder_width_cm' => $this->num($this->v($ws, 15, $r)),
                'hr_rest' => $this->num($this->v($ws, 6, $r)),
                'hr_max' => $this->num($this->v($ws, 7, $r)),
                'hr_training' => $this->num($this->v($ws, 8, $r)),
            ]);

            $vals = ['yoyo_level' => $this->num($this->v($ws, 18, $r))];
            foreach ($cols as $key => [$res, $bm]) {
                $vals[$key] = $this->num($this->v($ws, $res, $r));
                $vals[$key . '_bm'] = $this->num($this->v($ws, $bm, $r));
            }
            if (array_filter($vals, fn ($x) => $x !== null)) {
                PhysicalTest::updateOrCreate(['athlete_id' => $a->id, 'tested_at' => $date], $vals);
            }
            $n++;
        }
        $this->line("Antropometri & tes fisik: $n");
    }

    private function technique(Worksheet $ws, string $date): void
    {
        $cols = ['qualification' => [4, 5], 'elimination' => [7, 8], 'max_shooting' => [10, 11],
                 'bow_training' => [13, 14], 'bow_draw' => [16, 17], 'battery' => [19, 20], 'speed_shooting' => [22, 23]];
        $n = 0;
        foreach ($this->rows($ws, 3) as $r) {
            if (! ($a = $this->athleteFor($ws, $r))) {
                $this->warn('Teknik: nama tidak ada di DATA ATLET: ' . $this->v($ws, 3, $r));
                continue;
            }
            $vals = [];
            foreach ($cols as $key => [$res, $bm]) {
                $vals[$key] = $this->num($this->v($ws, $res, $r));
                $vals[$key . '_bm'] = $this->num($this->v($ws, $bm, $r));
            }
            if (array_filter($vals, fn ($x) => $x !== null)) {
                TechniqueTest::updateOrCreate(['athlete_id' => $a->id, 'tested_at' => $date], $vals);
                $n++;
            }
        }
        $this->line("Performa teknik: $n");
    }

    private function scores(Worksheet $ws, int $year): void
    {
        $n = 0;
        foreach ($this->rows($ws, 3) as $r) {
            if (! ($a = $this->athleteFor($ws, $r))) {
                $this->warn('Skor: nama tidak ada di DATA ATLET: ' . $this->v($ws, 3, $r));
                continue;
            }
            for ($m = 1; $m <= 12; $m++) {
                $c = 4 + ($m - 1) * 3;                 // blok per bulan: SKOR, TEMPAT, ANALISIS
                $raw = $this->v($ws, $c, $r);
                if ($raw === null) {
                    continue;
                }
                $score = null;
                $note = null;
                if (is_numeric($raw)) {
                    $score = (int) $raw;
                } elseif (preg_match('/^\d+(\s*\+\s*\d+)+$/', (string) $raw)) {   // mis. "307+304"
                    $score = (int) array_sum(preg_split('/\s*\+\s*/', (string) $raw));
                    $note = (string) $raw;
                } else {
                    $note = (string) $raw;
                }
                MonthlyScore::updateOrCreate(['athlete_id' => $a->id, 'year' => $year, 'month' => $m], [
                    'score' => $score,
                    'score_note' => $note,
                    'venue' => $this->v($ws, $c + 1, $r),
                    'analysis' => $this->v($ws, $c + 2, $r),
                ]);
                $n++;
            }
        }
        $this->line("Skor bulanan: $n");
    }

    private function equipment(Worksheet $ws): void
    {
        $fields = array_keys(config('archery.equipment_fields'));   // urutan sama dengan kolom D..R
        $n = 0;
        foreach ($this->rows($ws, 2) as $r) {
            if (! ($a = $this->athleteFor($ws, $r))) {
                continue;
            }
            $vals = [];
            foreach ($fields as $i => $f) {
                $v = $this->v($ws, 4 + $i, $r);
                $vals[$f] = $v === null ? null : (string) $v;
            }
            if (array_filter($vals)) {
                Equipment::updateOrCreate(['athlete_id' => $a->id], $vals);
                $n++;
            }
        }
        $this->line("Peralatan: $n");
    }

    private function loads(Worksheet $ws, string $date): void
    {
        $known = config('archery.load_exercises');
        $starts = [];                                   // kolom awal tiap latihan dari baris header 1
        $last = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($ws->getHighestDataColumn(1));
        for ($c = 4; $c <= $last; $c++) {
            $h = $this->v($ws, $c, 1);
            if ($h !== null) {
                $name = ucwords(strtolower((string) $h));
                if (in_array($name, $known, true)) {
                    $starts[$name] = $c;                // beban = c, reps = c+1
                }
            }
        }
        $n = 0;
        foreach ($this->rows($ws, 3) as $r) {
            if (! ($a = $this->athleteFor($ws, $r))) {
                continue;
            }
            foreach ($starts as $exercise => $c) {
                $w = $this->num($this->v($ws, $c, $r));
                $reps = $this->num($this->v($ws, $c + 1, $r));
                if ($w === null || ! $reps) {
                    continue;
                }
                LoadTest::updateOrCreate(
                    ['athlete_id' => $a->id, 'exercise' => $exercise, 'tested_at' => $date],
                    ['weight_kg' => $w, 'reps' => (int) $reps]
                );
                $n++;
            }
        }
        $this->line("Data beban: $n");
    }
}
