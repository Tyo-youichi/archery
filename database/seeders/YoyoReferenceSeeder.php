<?php

namespace Database\Seeders;

use App\Models\YoyoReference;
use Illuminate\Database\Seeder;

/** Tabel referensi Yo-Yo Intermittent Recovery Test Level 1 (level -> VO2max). */
class YoyoReferenceSeeder extends Seeder
{
    public function run(): void
    {
        $rows = json_decode(file_get_contents(__DIR__ . '/data/yoyo_reference.json'), true);
        foreach ($rows as $r) {
            YoyoReference::updateOrCreate(['level' => $r['level']], ['vo2max' => $r['vo2max']]);
        }
    }
}
