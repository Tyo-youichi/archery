<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Definisi metrik tes fisik & teknik + nilai benchmark (BM), dapat di-CRUD oleh admin.
 * Nilai tes atlet sendiri tersimpan di kolom JSON `results` pada physical_tests/technique_tests.
 * Tabel ini hanya definisi: label, grup, satuan, BM per gender, urutan, aktif/nonaktif.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('metrics', function (Blueprint $table) {
            $table->id();
            $table->enum('domain', ['physical', 'technique']);
            $table->string('group_key', 40);               // mis. endurance, scoring
            $table->string('group_label', 80);             // mis. Endurance, Scoring
            $table->string('key', 40);                     // slug metrik, mis. yoyo_vo2
            $table->string('label', 150);
            $table->string('unit', 30)->nullable();        // satuan tampilan, mis. "ml/kg/mnt"
            $table->string('step', 10)->default('0.01');   // step input number
            $table->decimal('bm_l', 10, 2)->nullable();    // benchmark putra (L)
            $table->decimal('bm_p', 10, 2)->nullable();    // benchmark putri (P); null = samakan dgn L
            $table->boolean('is_promoted')->default(false);// ditandai perlu kolom generated ter-index
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort')->default(0);
            $table->timestamps();

            $table->unique(['domain', 'key']);
            $table->index(['domain', 'is_active', 'sort']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('metrics');
    }
};
