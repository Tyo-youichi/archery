<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('athletes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('code', 20)->unique();           // ATL1, ATL2, ...
            $table->string('name');
            $table->string('birth_place', 100)->nullable();
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['L', 'P'])->nullable();
            $table->string('origin', 150)->nullable();       // asal daerah
            $table->string('division', 30)->nullable();      // Recurve / Compound / ...
            $table->enum('registration_type', ['reguler', 'prestasi', 'terapi'])->default('reguler');
            $table->string('photo')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('anthropometries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('athlete_id')->constrained()->cascadeOnDelete();
            $table->date('measured_at');
            $table->decimal('height_cm', 5, 1)->nullable();
            $table->decimal('sitting_height_cm', 5, 1)->nullable();
            $table->decimal('weight_kg', 5, 1)->nullable();
            $table->decimal('leg_length_cm', 5, 1)->nullable();
            $table->decimal('arm_length_cm', 5, 1)->nullable();
            $table->decimal('arm_span_cm', 5, 1)->nullable();
            $table->decimal('shoulder_width_cm', 5, 1)->nullable();
            $table->unsignedSmallInteger('hr_rest')->nullable();
            $table->unsignedSmallInteger('hr_max')->nullable();
            $table->unsignedSmallInteger('hr_training')->nullable();
            $table->timestamps();
            $table->index(['athlete_id', 'measured_at']);
        });

        Schema::create('yoyo_references', function (Blueprint $table) {
            $table->id();
            $table->decimal('level', 4, 1)->unique();       // Level & Shuttle, mis. 14.5
            $table->decimal('vo2max', 5, 2);
        });

        /*
         * Hasil tes fisik & teknik disimpan sebagai JSON `results` (sumber data mentah):
         *   { "<metric_key>": { "result": <nilai>, "bm": <benchmark snapshot> }, ... }
         * Dengan begini metrik bisa ditambah/dihapus lewat UI (tabel `metrics`) tanpa
         * mengubah skema. Untuk key "panas" yang perlu analitik lintas-atlet (rata-rata,
         * ranking) ditambahkan kolom GENERATED yang di-index — diturunkan otomatis dari
         * JSON sehingga tidak pernah drift & tanpa dobel-tulis. Tambah kolom semacam ini
         * lewat migration baru saat sebuah metrik ditandai "promoted".
         */
        Schema::create('physical_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('athlete_id')->constrained()->cascadeOnDelete();
            $table->date('tested_at');
            $table->string('venue', 150)->nullable();
            $table->decimal('yoyo_level', 4, 1)->nullable();
            $table->json('results')->nullable();
            $table->decimal('yoyo_vo2_val', 8, 2)
                ->virtualAs("CAST(JSON_VALUE(`results`, '\$.yoyo_vo2.result') AS DECIMAL(8,2))");
            $table->timestamps();
            $table->index(['athlete_id', 'tested_at']);
            $table->index('yoyo_vo2_val');
        });

        Schema::create('technique_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('athlete_id')->constrained()->cascadeOnDelete();
            $table->date('tested_at');
            $table->string('venue', 150)->nullable();
            $table->json('results')->nullable();
            $table->decimal('qualification_val', 8, 2)
                ->virtualAs("CAST(JSON_VALUE(`results`, '\$.qualification.result') AS DECIMAL(8,2))");
            $table->timestamps();
            $table->index(['athlete_id', 'tested_at']);
            $table->index('qualification_val');
        });

        Schema::create('load_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('athlete_id')->constrained()->cascadeOnDelete();
            $table->date('tested_at');
            $table->string('exercise', 80);
            $table->decimal('weight_kg', 6, 1);
            $table->unsignedSmallInteger('reps');
            $table->decimal('one_rm', 8, 2);
            $table->timestamps();
            $table->index(['athlete_id', 'exercise', 'tested_at']);
        });

        Schema::create('monthly_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('athlete_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('score')->nullable();
            $table->string('score_note', 60)->nullable();    // mis. "307+304"
            $table->string('venue', 150)->nullable();
            $table->text('analysis')->nullable();
            $table->timestamps();
            $table->index(['athlete_id', 'year', 'month']);
        });

        Schema::create('equipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('athlete_id')->unique()->constrained()->cascadeOnDelete();
            foreach (array_keys(config('archery.equipment_fields')) as $field) {
                $table->string($field, 150)->nullable();
            }
            $table->timestamps();
        });

        // Buku Saku Atlet: Tabel Monitor Shoot 100 Arrow
        Schema::create('shoot_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('athlete_id')->constrained()->cascadeOnDelete();
            $table->date('log_date');
            $table->json('techniques')->nullable();          // kode teknik diperbaiki: a-f
            $table->boolean('shot_100')->default(false);     // Ya / Tdk
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['athlete_id', 'log_date']);
        });
    }

    public function down(): void
    {
        foreach (['shoot_logs', 'equipments', 'monthly_scores', 'load_tests', 'technique_tests',
                  'physical_tests', 'yoyo_references', 'anthropometries', 'athletes'] as $t) {
            Schema::dropIfExists($t);
        }
    }
};
