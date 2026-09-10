<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tryout_soal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tryout_id')->constrained('tryout')->cascadeOnDelete();
            $table->foreignId('soal_id')->constrained('soal')->cascadeOnDelete();
            $table->unsignedInteger('urutan')->default(0);
            $table->timestamps();
            $table->unique(['tryout_id','soal_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('tryout_soal'); }
};
