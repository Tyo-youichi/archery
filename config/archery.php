<?php

/*
 * Konfigurasi domain monitoring atlet panahan.
 * Metrik, benchmark (BM) dan rumus profil diambil dari file ATHTRIX PROFILE (Excel).
 */

$physical = [
    'yoyo_vo2'     => ['label' => 'Yo-Yo IR1 (VO2max, ml/kg/mnt)', 'group' => 'endurance', 'bm' => ['L' => 56.56, 'P' => 49.84]],
    'stork_right'  => ['label' => 'Standing Stork Kanan (detik)',  'group' => 'balance',   'bm' => ['L' => 60, 'P' => 50]],
    'stork_left'   => ['label' => 'Standing Stork Kiri (detik)',   'group' => 'balance',   'bm' => ['L' => 60, 'P' => 50]],
    'grip_right'   => ['label' => 'Grip Strength Kanan (kg)',      'group' => 'strength',  'bm' => ['L' => 57.5, 'P' => 41.4]],
    'grip_left'    => ['label' => 'Grip Strength Kiri (kg)',       'group' => 'strength',  'bm' => ['L' => 57.5, 'P' => 41.4]],
    'bench_pull'   => ['label' => 'Bench Pull (repetisi)',         'group' => 'strength',  'bm' => ['L' => 30, 'P' => 20]],
    'core'         => ['label' => 'Core Stability (level)',        'group' => 'strength',  'bm' => ['L' => 12, 'P' => 12]],
];

$technique = [
    'qualification'  => ['label' => 'Skor Kualifikasi',               'group' => 'scoring',           'bm' => 625],
    'elimination'    => ['label' => 'Skor Eliminasi',                 'group' => 'scoring',           'bm' => 135],
    'max_shooting'   => ['label' => 'Maximal Shooting (AP)',          'group' => 'specific_strength', 'bm' => 350],
    'bow_training'   => ['label' => 'Bow Training (detik)',           'group' => 'specific_strength', 'bm' => 75],
    'bow_draw'       => ['label' => 'Bow Draw Max Quantity (repetisi)', 'group' => 'specific_strength', 'bm' => 37],
    'battery'        => ['label' => 'Battery Test Archery (level)',   'group' => 'specific_strength', 'bm' => 6],
    'speed_shooting' => ['label' => 'Speed Shooting (AP)',            'group' => 'specific_speed',    'bm' => 7],
];

$num = fn ($label, $step = '0.01', $rules = 'nullable|numeric|min:0') =>
    ['label' => $label, 'type' => 'number', 'step' => $step, 'rules' => $rules];

return [

    // Cache snapshot definisi metrik dari DB (App\Support\Metrics). Set false saat debugging.
    'metrics_cache' => true,
    'divisions' => ['Recurve', 'Compound', 'Barebow', 'Nasional'],
    'registration_types' => [
        'reguler'  => 'Reguler',
        'prestasi' => 'Prestasi',
        'terapi'   => 'Terapi ABK',
    ],

    'physical_metrics'  => $physical,
    'technique_metrics' => $technique,

    // Nama grup => label (urutan = urutan di radar chart)
    'physical_groups'  => ['endurance' => 'Endurance', 'balance' => 'Balance', 'strength' => 'Strength'],
    'technique_groups' => ['scoring' => 'Scoring', 'specific_strength' => 'Specific Strength', 'specific_speed' => 'Specific Speed'],

    // Rumus Excel: 1RM = beban x (1 + 0.0333 x reps)
    'one_rm_factor' => 0.0333,

    'load_exercises' => [
        'Back Squat', 'Split Squat', 'Leg Press', 'Leg Curl', 'Leg Extension', 'Bench Press',
        'Bench Pull', 'Face Pull', 'Lats Pull Down', 'Seated Rowing', 'Front Raise', 'Lateral Raise',
        'Shoulder Press', 'Australian Pull Up', 'Upright Row', 'Dumbell Triceps Extension', 'Triceps Push Down',
    ],

    // Buku Saku Atlet - Tabel Monitor Shoot 100 Arrow
    'shoot_days' => ['Sn' => 'Senin', 'Sl' => 'Selasa', 'Rb' => 'Rabu', 'Km' => 'Kamis', 'Jm' => 'Jumat', 'Sb' => 'Sabtu'],
    'technique_codes' => [
        'a' => 'Grip/Pegangan',
        'b' => 'Set-up',
        'c' => 'Full Draw & Ancore',
        'd' => 'Iming & Focus',
        'e' => 'Release',
        'f' => 'Follow Through',
    ],

    'equipment_fields' => [
        'riser' => 'Riser', 'limbs' => 'Limbs', 'sight' => 'Sight', 'scope' => 'Scope', 'plunger' => 'Plunger',
        'clicker' => 'Clicker', 'arrow_rest' => 'Arrow Rest', 'stabilizer' => 'Stabilizer',
        'side_stabilizer' => 'Side Stabilizer', 'weight' => 'Weight', 'string' => 'String', 'peep_sight' => 'Peep Sight',
        'arrow' => 'Arrow', 'finger_tab_release' => 'Finger Tab / Release', 'stand' => 'Stand',
    ],

    /*
     * Definisi form generik untuk RecordController.
     * type => model, label, field list, kolom tanggal untuk urutan.
     */
    'records' => [
        'anthropometry' => [
            'model' => \App\Models\Anthropometry::class,
            'label' => 'Antropometri',
            'date'  => 'measured_at',
            'fields' => [
                'measured_at'       => ['label' => 'Tanggal ukur', 'type' => 'date', 'rules' => 'required|date'],
                'height_cm'         => $num('Tinggi badan (cm)', '0.1'),
                'sitting_height_cm' => $num('Tinggi duduk (cm)', '0.1'),
                'weight_kg'         => $num('Berat badan (kg)', '0.1'),
                'leg_length_cm'     => $num('Panjang tungkai (cm)', '0.1'),
                'arm_length_cm'     => $num('Panjang lengan (cm)', '0.1'),
                'arm_span_cm'       => $num('Rentang lengan (cm)', '0.1'),
                'shoulder_width_cm' => $num('Lebar bahu (cm)', '0.1'),
                'hr_rest'           => $num('Denyut nadi istirahat', '1'),
                'hr_max'            => $num('Denyut nadi maksimal', '1'),
                'hr_training'       => $num('Denyut nadi latihan', '1'),
            ],
        ],
        'physical' => [
            'model' => \App\Models\PhysicalTest::class,
            'label' => 'Tes Performa Fisik',
            'date'  => 'tested_at',
            // Field metrik (nilai + BM) disisipkan dinamis oleh RecordController dari tabel `metrics`.
            'fields' => ['tested_at' => ['label' => 'Tanggal tes', 'type' => 'date', 'rules' => 'required|date'],
                         'venue' => ['label' => 'Tempat tes', 'type' => 'text', 'rules' => 'nullable|string|max:150'],
                         'yoyo_level' => $num('Yo-Yo IR1 level (mis. 14.5; VO2max terisi otomatis dari tabel referensi bila kosong)', '0.1')],
        ],
        'technique' => [
            'model' => \App\Models\TechniqueTest::class,
            'label' => 'Performa Teknik',
            'date'  => 'tested_at',
            // Field metrik (nilai + BM) disisipkan dinamis oleh RecordController dari tabel `metrics`.
            'fields' => ['tested_at' => ['label' => 'Tanggal tes', 'type' => 'date', 'rules' => 'required|date'],
                         'venue' => ['label' => 'Tempat tes', 'type' => 'text', 'rules' => 'nullable|string|max:150']],
        ],
        'load' => [
            'model' => \App\Models\LoadTest::class,
            'label' => 'Data Beban (1RM)',
            'date'  => 'tested_at',
            'fields' => [
                'tested_at' => ['label' => 'Tanggal tes', 'type' => 'date', 'rules' => 'required|date'],
                'exercise'  => ['label' => 'Latihan', 'type' => 'select', 'options' => 'load_exercises', 'rules' => 'required|string|max:80'],
                'weight_kg' => $num('Beban (kg)', '0.5', 'required|numeric|min:0'),
                'reps'      => $num('Repetisi', '1', 'required|integer|min:1|max:100'),
            ],
        ],
        'score' => [
            'model' => \App\Models\MonthlyScore::class,
            'label' => 'Skor Bulanan',
            'date'  => 'created_at',
            'fields' => [
                'year'       => $num('Tahun', '1', 'required|integer|min:2000|max:2100'),
                'month'      => $num('Bulan (1-12)', '1', 'required|integer|min:1|max:12'),
                'score'      => $num('Skor', '1', 'nullable|integer|min:0|max:720'),
                'score_note' => ['label' => 'Catatan skor (mis. 307+304)', 'type' => 'text', 'rules' => 'nullable|string|max:60'],
                'venue'      => ['label' => 'Tempat', 'type' => 'text', 'rules' => 'nullable|string|max:150'],
                'analysis'   => ['label' => 'Analisis', 'type' => 'textarea', 'rules' => 'nullable|string|max:2000'],
            ],
        ],
    ],
];
