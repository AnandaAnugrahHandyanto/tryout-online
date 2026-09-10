<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('hasil_tryout', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tryout_id')->constrained('tryout')->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
            $table->decimal('nilai', 5, 2)->default(0);
            $table->unsignedInteger('jumlah_benar')->default(0);
            $table->unsignedInteger('jumlah_salah')->default(0);
            $table->unsignedInteger('waktu_pengerjaan_menit')->default(0);
            $table->dateTime('waktu_submit')->nullable();
            $table->timestamps();
            $table->unique(['tryout_id','siswa_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('hasil_tryout'); }
};
