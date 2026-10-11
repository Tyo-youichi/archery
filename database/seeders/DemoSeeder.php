<?php

namespace Database\Seeders;

use App\Models\Anthropometry;
use App\Models\Athlete;
use App\Models\LoadTest;
use App\Models\MonthlyScore;
use App\Models\PhysicalTest;
use App\Models\ShootLog;
use App\Models\TechniqueTest;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Data atlet dummy (fiktif) lengkap dengan antropometri, tes fisik, performa teknik,
 * skor bulanan, beban, dan Buku Saku — cukup untuk mencoba Dashboard Profil dan Head to Head.
 *
 *   php artisan db:seed --class=DemoSeeder
 *
 * Pasangan untuk Head to Head: DUM1 vs DUM2 (putra Recurve), DUM3 vs DUM4 (putri Recurve).
 * Menjalankan seeder ini juga memanggil RoleUserSeeder dan YoyoReferenceSeeder.
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([RbacSeeder::class, RoleUserSeeder::class, YoyoReferenceSeeder::class]);

        $year = now()->year;
        $testDate = Carbon::create($year, 1, 15)->toDateString();
        $monday = now()->startOfWeek();

        // metrik fisik & teknik (urutan sama dengan kolom pada array 'ph' dan 'te')
        $physKeys = array_keys(config('archery.physical_metrics'));   // yoyo_vo2, stork_right, ...
        $techKeys = array_keys(config('archery.technique_metrics'));  // qualification, ...

        foreach ($this->athletes() as $d) {
            $athlete = Athlete::updateOrCreate(['code' => $d['code']], [
                'name' => $d['name'],
                'gender' => $d['gender'],
                'division' => $d['division'],
                'birth_date' => $d['birth_date'],
                'origin' => 'Kota Bekasi, Jawa Barat',
                'registration_type' => 'prestasi',
                'is_active' => true,
            ]);

            // Tautkan akun atlet demo ke atlet pertama
            if ($d['link'] && ($u = User::where('email', $d['link'])->first())) {
                $athlete->update(['user_id' => $u->id]);
            }

            [$tb, $tduduk, $bb, $tungkai, $lengan, $rentang, $bahu, $hrR, $hrM, $hrL] = $d['an'];
            Anthropometry::updateOrCreate(['athlete_id' => $athlete->id, 'measured_at' => $testDate], [
                'height_cm' => $tb, 'sitting_height_cm' => $tduduk, 'weight_kg' => $bb,
                'leg_length_cm' => $tungkai, 'arm_length_cm' => $lengan, 'arm_span_cm' => $rentang,
                'shoulder_width_cm' => $bahu, 'hr_rest' => $hrR, 'hr_max' => $hrM, 'hr_training' => $hrL,
            ]);

            // Tes fisik: ph = [yoyo_level, yoyo_vo2, stork_r, stork_l, grip_r, grip_l, bench, core]
            $ph = $d['ph'];
            $physVals = ['yoyo_level' => $ph[0]];
            foreach ($physKeys as $i => $key) {
                $physVals[$key] = $ph[$i + 1];   // BM dibiarkan null => pakai default per gender
            }
            PhysicalTest::updateOrCreate(['athlete_id' => $athlete->id, 'tested_at' => $testDate], $physVals);

            // Performa teknik: te sejajar dengan $techKeys
            $techVals = [];
            foreach ($techKeys as $i => $key) {
                $techVals[$key] = $d['te'][$i];  // BM default dari config
            }
            TechniqueTest::updateOrCreate(['athlete_id' => $athlete->id, 'tested_at' => $testDate], $techVals);

            foreach ($d['sc'] as [$month, $score]) {
                MonthlyScore::updateOrCreate(
                    ['athlete_id' => $athlete->id, 'year' => $year, 'month' => $month],
                    ['score' => $score, 'venue' => 'Lapangan Panahan Synergi']
                );
            }

            foreach ($d['ld'] as [$exercise, $weight, $reps]) {
                LoadTest::updateOrCreate(
                    ['athlete_id' => $athlete->id, 'exercise' => $exercise, 'tested_at' => $testDate],
                    ['weight_kg' => $weight, 'reps' => $reps]   // one_rm dihitung otomatis oleh model
                );
            }

            // Buku Saku minggu ini: 4 hari pertama
            foreach (range(0, 3) as $i) {
                ShootLog::updateOrCreate(
                    ['athlete_id' => $athlete->id, 'log_date' => $monday->copy()->addDays($i)->toDateString()],
                    ['techniques' => array_slice(['a', 'b', 'c', 'd', 'e', 'f'], 0, ($i % 3) + 1),
                     'shot_100' => $i % 2 === 0]
                );
            }
        }
    }

    private function athletes(): array
    {
        return [
            ['code' => 'DUM1', 'name' => 'ARYA PRATAMA', 'gender' => 'L', 'division' => 'Recurve', 'birth_date' => '2008-03-12', 'link' => 'athlete@archer.test',
             'an' => [170,88,62,98,70,170,42,72,200,120], 'ph' => [18.5,54.0,58,56,55,52,33,12], 'te' => [650,130,430,60,49,8,8], 'sc' => [[1,589], [2,608], [3,620], [4,641], [5,650]], 'ld' => [['Back Squat',60,8], ['Bench Press',40,6], ['Seated Rowing',45,10]]],
            ['code' => 'DUM2', 'name' => 'BAGUS SETIAWAN', 'gender' => 'L', 'division' => 'Recurve', 'birth_date' => '2009-07-01', 'link' => null,
             'an' => [168,86,58,95,68,166,40,76,205,122], 'ph' => [15.5,44.0,40,38,42,40,24,10], 'te' => [560,118,400,35,41,7,8], 'sc' => [[1,566], [2,590], [3,566], [4,585]], 'ld' => [['Back Squat',45,10], ['Bench Press',30,8], ['Seated Rowing',35,12]]],
            ['code' => 'DUM3', 'name' => 'CITRA LESTARI', 'gender' => 'P', 'division' => 'Recurve', 'birth_date' => '2008-11-20', 'link' => null,
             'an' => [162,84,52,92,66,160,38,74,198,118], 'ph' => [16.5,48.0,50,48,40,38,22,12], 'te' => [640,131,460,66,42,8,9], 'sc' => [[1,625], [2,632], [3,641], [4,647], [5,639]], 'ld' => [['Back Squat',50,8], ['Bench Press',30,6], ['Seated Rowing',40,10]]],
            ['code' => 'DUM4', 'name' => 'DINDA PERMATA', 'gender' => 'P', 'division' => 'Recurve', 'birth_date' => '2010-01-15', 'link' => null,
             'an' => [158,82,48,90,64,156,37,78,200,120], 'ph' => [13.5,40.0,35,33,30,28,15,9], 'te' => [517,120,520,46,46,8,9], 'sc' => [[1,517], [2,589], [3,583], [4,584]], 'ld' => [['Back Squat',35,10], ['Bench Press',22,8], ['Seated Rowing',30,12]]],
            ['code' => 'DUM5', 'name' => 'EKO NUGROHO', 'gender' => 'L', 'division' => 'Compound', 'birth_date' => '2007-05-09', 'link' => null,
             'an' => [173,89,66,100,72,174,43,70,205,124], 'ph' => [19.0,56.0,60,60,57,55,30,12], 'te' => [660,134,510,54,49,8,9], 'sc' => [[1,657], [2,633], [3,654], [4,653]], 'ld' => [['Back Squat',65,8], ['Bench Press',45,6], ['Seated Rowing',48,10]]],
            ['code' => 'DUM6', 'name' => 'FARAH AULIA', 'gender' => 'P', 'division' => 'Compound', 'birth_date' => '2009-09-03', 'link' => null,
             'an' => [160,83,50,91,65,158,38,75,199,119], 'ph' => [14.5,42.0,42,40,34,32,17,11], 'te' => [580,125,460,50,40,7,8], 'sc' => [[1,580], [2,575], [3,582], [4,601]], 'ld' => [['Back Squat',40,10], ['Bench Press',25,8], ['Seated Rowing',35,11]]],
        ];
    }
}
