<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('detail_jawaban', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hasil_tryout_id')->constrained('hasil_tryout')->cascadeOnDelete();
            $table->foreignId('soal_id')->constrained('soal')->cascadeOnDelete();
            $table->enum('jawaban_siswa', ['a','b','c','d']);
            $table->boolean('benar_salah')->default(false);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('detail_jawaban'); }
};
