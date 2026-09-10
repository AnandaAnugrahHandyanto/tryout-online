<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('soal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mapel_id')->constrained('mata_pelajaran')->cascadeOnDelete();
            $table->foreignId('guru_id')->constrained('guru')->cascadeOnDelete();
            $table->text('pertanyaan');
            $table->text('pilihan_a');
            $table->text('pilihan_b');
            $table->text('pilihan_c');
            $table->text('pilihan_d');
            $table->enum('jawaban_benar', ['a','b','c','d']);
            $table->enum('tingkat_kesulitan', ['mudah','sedang','sulit'])->default('sedang');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('soal'); }
};
