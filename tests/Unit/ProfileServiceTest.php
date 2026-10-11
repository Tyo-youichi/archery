<?php

namespace Tests\Unit;

use App\Support\ProfileService;
use PHPUnit\Framework\TestCase;

/** Angka acuan diambil dari sheet DATA PERFORMA TEKNIK / FISIK pada Excel ATHTRIX (baris atlet pertama). */
class ProfileServiceTest extends TestCase
{
    private function cfg(): array
    {
        return require __DIR__ . '/../../config/archery.php';
    }

    public function test_technique_profile_matches_excel(): void
    {
        $c = $this->cfg();
        $rec = (object) [
            'qualification' => 589, 'qualification_bm' => 625, 'elimination' => 125, 'elimination_bm' => 135,
            'max_shooting' => 430, 'max_shooting_bm' => 350, 'bow_training' => 54, 'bow_training_bm' => 75,
            'bow_draw' => 49, 'bow_draw_bm' => 37, 'battery' => 8, 'battery_bm' => 6,
            'speed_shooting' => 8, 'speed_shooting_bm' => 7,
        ];
        $r = ProfileService::evaluate($rec, $c['technique_metrics'], $c['technique_groups'], 'L');

        $this->assertEqualsWithDelta(0.934163, $r['groups']['scoring']['pct'], 1e-6);
        $this->assertEqualsWithDelta(0.93, $r['groups']['specific_strength']['pct'], 1e-6);
        $this->assertEqualsWithDelta(1.0, $r['groups']['specific_speed']['pct'], 1e-6);
        $this->assertEqualsWithDelta(0.954721, $r['overall'], 1e-6);
    }

    public function test_physical_profile_uses_gender_default_benchmarks(): void
    {
        $c = $this->cfg();
        $rec = (object) [
            'yoyo_vo2' => 41.78, 'stork_right' => 55, 'stork_left' => 60, 'grip_right' => 38, 'grip_left' => 38,
            'bench_pull' => 33, 'core' => 12,
        ];
        foreach (array_keys($c['physical_metrics']) as $k) {
            $rec->{$k . '_bm'} = null;                       // pakai BM default per gender
        }
        $r = ProfileService::evaluate($rec, $c['physical_metrics'], $c['physical_groups'], 'L');

        $this->assertEqualsWithDelta(0.738685, $r['groups']['endurance']['pct'], 1e-6);   // 41.78 / 56.56
        $this->assertEqualsWithDelta(0.958333, $r['groups']['balance']['pct'], 1e-6);
        $this->assertSame(1, (int) ($r['metrics']['bench_pull']['pct']));                  // dibatasi 100%
    }

    public function test_empty_record_gives_null_overall(): void
    {
        $c = $this->cfg();
        $r = ProfileService::evaluate(null, $c['physical_metrics'], $c['physical_groups'], 'L');
        $this->assertNull($r['overall']);
    }
}
