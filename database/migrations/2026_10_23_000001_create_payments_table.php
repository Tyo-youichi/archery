<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Pembayaran iuran bulanan per atlet per periode (tahun+bulan).
 * Atlet mengunggah bukti; admin memverifikasi. Akses atlet dikunci sampai
 * ada pembayaran berstatus 'verified' untuk periode berjalan.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('athlete_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');                 // 1-12
            $table->decimal('amount', 12, 2)->nullable();         // jumlah yang dibayar (opsional)
            $table->string('proof')->nullable();                  // file bukti (.webp) di disk 'payments'
            $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->string('note', 500)->nullable();              // catatan atlet / alasan tolak
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->unique(['athlete_id', 'year', 'month']);
            $table->index(['year', 'month', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
